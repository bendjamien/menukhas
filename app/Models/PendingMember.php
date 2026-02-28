<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingMember extends Model
{
    protected $fillable = ['nama', 'metode', 'target', 'otp', 'expires_at'];
}
