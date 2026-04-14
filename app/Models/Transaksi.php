<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'kasir_id',
        'pelanggan_id',
        'total',
        'diskon',
        'pajak',
        'metode_bayar',
        'nominal_bayar',
        'kembalian',
        'status',
        'status_produksi',
        'waktu_mulai_produksi',
        'waktu_selesai_produksi',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'waktu_mulai_produksi' => 'datetime',
        'waktu_selesai_produksi' => 'datetime',
        'total' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transaksi) {
            // Jika status berubah ke 'selesai' dan status_produksi masih null atau default
            if ($transaksi->status === 'selesai' && (!$transaksi->status_produksi || $transaksi->status_produksi === 'pending')) {
                $transaksi->status_produksi = 'pending';
            }
        });
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }
    
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'transaksi_id');
    }
}