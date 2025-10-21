@extends('layouts.app')

@section('content')
<h2 style="margin-top:0">Create Link</h2>
@if(session('ok'))<div style="color:#34d399">{{ session('ok') }}</div>@endif
<form method="post" action="{{ route('links.store') }}" class="row">
    @csrf
    <div class="row cols-2">
        <div>
            <label>Custom Code (optional)</label>
            <input type="text" name="code" placeholder="e.g. promo2025">
        </div>
        <div>
            <label>Rotator?</label>
            <select name="is_rotator">
                <option value="0">No (Single Destination)</option>
                <option value="1">Yes (Multiple URLs)</option>
            </select>
        </div>
    </div>

    <div id="single">
        <label>Destination URL</label>
        <input type="url" name="destination" placeholder="https://example.com">
    </div>

    <div id="rotator" style="display:none">
        <label>Rotator Destinations</label>
        <div id="rows"></div>
        <button class="btn outline" type="button" onclick="addRow()">+ Add URL</button>
    </div>

    <div style="margin-top:1rem">
        <button class="btn" type="submit">Save</button>
    </div>
</form>
<script>
const sel = document.querySelector('select[name="is_rotator"]');
const single = document.getElementById('single');
const rotator = document.getElementById('rotator');
function sync(){ if(sel.value==='1'){ single.style.display='none'; rotator.style.display='block'; } else { single.style.display='block'; rotator.style.display='none'; }}
sel.addEventListener('change', sync); sync();
let rIndex = 0;
function addRow(){
    const c = document.createElement('div');
    c.className = 'row cols-2';
    c.innerHTML = `<div><input type="url" name="rotator[${rIndex}][url]" placeholder="https://..."/></div><div><input type="number" name="rotator[${rIndex}][weight]" value="1" min="1" max="100"/></div>`;
    document.getElementById('rows').appendChild(c);
    rIndex++;
}
</script>
@endsection