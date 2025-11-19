<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
   
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
        'email',
        'member_level',
        'poin',
    ];
}