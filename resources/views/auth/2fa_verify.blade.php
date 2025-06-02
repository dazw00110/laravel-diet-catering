@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white shadow-xl rounded-2xl">
    <h2 class="text-2xl font-bold mb-4">Zweryfikuj kod TOTP</h2>
    <form method="POST" action="{{ url('/2fa/verify') }}" class="space-y-4">
        @csrf
        <div>
            <label for="code" class="block font-medium text-sm text-gray-700">Kod uwierzytelniający</label>
            <input type="text" name="code" id="code" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" required>
            @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Verify
        </button>
    </form>
</div>
@endsection
