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

    {{-- DANE FIRMY --}}
    <div class="bg-gray-100 p-6 rounded-lg shadow mb-10 space-y-3 text-sm text-gray-700">
        <p><strong>📍 Adres:</strong> ul. Zdrowa 10, 00-123 Warszawa</p>
        <p><strong>🕒 Godziny pracy:</strong> Pon - Pt: 8:00 - 18:00</p>
        <p><strong>📞 Telefon:</strong> +48 123 456 789</p>
        <p><strong>📧 E-mail:</strong> kontakt@fitbox.pl</p>
        <p><strong>🧾 NIP:</strong> 123-456-78-90</p>
    </div>

    {{-- FORMULARZ --}}
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
