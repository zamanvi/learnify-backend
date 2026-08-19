<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTypeHintConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
        'display_title',
        'column_1_hint',
        'column_2_hint',
        'column_3_hint',
        'column_4_hint',
        'description',
        'status',
    ];

    protected $casts = ['status' => 'bool'];

    /**
     * Get configuration by lesson type
     * Returns null if type not found or inactive
     */
    public static function getByType($type)
    {
        return self::where('type_name', $type)
            ->where('status', true)
            ->first();
    }

    /**
     * Get hints as array for API response
     */
    public function getHintsArray()
    {
        return [
            'column_1' => $this->column_1_hint,
            'column_2' => $this->column_2_hint,
            'column_3' => $this->column_3_hint,
            'column_4' => $this->column_4_hint,
        ];
    }
}
