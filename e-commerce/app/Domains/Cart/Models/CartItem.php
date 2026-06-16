<?php

namespace App\Domains\Cart\Models;

use App\Domains\Catalog\Models\Product;
use App\Models\User;
use Database\Factories\Domains\Cart\Models\CartItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /** @use HasFactory<CartItemFactory> */
    use HasFactory;

    protected static function newFactory(): CartItemFactory
    {
        return CartItemFactory::new();
    }

    protected $fillable = ['user_id', 'product_id', 'quantity'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
