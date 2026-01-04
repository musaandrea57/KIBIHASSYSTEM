<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Invoice::observe(\App\Observers\InvoiceObserver::class);
        \App\Models\Payment::observe(\App\Observers\PaymentObserver::class);

        // Share urgent announcements with the layout
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $urgentAnnouncements = collect();
            if (\Illuminate\Support\Facades\Auth::check()) {
                 try {
                    $urgentAnnouncements = \App\Models\Announcement::active()
                        ->where('priority', 'urgent')
                        ->latest()
                        ->get();
                 } catch (\Exception $e) {
                     // Fail silently if table doesn't exist yet
                 }
            }
            $view->with('urgentAnnouncements', $urgentAnnouncements);
        });
    }
}
