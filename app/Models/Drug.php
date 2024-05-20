<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'effective_material',
        'description',
        'side_effects',
        'dosage',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_drugs');
    }
}
