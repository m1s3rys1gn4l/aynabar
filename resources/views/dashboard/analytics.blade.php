@extends('layouts.app')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px; margin-top:12px;">
        <div>
            <h1 style="margin:0;">Code Analytics</h1>
            <p class="muted">{{ $code->label }} / {{ $code->kind }} / {{ $code->mode }} / {{ $code->owner->email }}</p>
        </div>
        <a href="{{ url('/dashboard') }}">Back</a>
    </div>

    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); margin-top:12px;">
        <div class="card"><div class="muted">Total scans</div><h2>{{ $totalScans }}</h2></div>
        <div class="card"><div class="muted">Unique IPs</div><h2>{{ $uniqueIps }}</h2></div>
        <div class="card"><div class="muted">Last scan</div><h2 style="font-size:17px;">{{ optional($code->scanEvents->first())->scanned_at?->toDateTimeString() ?? 'Never' }}</h2></div>
    </div>

    <div class="card" style="margin-top:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
            <h3 style="margin:0;">Daily scans</h3>
            <a class="btn" href="{{ url('/codes/'.$code->id.'/scans/export') }}">Download CSV</a>
        </div>

        <table style="margin-top:10px;">
            <thead><tr><th>Date</th><th>Scans</th></tr></thead>
            <tbody>
                @forelse ($dailyScans as $item)
                    <tr><td>{{ $item['day'] }}</td><td>{{ $item['count'] }}</td></tr>
                @empty
                    <tr><td colspan="2">No scans yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top:14px;">
        <h3 style="margin-top:0;">Top referrers</h3>
        <table>
            <thead><tr><th>Referrer</th><th>Scans</th></tr></thead>
            <tbody>
                @forelse ($topReferrers as $item)
                    <tr><td>{{ $item['referer'] }}</td><td>{{ $item['count'] }}</td></tr>
                @empty
                    <tr><td colspan="2">No referrer data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
