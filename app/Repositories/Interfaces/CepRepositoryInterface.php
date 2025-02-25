<?php

namespace App\Repositories\Interfaces;

interface CepRepositoryInterface
{
    public function allQueryBuilder($userId);

    public function all($userId);
    
    public function findByColumn($column, $value, $userId);

    public function find($id, $userId);
    
    public function create(array $data);
    
    public function update($id, array $data, $userId);
    
    public function delete($id, $userId);
}