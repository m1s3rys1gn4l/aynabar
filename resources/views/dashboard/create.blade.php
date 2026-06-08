@extends('layouts.app')

@section('content')
    <h1 style="margin-top:12px;">Create Barcode / QR</h1>
    <p class="muted">Static mode stores fixed data. Dynamic mode stores a stable redirect URL whose destination can be changed later.</p>

    <div class="card" style="margin-top:12px;">
        <form action="{{ url('/dashboard/create') }}" method="post">
            @csrf
            <label>Label
                <input name="label" required value="{{ old('label') }}">
            </label>

            <div class="grid" style="grid-template-columns: 1fr 1fr;">
                <label>Type
                    <select name="kind">
                        <option value="QR">QR Code</option>
                        <option value="BARCODE">Barcode</option>
                    </select>
                </label>

                <label>Mode
                    <select name="mode">
                        <option value="STATIC">Static</option>
                        <option value="DYNAMIC">Dynamic</option>
                    </select>
                </label>
            </div>

            <label>Static payload
                <textarea name="static_payload" rows="3">{{ old('static_payload') }}</textarea>
            </label>

            <label>Dynamic destination URL
                <input name="dynamic_target_url" placeholder="https://local.test" value="{{ old('dynamic_target_url') }}">
            </label>

            <label>Barcode format
                <select name="barcode_format">
                    <option value="code128">Code 128</option>
                    <option value="ean13">EAN-13</option>
                    <option value="upca">UPC-A</option>
                    <option value="code39">Code 39</option>
                </select>
            </label>

            <button class="btn" style="margin-top:12px;" type="submit">Create</button>
        </form>
    </div>

    @if ($editable)
        <div class="card" style="margin-top:16px;">
            <h3 style="margin-top:0;">Edit Dynamic Destination</h3>
            <p class="muted">Encoded URL remains same while destination can be updated.</p>
            <p class="muted">Encoded URL: {{ rtrim(config('app.url'), '/') }}/r/{{ $editable->dynamic_slug }}</p>

            <form action="{{ url('/dashboard/dynamic-target') }}" method="post">
                @csrf
                <input type="hidden" name="code_id" value="{{ $editable->id }}">
                <label>New destination URL
                    <input name="dynamic_target_url" required value="{{ $editable->dynamic_target_url }}">
                </label>
                <button class="btn" style="margin-top:12px;" type="submit">Update destination</button>
            </form>

            @if ($editable->targetHistory->count())
                <h4>Recent destination changes</h4>
                <div class="grid">
                    @foreach ($editable->targetHistory as $entry)
                        <div class="card" style="padding:10px;">
                            <div class="muted">{{ $entry->previous_target_url ?? '(initial)' }} -> {{ $entry->new_target_url }}</div>
                            <div class="muted">{{ $entry->changed_at?->toDateTimeString() }} by {{ $entry->changedBy->email ?? 'system' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
@endsection
