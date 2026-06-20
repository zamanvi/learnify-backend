<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportReplay extends Model
{
    use HasFactory;
    protected $fillable = ['support_id', 'sender_id', 'receiver_id', 'message'];
    public function support()
    {
        return $this->belongsTo(Support::class, 'support_id');
    }
}
