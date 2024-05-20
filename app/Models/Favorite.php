<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'product_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
