<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $fillable = ['uuid', 'path', 'disk', 'expires_at'];

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
