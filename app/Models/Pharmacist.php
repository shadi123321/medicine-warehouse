<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Pharmacist extends Model
{
    use HasFactory, HasApiTokens;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'address'
    ];
    protected $hidden = [
        'phone',
        'password'
    ];

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
