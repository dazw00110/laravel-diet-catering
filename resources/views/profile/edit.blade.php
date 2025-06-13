@extends($layout)

@section('title', 'Edytuj profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 space-y-10">
    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.update-password-form')
    @include('profile.partials.delete-user-form')
</div>
@endsection
