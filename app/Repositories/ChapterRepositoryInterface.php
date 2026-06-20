<?php

namespace App\Repositories;

interface ChapterRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function store(array $data);
    public function update($id, array $data);
    public function delete(int $id): bool;
}
