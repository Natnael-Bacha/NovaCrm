@extends('layouts.app')
@section('title', 'Dashboard · NovaTra')

@section('content')
    <div>
        <p>Dashboard</p>
            <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
    </div>

@endsection
