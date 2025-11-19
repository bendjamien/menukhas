<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    
    public $timestamps = false;

   
    protected $fillable = [
        'nama_produk',
        'kode_barcode',
        'kategori_id',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'deskripsi',
        'gambar_url',
        'status',
    ];

  
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}