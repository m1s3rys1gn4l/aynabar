<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $codes = Code::query()
            ->with(['owner:id,name,email', 'scanEvents' => fn ($query) => $query->orderByDesc('scanned_at')->limit(1)])
            ->withCount('scanEvents')
            ->when(!$user->isSuperadmin(), fn ($query) => $query->where('owner_id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.index', [
            'codes' => $codes,
        ]);
    }
}
