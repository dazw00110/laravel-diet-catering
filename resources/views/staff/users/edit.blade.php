@extends('layouts.staff')

@section('title', 'Edytuj dane klienta')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-semibold mb-4">Edytuj dane klienta</h1>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('staff.users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PUT')

        @include('staff.users.form', ['user' => $user])

        <div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Zapisz zmiany</button>
        </div>
    </form>
</div>
@endsection
