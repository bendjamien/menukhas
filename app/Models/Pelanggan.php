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
        'kode_member',
        'nama',
        'no_hp',
        'alamat',
        'email',
        'member_level',
        'poin',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pelanggan) {
            if (!$pelanggan->kode_member) {
                $lastMember = self::orderBy('id', 'desc')->first();
                $lastNumber = $lastMember ? (int) substr($lastMember->kode_member, 3) : 0;
                $pelanggan->kode_member = 'MBR' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

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