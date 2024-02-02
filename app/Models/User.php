<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, UUID, HasRoles;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'manager_id', // Id usado para identificar o dono geral da empresa.
        'cpf', 'rg',
        'address',
        'addressNumber',
        'telephone',
        'postalCode',
        'salary',
    ];

    protected $updateRules = [
        "name" => "filled",
        "password" => "filled|confirmed",
        "email" => 'required|email|unique:users',
        "cpf" => "max:20",
        "rg" => "max:20",
        "address" => "min:10|max:200",
        "addressNumber" => "max:10",
        "telephone" => "max:20",
        "postalCode" => "max:20",
        "salary" => "numeric",
        "role" => "filled"
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }

    public function getUpdateRules($id)
    {
        $rules = $this->updateRules;
        $rules['email'] = $rules['email'] . ',email,' . $id;
        return $rules;
    }
}
