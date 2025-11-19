<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokLog extends Model
{
    use HasFactory;

    protected $table = 'stok_log';

    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'tanggal',
        'tipe',
        'jumlah',
        'sumber',
        'keterangan',
        'user_id',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}