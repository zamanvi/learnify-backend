<?php

namespace App\Repositories;

use App\Models\Lesson;

class LessonRepository  implements LessonRepositoryInterface
{
    protected $lesson;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function getAll()
    {
        return $this->lesson->paginate(10);
    }
    public function getAllById($id)
    {
        // chapter_id is already on the lesson row; neither the app nor the
        // admin view reads the nested chapter relation, so eager-loading it
        // just duplicates the same Chapter JSON object across every row.
        return $this->lesson->where('chapter_id', $id)->paginate(10);
    }

    public function findById($id)
    {
        return $this->lesson->findOrFail($id);
    }

    public function store(array $data)
    {
        return $this->lesson->create($data);
    }

    public function update($id, array $data)
    {
        return $this->findById($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }
}
