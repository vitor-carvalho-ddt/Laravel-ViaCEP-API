<?php

namespace App\Repositories;

use App\Models\Cep;
use App\Repositories\Interfaces\CepRepositoryInterface;

class CepRepository implements CepRepositoryInterface
{
    public function allQueryBuilder($userId)
    {
        return Cep::where('user_id', $userId);
    }
    public function all($userId)
    {
        return Cep::where('user_id', $userId)->get();
    }

    public function find($id, $userId)
    {
        return Cep::where('id', $id)->where('user_id', $userId)->first();
    }

    public function create(array $data)
    {
        return Cep::create($data);
    }

    public function update($id, array $data, $userId)
    {
        $cep = $this->find($id, $userId);
        if ($cep) {
            $cep->update($data);
            return $cep;
        }
        return null;
    }

    public function delete($id, $userId)
    {
        $cep = $this->find($id, $userId);
        if ($cep) {
            return $cep->delete();
        }
        return false;
    }
}