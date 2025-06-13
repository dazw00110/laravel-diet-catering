<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\DiscountCode;
use Illuminate\Support\Str;

class LoyaltyProgramService
{
    public function evaluate(User $user): void
    {
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $totalSpent = $completedOrders->sum('total_price');
        $orderCount = $completedOrders->count();
        $accountAgeMonths = $user->created_at->diffInMonths(now());

        if ($totalSpent >= 15000 && !$this->hasPermanentDiscount($user, 10)) {
            $this->createPermanentDiscount($user, 10);
        } elseif ($totalSpent >= 10000 && !$this->hasPermanentDiscount($user, 5)) {
            $this->createPermanentDiscount($user, 5);
        }

        if ($orderCount >= 20 && !$this->hasDiscount($user, 100, false)) {
            $this->createSingleUseCode($user, 100, false);
        } elseif ($orderCount >= 10 && !$this->hasDiscount($user, 50, false)) {
            $this->createSingleUseCode($user, 50, false);
        }

        if ($this->hasFullProfile($user) && !$this->hasDiscount($user, 30, false)) {
            $this->createSingleUseCode($user, 30, false);
        }

        if ($accountAgeMonths >= 12 && !$this->hasPermanentDiscount($user, 5, 'seniority')) {
            $this->createPermanentDiscount($user, 5, 'seniority');
        }

        // -5% discount for users with 500 points
        $points = floor($totalSpent / 10);
        if ($points >= 500 && !$this->hasDiscount($user, 5, true, 'points')) {
            $this->createSingleUseCode($user, 5, true, 'points');
        }
    }

    private function hasPermanentDiscount(User $user, int $value, string $tag = null): bool
    {
        return DiscountCode::where('user_id', $user->id)
            ->where('value', $value)
            ->where('permanent', true)
            ->when($tag, fn($q) => $q->where('description', $tag))
            ->exists();
    }

    private function hasDiscount(User $user, int $value, bool $percentage = true, string $tag = null): bool
    {
        return DiscountCode::where('user_id', $user->id)
            ->where('value', $value)
            ->where('is_percentage', $percentage)
            ->when($tag, fn($q) => $q->where('description', $tag))
            ->exists();
    }

    private function createPermanentDiscount(User $user, int $value, string $desc = null): void
    {
        DiscountCode::create([
            'user_id' => $user->id,
            'code' => strtoupper(Str::random(10)),
            'value' => $value,
            'is_percentage' => true,
            'permanent' => true,
            'description' => $desc,
        ]);
    }

    private function createSingleUseCode(User $user, int $value, bool $percentage = true, string $desc = null): void
    {
        DiscountCode::create([
            'user_id' => $user->id,
            'code' => strtoupper(Str::random(10)),
            'value' => $value,
            'is_percentage' => $percentage,
            'permanent' => false,
            'expires_at' => now()->addDays(30),
            'description' => $desc,
        ]);
    }

    private function hasFullProfile(User $user): bool
    {
        return $user->is_verified;
    }
}

