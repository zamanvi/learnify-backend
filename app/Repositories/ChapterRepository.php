<?php

namespace App\Repositories;

use App\Models\Chapter;

class ChapterRepository  implements ChapterRepositoryInterface
{
    protected $chapter;

    public function __construct(Chapter $chapter)
    {
        $this->chapter = $chapter;
    }

    public function getAll()
    {
        return $this->chapter->paginate(10);
    }

    public function findById($id)
    {
        return $this->chapter->findOrFail($id);
    }

    public function store(array $data)
    {
        return $this->chapter->create($data);
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
