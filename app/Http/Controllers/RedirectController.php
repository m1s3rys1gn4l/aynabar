<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\ScanEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function handle(Request $request, string $slug): RedirectResponse
    {
        $code = Code::query()
            ->where('mode', 'DYNAMIC')
            ->where('dynamic_slug', $slug)
            ->firstOrFail();

        if (!$code->dynamic_target_url) {
            abort(404);
        }

        try {
            ScanEvent::create([
                'code_id' => $code->id,
                'scanned_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
            ]);
        } catch (\Throwable) {
            // Keep redirect behavior even if analytics write fails.
        }

        return redirect()->away($code->dynamic_target_url);
    }
}
