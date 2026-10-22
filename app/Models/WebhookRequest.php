<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookRequest extends Model
{
    protected $fillable = [
        'method',
        'url',
        'headers',
        'body',
        'query_string',
        'content_type',
        'ip_address',
        'received_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'received_at' => 'datetime',
    ];
}
