<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $fillable = ['path', 'disk', 'access_token', 'expires_at'];

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }
}
