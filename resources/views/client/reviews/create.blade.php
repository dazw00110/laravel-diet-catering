@extends('layouts.client')

@section('title', 'Wystaw opinię do zamówienia #' . $order->id)

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded space-y-8">
    <h1 class="text-2xl font-bold mb-6">📝 Wystaw opinię do zamówienia #{{ $order->id }}</h1>

    <form action="{{ route('client.orders.reviews.store', $order) }}" method="POST" class="space-y-6">
        @csrf

        @foreach($order->items as $index => $item)
        <div class="border rounded p-4 bg-gray-50 mb-4">
            <h2 class="font-semibold mb-2">{{ $item->product->name ?? 'Produkt nieznany' }}</h2>

            <input type="hidden" name="reviews[{{ $index }}][product_id]" value="{{ $item->product->id ?? '' }}">

            <label class="block mb-1 font-medium">Ocena:</label>
            <div class="flex space-x-1">
                @for ($i = 5; $i >= 1; $i--)
                    <input
                        type="radio"
                        id="rating-{{ $index }}-star-{{ $i }}"
                        name="reviews[{{ $index }}][rating]"
                        value="{{ $i }}"
                        class="sr-only peer"
                        {{ old('reviews.' . $index . '.rating') == $i ? 'checked' : '' }}
                        required
                    >
                    <label for="rating-{{ $index }}-star-{{ $i }}"
                        class="cursor-pointer text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition-colors"
                        title="{{ $i }} gwiazdek"
                    >
                        &#9733;
                    </label>
                @endfor
            </div>

            @error('reviews.' . $index . '.rating')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="comment-{{ $index }}" class="block mt-3 mb-1 font-medium">Komentarz (opcjonalny):</label>
            <textarea
                id="comment-{{ $index }}"
                name="reviews[{{ $index }}][comment]"
                rows="3"
                class="w-full rounded border border-gray-300 focus:ring-yellow-400 focus:border-yellow-400"
            >{{ old('reviews.' . $index . '.comment') }}</textarea>

            @error('reviews.' . $index . '.comment')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endforeach

        @error('reviews')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <button type="submit"
            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded font-semibold transition">
            Wyślij opinie
        </button>
    </form>
</div>
@endsection
