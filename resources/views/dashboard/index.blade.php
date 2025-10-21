@extends('layouts.app')

@section('content')
<div class="row cols-3">
    <div>
        <div class="muted">Visitors (30 days)</div>
        <div style="font-size:1.8rem;font-weight:700">{{ $perDay->sum('total') }}</div>
    </div>
    <div>
        <div class="muted">This year</div>
        <div style="font-size:1.8rem;font-weight:700">{{ $perYear->where('year', now()->format('Y'))->sum('total') }}</div>
    </div>
    <div>
        <a href="{{ route('links.create') }}" class="btn" style="margin-top:1.2rem">+ New Link</a>
    </div>
</div>
<hr style="border-color:#232323;margin:1rem 0">
<div class="row cols-2">
    <div>
        <h3>Per Day</h3>
        <table class="table">
            <thead><tr><th>Day</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($perDay as $d)
                <tr><td>{{ $d->day }}</td><td>{{ $d->total }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h3>Per Week</h3>
        <table class="table">
            <thead><tr><th>Week</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($perWeek as $w)
                <tr><td>{{ $w->week }}</td><td>{{ $w->total }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<hr style="border-color:#232323;margin:1rem 0">
<div class="row cols-3">
    <div>
        <h3>Devices</h3>
        <table class="table">
            <thead><tr><th>Device</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($byDevice as $r)
                <tr><td>{{ $r->device ?? 'Unknown' }}</td><td>{{ $r->total }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h3>Browsers</h3>
        <table class="table">
            <thead><tr><th>Browser</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($byBrowser as $r)
                <tr><td>{{ $r->browser ?? 'Unknown' }}</td><td>{{ $r->total }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h3>Countries</h3>
        <table class="table">
            <thead><tr><th>Country</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($byCountry as $r)
                <tr><td>{{ $r->country ?? '??' }}</td><td>{{ $r->total }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection