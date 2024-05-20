<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = [
        'address',
        'phone_number',
        'user_id',
    ];

    public  function user(){
        return $this->belongsTo(User::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

}
