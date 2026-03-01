<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanGaji extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_gajis';
    protected $fillable = ['user_id', 'gaji_pokok', 'nomor_rekening', 'bank'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
