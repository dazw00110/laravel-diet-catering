@extends($layout)

@section('title', 'Mój profil')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Dane konta</h2>

        <p><strong>Imię:</strong> {{ $user->first_name }}</p>
        <p><strong>Nazwisko:</strong> {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Rola:</strong> {{ $user->userType->name }}</p>

        @if ($user->is_vegetarian)
            <p>✅ Wegetarianin</p>
        @endif
        @if ($user->is_vegan)
            <p>✅ Weganin</p>
        @endif

        <a href="{{ route('profile.edit') }}"
           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edytuj profil
        </a>
    </div>
@endsection
