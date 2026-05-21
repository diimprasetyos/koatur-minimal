<?php

namespace App\Filament\Widgets;

use App\Models\Subscription\Subscription;
use Filament\Widgets\Widget;

class SubscriptionBannerWidget extends Widget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $subscription = auth()->user()
            ->subscriptions()
            ->latest()
            ->first();

        if (! $subscription) {
            return true;
        }

        return $subscription->isTrial() || $subscription->isExpired();
    }

    public function getSubscription(): ?Subscription
    {
        return auth()->user()
            ->subscriptions()
            ->with('plan')
            ->latest()
            ->first();
    }

    public function getDaysRemaining(): int
    {
        return $this->getSubscription()?->daysRemaining() ?? 0;
    }

    public function isExpired(): bool
    {
        return $this->getSubscription()?->isExpired() ?? false;
    }

    public function getPlanName(): string
    {
        return $this->getSubscription()?->plan?->name ?? 'Trial';
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.subscription-banner-widget', [
            'subscription'  => $this->getSubscription(),
            'daysRemaining' => $this->getDaysRemaining(),
            'isExpired'     => $this->isExpired(),
            'planName'      => $this->getPlanName(),
        ]);
    }
}
