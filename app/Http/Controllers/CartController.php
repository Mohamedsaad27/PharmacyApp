<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    public  function addProductToCart(){
        try {

        }catch (\Exception $exception)
        {
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }

    public  function showProductOnCart(){

    }

    public  function deleteProductFromCart(){

    }
}
