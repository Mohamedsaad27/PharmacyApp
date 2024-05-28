<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'image',
        'effective_material',
        'side_effects',
        'dosage',
        'category_id',
        'pharmacy_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'user_id', 'id');
    }


    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
