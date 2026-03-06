<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'bulan', 
        'tahun', 
        'gaji_pokok', 
        'lembur', 
        'potongan_kasbon', 
        'total_diterima', 
        'status_bayar', 
        'tanggal_bayar', 
        'metode_bayar', 
        'order_id', 
        'snap_token'
    ];

    protected $casts = ['tanggal_bayar' => 'datetime'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
