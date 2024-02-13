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

    protected $rules = [
        "name" => "filled",
        "email" => 'required|email|unique:managers',
        'typeIdentificationNumber' => "max:2",
        'identificationNumber' => "max:30", //cpf or cnpj
        "address" => "min:10|max:200",
        "addressNumber" => "max:10",
        "telephone" => "max:20",
        "postalCode" => "max:20",
    ];

    public function getRules(): array
    {
        return $this->rules;
    }


    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

}
