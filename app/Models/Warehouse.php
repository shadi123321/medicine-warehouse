<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasFactory;

    public function medicine(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, "medicinewarehouse")
        ->withPivot('availableQuantity');
    }
}
