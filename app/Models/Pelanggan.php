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

    public function recalculateLevel()
    {
        if ($this->poin >= 5000) {
            $this->member_level = 'Gold';
        } elseif ($this->poin >= 1000) {
            $this->member_level = 'Silver';
        } else {
            $this->member_level = 'Member'; // atau Bronze
        }
        
        $this->save();
    }
}