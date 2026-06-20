<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ScholarShipResult extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'scholar_ship_id', 'order_by', 'is_winner'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
