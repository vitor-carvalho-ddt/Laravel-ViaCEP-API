<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cep extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cep',
        'logradouro',
        'complemento',
        'unidade',
        'bairro',
        'localidade',
        'uf',
        'estado',
        'regiao',
        'ibge',
        'gia',
        'ddd',
        'siafi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

