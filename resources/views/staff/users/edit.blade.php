@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-2xl font-semibold mb-6">Edytuj dane klienta</h2>

    <form method="POST" action="{{ route('staff.users.update', $user->id) }}">
        @csrf
        @method('PUT')
        @include('staff.users.form', ['user' => $user])
    </form>
</div>
@endsection
