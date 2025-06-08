@extends('layouts.client')

@section('title', 'Kontakt')

@section('content')
<div class="max-w-2xl mx-auto py-16 px-4"
     x-data="{
         sent: false,
         handleSubmit() {
             this.sent = true;
             setTimeout(() => {
                 window.location.href = '{{ route('client.dashboard') }}';
             }, 3000);
         }
     }">

    <h1 class="text-3xl font-bold mb-6">Skontaktuj się z nami</h1>

    <template x-if="!sent">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div>
                <label class="block text-sm font-medium">Imię i nazwisko</label>
                <input type="text" class="input input-bordered w-full mt-1" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Adres e-mail</label>
                <input type="email" class="input input-bordered w-full mt-1" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Wiadomość</label>
                <textarea rows="5" class="textarea textarea-bordered w-full mt-1" required></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Wyślij wiadomość
            </button>
        </form>
    </template>

    <template x-if="sent">
        <div class="bg-green-100 text-green-800 px-4 py-4 rounded text-center text-lg mt-6">
            ✅ Wiadomość została wysłana. Niedługo się skontaktujemy.<br>
            Za chwilę nastąpi przekierowanie na stronę główną...
        </div>
    </template>
</div>
@endsection
