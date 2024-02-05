<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manager extends Model
{
    use HasFactory, UUID;

    protected $fillable = [
        'name',
        'email',
        'typeIdentificationNumber',
        'identificationNumber', //cpf or cnpj
        'address',
        'addressNumber', // numero
        'telephone',
        'postalCode' // cep
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

}
