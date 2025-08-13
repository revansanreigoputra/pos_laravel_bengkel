<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',            // purchase, sale, stock_alert
        'notifiable_type', // App\Models\User
        'notifiable_id',
        'message',
        'read_at',
        'data',            // biarkan null / tidak dipakai di UI
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data'    => 'array',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }
};