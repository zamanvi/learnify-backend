<?php

namespace App\Repositories;

interface ChapterRepositoryInterface
{
    public function getAll(?string $type = null);
    public function findById($id);
    public function store(array $data);
    public function update($id, array $data);
    public function delete(int $id): bool;
}
