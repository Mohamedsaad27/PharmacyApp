<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    use ApiResponseTrait;

    public function getFavoriteProducts(){
        try {
            $favProducts = Favorite::with('product')
                ->where('patient_id',Auth::id())
                ->get();
            if($favProducts->isEmpty()){
                return $this->errorResponse('No Favorite Products For This User',404);
            }
            return $this->successResponse([
                $favProducts,
                'Favorite Products Retrieved Successfully'
            ],200);
        }catch (\Exception $exception)
        {
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }

    public function storeFavoriteProducts(Request $request){
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);
            $patientId = Auth::id();
            $productId = $request->product_id;
            $existingFavorite = Favorite::where('patient_id', $patientId)
                ->where('product_id', $productId)
                ->exists();
            if ($existingFavorite) {
                return $this->errorResponse('Product already added to favorites', 422);
            }
            $favProduct =  Favorite::create([
                'patient_id' => $patientId,
                'product_id' => $productId,
            ]);
            return $this->successResponse($favProduct,'Product added to favorites successfully', 201);
        }catch (\Exception $exception)
        {
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }

    public function deleteFavoriteProducts($id){
        try {
            $favProduct = Favorite::find($id);
            if(!$favProduct){
                return $this->errorResponse('Product Not Found',404);
            }
            $favProduct->delete();
            return $this->successResponse(null,'Product Deleted Successfully',200);
        }catch (\Exception $exception)
        {
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }

}
