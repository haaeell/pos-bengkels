<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'quantity',
        'price',
        'satuan',
        'image',
        'description',
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'product_transactions')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

}
