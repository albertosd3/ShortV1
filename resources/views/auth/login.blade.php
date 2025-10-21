@extends('layouts.app')

@section('content')
<h2 style="margin-top:0">Masuk</h2>
<p class="muted">Simple, dark, classy.</p>
@if($errors->any())
    <div style="color:#fca5a5; margin:.5rem 0;">{{ $errors->first() }}</div>
@endif
<form method="post" action="{{ route('login.post') }}" class="row" style="margin-top:1rem; gap:1rem; max-width:400px">
    @csrf
    <div>
        <label>Password</label>
        <input type="password" name="password" placeholder="Password" autofocus>
    </div>
    <div>
        <button class="btn" type="submit">Login</button>
    </div>
</form>
@endsection