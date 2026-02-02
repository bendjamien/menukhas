<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Produk;
use App\Models\Setting;

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
        Schema::defaultStringLength(191);

        // Share Global Notifications to Layout
        View::composer('layouts.app', function ($view) {
            $globalNotifs = [];

            try {
                // 1. Cek Stok Menipis
                if (Schema::hasTable('produk') && Schema::hasTable('settings')) {
                    $batasStok = Setting::where('key', 'stok_minimum')->value('value') ?? 5;
                    $stokMenipis = Produk::where('stok', '<=', $batasStok)->count();

                    if ($stokMenipis > 0) {
                        $globalNotifs[] = [
                            'title' => 'Stok Menipis!',
                            'message' => "Ada {$stokMenipis} produk dengan stok rendah.",
                            'time' => 'Sekarang',
                            'type' => 'warning', // warning, info, danger
                            'link' => route('dashboard') // Arahkan ke dashboard untuk liat detail
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Silent fail jika tabel belum siap (saat migrasi awal)
            }

            $view->with('globalNotifs', $globalNotifs);
        });
    }
}