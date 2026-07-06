<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'recipients_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
