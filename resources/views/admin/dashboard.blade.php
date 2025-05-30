@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>
    <p>Witaj, {{ Auth::user()->first_name }}!</p>
@endsection
