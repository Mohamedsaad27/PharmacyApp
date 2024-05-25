<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyController extends Controller
{
    use ApiResponseTrait;
    public function showDictionary(){
        try {
            $dictionaryItems = Product::with('drugs')
                ->where('pharmacy_id',Auth::id())
                ->get()
                ->makeHidden(['created_at','updated_at']);
            if ($dictionaryItems->isEmpty()){
                return $this->errorResponse('No items in dictionary',404);
            }
            return $this->successResponse(
                $dictionaryItems,
                'Items Retrieved Successfully',
                200
            );
        }catch (\Exception $exception)
        {
            return $this->errorResponse(['message'=>$exception->getMessage()],200);
        }
    }
}
