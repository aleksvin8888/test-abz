<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['token', 'is_used', 'expires_at'];

    public function isExpired() {
        return $this->expires_at->isPast();
    }
}
