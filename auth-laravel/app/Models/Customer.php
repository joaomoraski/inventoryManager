<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'typeIdentificationNumber',
        'identificationNumber', //cpf or cnpj
        'address',
        'addresNumber', // numero
        'telephone',
        'postalCode' // cep
    ];
}
