<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestQuestion extends Model
{
    use HasFactory;
    protected $fillable = ['contest_id', 'name', 'option1', 'option2', 'option3', 'option4', 'option5'];


    /**
     * Get the contest that owns the ContestQuestion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contest()
    {
        return $this->belongsTo(Contest::class, 'contest_id');
    }

    public static function createStore($request): bool
    {
        $newEntry = self::create([
            'contest_id' => $request->contest_id,
            'name' => $request->name,
            'option1' => $request->option1,
            'option2' => $request->option2,
            'option3' => $request->option3,
            'option4' => $request->option4,
            'option5' => $request->option5,
        ]);
        return $newEntry instanceof self;
    }

    public static function updateStore($request, $id): bool
    {
        $updateEntry = self::where('id', $id)->update([
            'name' => $request->name,
            'option1' => $request->option1,
            'option2' => $request->option2,
            'option3' => $request->option3,
            'option4' => $request->option4,
            'option5' => $request->option5,
        ]);
        return $updateEntry;
    }


}
