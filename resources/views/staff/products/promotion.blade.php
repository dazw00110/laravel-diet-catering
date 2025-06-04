@extends('layouts.staff')

@section('title', 'Ustal tymczasową super promocję')

@section('content')
@php
    $hasActivePromotion = $product->promotion_price &&
                         $product->promotion_expires_at &&
                         $product->promotion_expires_at->isFuture();
    $currentPrice = $hasActivePromotion ? $product->promotion_price : $product->price;

    function formatTimeRemainingPolish($expiresAt) {
        if (!$expiresAt) return '';

        $now = now();
        $expires = \Carbon\Carbon::parse($expiresAt);

        if ($expires->isPast()) {
            return 'Promocja wygasła';
        }

        $diff = $now->diff($expires);

        if ($diff->days > 0) {
            $daysText = $diff->days == 1 ? 'dzień' : ($diff->days < 5 ? 'dni' : 'dni');
            $hoursText = $diff->h == 1 ? 'godzinę' : ($diff->h < 5 ? 'godziny' : 'godzin');
            return $diff->days . ' ' . $daysText . ' i ' . $diff->h . ' ' . $hoursText;
        } elseif ($diff->h > 0) {
            $hoursText = $diff->h == 1 ? 'godzinę' : ($diff->h < 5 ? 'godziny' : 'godzin');
            $minutesText = $diff->i == 1 ? 'minutę' : ($diff->i < 5 ? 'minuty' : 'minut');
            return $diff->h . ' ' . $hoursText . ' i ' . $diff->i . ' ' . $minutesText;
        } else {
            $minutesText = $diff->i == 1 ? 'minutę' : ($diff->i < 5 ? 'minuty' : 'minut');
            return $diff->i . ' ' . $minutesText;
        }
    }
@endphp

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <p class="text-gray-600">Promocja na: <strong>{{ $product->name }}</strong></p>
        <p class="text-sm text-gray-600">Aktualna cena: <strong>{{ number_format($product->price, 2) }} zł</strong></p>
    </div>

    @if($hasActivePromotion)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold">⚠️ Aktywna promocja</p>
                    <p class="text-sm">
                        Cena promocyjna: <strong>{{ number_format($product->promotion_price, 2) }} zł</strong><br>
                        Wygasa: <strong>{{ $product->promotion_expires_at->format('H:i d.m.Y') }}</strong>
                        (za {{ formatTimeRemainingPolish($product->promotion_expires_at) }})
                    </p>
                </div>
                <form method="POST" action="{{ route('staff.products.promotion.remove', $product) }}" class="ml-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"
                            onclick="return confirm('Czy na pewno chcesz usunąć aktywną promocję?')">
                        Usuń promocję
                    </button>
                </form>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('staff.products.promotion.store', $product) }}">
        @csrf

        <div class="mb-4">
            <label for="promotion_price" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $hasActivePromotion ? 'Nowa cena promocyjna (zł)' : 'Cena promocyjna (zł)' }} <span class="text-red-500">*</span>
            </label>
            <input type="number"
                   id="promotion_price"
                   name="promotion_price"
                   step="0.01"
                   min="0.01"
                   max="{{ $currentPrice - 0.01 }}"
                   value="{{ old('promotion_price') }}"
                   class="input input-bordered w-full @error('promotion_price') border-red-500 @enderror"
                   placeholder="{{ number_format($currentPrice * 0.8, 2) }}"
                   required>
            <p class="text-xs text-gray-500 mt-1">
                Cena musi być niższa niż {{ $hasActivePromotion ? 'aktualna cena promocyjna' : 'aktualna cena' }}
                ({{ number_format($currentPrice, 2) }} zł)
            </p>
            @error('promotion_price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="hours" class="block text-sm font-medium text-gray-700 mb-2">
                Czas trwania promocji (godziny) <span class="text-red-500">*</span>
            </label>
            <select id="hours"
                    name="hours"
                    class="input input-bordered w-full @error('hours') border-red-500 @enderror"
                    required>
                <option value="">Wybierz czas trwania</option>
                <option value="1" {{ old('hours') == '1' ? 'selected' : '' }}>1 godzina</option>
                <option value="2" {{ old('hours') == '2' ? 'selected' : '' }}>2 godziny</option>
                <option value="3" {{ old('hours') == '3' ? 'selected' : '' }}>3 godziny</option>
                <option value="4" {{ old('hours') == '4' ? 'selected' : '' }}>4 godziny</option>
                <option value="6" {{ old('hours') == '6' ? 'selected' : '' }}>6 godzin</option>
                <option value="8" {{ old('hours') == '8' ? 'selected' : '' }}>8 godzin</option>
                <option value="12" {{ old('hours') == '12' ? 'selected' : '' }}>12 godzin</option>
                <option value="24" {{ old('hours') == '24' ? 'selected' : '' }}>24 godziny</option>
                <option value="48" {{ old('hours') == '48' ? 'selected' : '' }}>48 godzin</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Maksymalny czas promocji: 48 godzin</p>
            @error('hours')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">💡 Super promocja last minute</h3>
            <p class="text-sm text-blue-700">
                @if($hasActivePromotion)
                    Edytujesz istniejącą promocję. Nowa promocja zastąpi aktualną i będzie obowiązywać przez wybrany czas.
                @else
                    To jest promocja krótkoterminowa, idealna do wyprzedaży produktów lub zwiększenia sprzedaży w określonych godzinach.
                @endif
                Promocja będzie automatycznie wyświetlana klientom i wygaśnie po upływie wybranego czasu.
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 font-medium">
                🔥 {{ $hasActivePromotion ? 'Edytuj promocję' : 'Ustaw super promocję' }}
            </button>
            <a href="{{ route('staff.products.index') }}"
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                Anuluj
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('promotion_price');
    const currentPrice = {{ $currentPrice }};
    const hasActivePromotion = {{ $hasActivePromotion ? 'true' : 'false' }};

    priceInput.addEventListener('input', function() {
        const promoPrice = parseFloat(this.value);
        if (promoPrice && promoPrice >= currentPrice) {
            const priceType = hasActivePromotion ? 'aktualnej ceny promocyjnej' : 'aktualnej ceny';
            this.setCustomValidity('Cena promocyjna musi być niższa niż ' + priceType + ' (' + currentPrice.toFixed(2) + ' zł)');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endsection
