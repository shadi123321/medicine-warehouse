<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Medicine extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    public function order(): BelongsToMany
    {
        return $this->belongsToMany(Order::class,"order_items");
    }
    public function warehouse(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class,"medicinewarehouse")
        ->withPivot('availableQuantity');
    }
}

