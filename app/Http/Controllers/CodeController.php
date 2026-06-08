<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\DynamicTargetHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CodeController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();
        $editableId = $request->query('edit');

        $editable = null;

        if ($editableId) {
            $editable = Code::query()
                ->with(['targetHistory' => fn ($query) => $query->with('changedBy:id,name,email')->orderByDesc('changed_at')->limit(10)])
                ->find($editableId);

            if ($editable && !$this->canAccessCode($user, $editable)) {
                $editable = null;
            }
        }

        return view('dashboard.create', [
            'editable' => $editable,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'kind' => ['required', Rule::in(['QR', 'BARCODE'])],
            'mode' => ['required', Rule::in(['STATIC', 'DYNAMIC'])],
            'static_payload' => ['nullable', 'string'],
            'dynamic_target_url' => ['nullable', 'url'],
            'barcode_format' => ['nullable', Rule::in(['code128', 'ean13', 'upca', 'code39'])],
        ]);

        if ($data['mode'] === 'STATIC' && empty($data['static_payload'])) {
            return back()->withErrors(['static_payload' => 'Static payload is required for static mode.'])->withInput();
        }

        if ($data['mode'] === 'DYNAMIC' && empty($data['dynamic_target_url'])) {
            return back()->withErrors(['dynamic_target_url' => 'Destination URL is required for dynamic mode.'])->withInput();
        }

        DB::transaction(function () use ($request, $data): void {
            $code = Code::create([
                'owner_id' => $request->user()->id,
                'label' => $data['label'],
                'kind' => $data['kind'],
                'mode' => $data['mode'],
                'barcode_format' => $data['kind'] === 'BARCODE' ? ($data['barcode_format'] ?? 'code128') : null,
                'static_payload' => $data['mode'] === 'STATIC' ? $data['static_payload'] : null,
                'dynamic_slug' => $data['mode'] === 'DYNAMIC' ? $this->generateDynamicSlug() : null,
                'dynamic_target_url' => $data['mode'] === 'DYNAMIC' ? $data['dynamic_target_url'] : null,
            ]);

            if ($code->mode === 'DYNAMIC') {
                DynamicTargetHistory::create([
                    'code_id' => $code->id,
                    'previous_target_url' => null,
                    'new_target_url' => (string) $code->dynamic_target_url,
                    'changed_by_user_id' => $request->user()->id,
                    'changed_at' => now(),
                ]);
            }
        });

        return redirect('/dashboard');
    }

    public function updateDynamicTarget(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code_id' => ['required', 'integer', 'exists:codes,id'],
            'dynamic_target_url' => ['required', 'url'],
        ]);

        $code = Code::query()->findOrFail($data['code_id']);

        if (!$this->canAccessCode($request->user(), $code) || $code->mode !== 'DYNAMIC') {
            abort(403);
        }

        if ($code->dynamic_target_url !== $data['dynamic_target_url']) {
            DB::transaction(function () use ($request, $code, $data): void {
                DynamicTargetHistory::create([
                    'code_id' => $code->id,
                    'previous_target_url' => $code->dynamic_target_url,
                    'new_target_url' => $data['dynamic_target_url'],
                    'changed_by_user_id' => $request->user()->id,
                    'changed_at' => now(),
                ]);

                $code->update([
                    'dynamic_target_url' => $data['dynamic_target_url'],
                ]);
            });
        }

        return redirect('/dashboard/create?edit='.$code->id);
    }

    public function image(Request $request, Code $code): Response
    {
        if (!$this->canAccessCode($request->user(), $code)) {
            abort(403);
        }

        $payload = $this->encodedPayload($code);

        if ($code->kind === 'QR') {
            $png = QrCode::format('png')->size(420)->margin(1)->generate($payload);

            return response($png, 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=300',
            ]);
        }

        $generator = new BarcodeGeneratorPNG();

        $type = match ($code->barcode_format) {
            'ean13' => BarcodeGeneratorPNG::TYPE_EAN_13,
            'upca' => BarcodeGeneratorPNG::TYPE_UPC_A,
            'code39' => BarcodeGeneratorPNG::TYPE_CODE_39,
            default => BarcodeGeneratorPNG::TYPE_CODE_128,
        };

        $barcode = $generator->getBarcode($payload, $type, 2, 80);

        return response($barcode, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    public function analytics(Request $request, Code $code): View
    {
        if (!$this->canAccessCode($request->user(), $code)) {
            abort(403);
        }

        $code->load(['owner:id,name,email', 'scanEvents' => fn ($query) => $query->orderByDesc('scanned_at')->limit(500)]);

        $totalScans = $code->scanEvents->count();
        $uniqueIps = $code->scanEvents->pluck('ip_address')->filter()->unique()->count();

        $dailyScans = $code->scanEvents
            ->groupBy(fn ($scan) => $scan->scanned_at->format('Y-m-d'))
            ->map(fn ($items, $day) => ['day' => $day, 'count' => $items->count()])
            ->values()
            ->sortBy('day')
            ->values();

        $topReferrers = $code->scanEvents
            ->groupBy(fn ($scan) => trim((string) $scan->referer) ?: 'direct/unknown')
            ->map(fn ($items, $ref) => ['referer' => $ref, 'count' => $items->count()])
            ->sortByDesc('count')
            ->take(10)
            ->values();

        return view('dashboard.analytics', [
            'code' => $code,
            'totalScans' => $totalScans,
            'uniqueIps' => $uniqueIps,
            'dailyScans' => $dailyScans,
            'topReferrers' => $topReferrers,
        ]);
    }

    public function exportScansCsv(Request $request, Code $code): Response
    {
        if (!$this->canAccessCode($request->user(), $code)) {
            abort(403);
        }

        $scans = $code->scanEvents()->orderByDesc('scanned_at')->get(['scanned_at', 'ip_address', 'user_agent', 'referer']);

        $escape = fn (?string $value): string => '"'.str_replace('"', '""', (string) $value).'"';

        $rows = $scans->map(fn ($scan) => implode(',', [
            $escape($scan->scanned_at?->toIso8601String()),
            $escape($scan->ip_address),
            $escape($scan->user_agent),
            $escape($scan->referer),
        ]));

        $csv = collect(['scannedAt,ipAddress,userAgent,referer'])->concat($rows)->implode("\n");

        $safeLabel = preg_replace('/[^a-zA-Z0-9-_]/', '_', $code->label) ?: 'code';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$safeLabel.'-scans.csv"',
            'Cache-Control' => 'no-store',
        ]);
    }

    private function canAccessCode($user, Code $code): bool
    {
        return $user->isSuperadmin() || $code->owner_id === $user->id;
    }

    private function generateDynamicSlug(): string
    {
        do {
            $slug = 'c'.Str::lower(Str::random(8));
        } while (Code::query()->where('dynamic_slug', $slug)->exists());

        return $slug;
    }

    private function encodedPayload(Code $code): string
    {
        if ($code->mode === 'DYNAMIC') {
            $base = rtrim(config('app.url'), '/');
            return $base.'/r/'.$code->dynamic_slug;
        }

        return (string) ($code->static_payload ?? '');
    }
}
