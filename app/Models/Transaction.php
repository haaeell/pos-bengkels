<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_number', 'transaction_date', 'total_amount', 'status', 'payment_method', 'cashier_id',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_transactions')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
    
}
