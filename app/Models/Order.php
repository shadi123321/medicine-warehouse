<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'status'
    ];


    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(Pharmacist::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function medicine(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, "order_items")
            ->withPivot('quantity');
    }
}
