<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiptoTransaction extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'type', 'source',
        'description', 'balance_after', 'related_user_id',
    ];

    protected $casts = [
        'amount'        => 'integer',
        'balance_after' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
