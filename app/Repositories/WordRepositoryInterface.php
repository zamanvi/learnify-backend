<?php

namespace App\Repositories;

interface WordRepositoryInterface
{
    public function getAll();
    public function getAllById($id);
    public function findById($id);
    public function store(array $data);
    public function update($id, array $data);
    public function delete(int $id): bool;
}
