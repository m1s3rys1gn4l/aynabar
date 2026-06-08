@extends('layouts.app')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px; margin-top:12px;">
        <h1 style="margin:0;">Code Dashboard</h1>
        <a class="btn" href="{{ url('/dashboard/create') }}">Create New Code</a>
    </div>

    <div class="grid cols-3" style="margin-top:14px;">
        @forelse ($codes as $code)
            <article class="card">
                <h3 style="margin:0;">{{ $code->label }}</h3>
                <p class="muted" style="margin:6px 0;">{{ $code->kind }} / {{ $code->mode }} / {{ $code->owner->email }}</p>
                <img src="{{ url('/codes/'.$code->id.'/image') }}" alt="{{ $code->label }}" style="width:100%; height:170px; object-fit:contain; border:1px solid #e7e5e4; border-radius:8px;">
                <p class="muted" style="margin:8px 0 4px;">Scans: {{ $code->scan_events_count }}</p>
                <p class="muted" style="margin:0 0 8px;">Last scan: {{ optional($code->scanEvents->first())->scanned_at?->toDateTimeString() ?? 'Never' }}</p>
                <a href="{{ url('/dashboard/code/'.$code->id) }}">View Analytics</a>
                @if ($code->mode === 'DYNAMIC')
                    <div style="margin-top:6px;"><a href="{{ url('/dashboard/create?edit='.$code->id) }}">Edit Dynamic Destination</a></div>
                @endif
            </article>
        @empty
            <div class="card">No codes yet. Create your first code.</div>
        @endforelse
    </div>
@endsection
