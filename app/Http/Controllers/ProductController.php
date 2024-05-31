<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponseTrait;
    public function index(){
        try {
            $products = Product::all()->sortByDesc('price')->take(20);
            if ($products->isEmpty()){
                return $this->errorResponse('No Products Found',404);
            }
            return $this->successResponse($products,'Categories Retrieved Successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),500);
        }
    }
    public function productsByCategoryId($category_id){
        try {
            $products = Product::where('category_id',$category_id)->get();
            if ($products->isEmpty()){
                return $this->errorResponse('No Products For This Category',404);
            }
            return $this->successResponse($products,'Products Retrieved Successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),500);
        }
    }
    public function getProductById($id){
        try {
            $product = Product::find($id);
            if (!$product){
                return $this->errorResponse('Product Not Found',404);
            }
            return $this->successResponse($product,'Products Retrieved Successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),500);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'quantity' => ['required', 'integer', 'min:0'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'effective_material' => ['required', 'string', 'max:255'],
                'side_effects' =>['required', 'string', 'max:255'] ,
                'dosage' =>['required', 'string', 'max:255'] ,
                'category_id' => ['required', 'integer', 'exists:categories,id'],
                'pharmacy_id' => ['integer', 'exists:users,id']
            ];
            $messages = [
                'name.required' => 'The product name is required.',
                'name.string' => 'The product name must be a string.',
                'name.max' => 'The product name may not be greater than 255 characters.',
                'description.required' => 'The product description is required.',
                'description.string' => 'The product description must be a string.',
                'price.required' => 'The product price is required.',
                'price.numeric' => 'The product price must be a number.',
                'price.min' => 'The product price must be at least 0.',
                'quantity.required' => 'The product quantity is required.',
                'quantity.integer' => 'The product quantity must be an integer.',
                'quantity.min' => 'The product quantity must be at least 0.',
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
                'image.max' => 'The image may not be greater than 2048 kilobytes.',
                'effective_material.required' => 'The product name is required.',
                'effective_material.string' => 'The product name must be a string.',
                'effective_material.max' => 'The product name may not be greater than 255 characters.',
                'side_effects.required' => 'The product name is required.',
                'side_effects.string' => 'The product name must be a string.',
                'side_effects.max' => 'The product name may not be greater than 255 characters.',
                'dosage.required' => 'The product name is required.',
                'dosage.string' => 'The product name must be a string.',
                'dosage.max' => 'The product name may not be greater than 255 characters.',
                'category_id.required' => 'The category ID is required.',
                'category_id.integer' => 'The category ID must be an integer.',
                'category_id.exists' => 'The selected category ID is invalid.',
                'pharmacy_id.integer' => 'The pharmacy ID must be an integer.',
                'pharmacy_id.exists' => 'The selected pharmacy ID is invalid.'
            ];
            $validateData = Validator::make($request->all(), $rules, $messages);
            if($validateData->fails()) {
                return $this->errorResponse($validateData->errors(), 422);
            }
            $imageUrl = 'default.jpg';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('ProductImages', $imageName);
                $imageUrl = Storage::url($imagePath);
            }
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'image' => $imageUrl,
                'effective_material' => $request->effective_material ,
                'side_effects' => $request->side_effects ,
                'dosage' => $request->dosage,
                'category_id' => $request->category_id,
                'pharmacy_id' => Auth::id(),
            ]);
            return $this->successResponse(
                $product,
                'Product Created Successfully',
                201
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
    public function edit(Request $request,$id){
        try {
            $product = Product::find($id);
            if(!$product){
                return $this->errorResponse('Product Not Found',404);
            }
            $validateData = Validator::make($request->all(), [
                'name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'price' => ['nullable', 'numeric', 'min:0'],
                'quantity' => ['nullable', 'integer', 'min:0'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'effective_material' => ['nullable', 'string', 'max:255'],
                'side_effects' => ['nullable', 'string', 'max:255'],
                'dosage' => ['nullable', 'string', 'max:255'],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
                'pharmacy_id' => ['integer', 'exists:users,id']
            ]);
            if ($validateData->fails()) {
                return $this->errorResponse($validateData->errors(), 422);
            }
            $data = [
                'name' => $request->name ? $request->name : $product->name,
                'description' => $request->description ? $request->description : $product->description,
                'price'=>$request->price ? $request->price : $product->price,
                'quantity'=>$request->quantity ? $request->quantity : $product->quantity,
                'image'=>$request->image ? $request->image : $product->image,
                'effective_material'=>$request->effective_material ? $request->effective_material : $product->effective_material,
                'side_effects'=>$request->side_effects ? $request->side_effects : $product->side_effects,
                'dosage'=>$request->dosage ? $request->dosage : $product->dosage,
                'category_id'=>$request->category_id ? $request->category_id : $product->category_id
            ];
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName);
                $data['image'] = $imageName;
            }
            $product->update($data);
            return $this->successResponse(
                $product,
                'Product Updated Successfully',
                200
            );

        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
    public function delete($id){
        try {
            $product = Product::find($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            $deletedProduct = $product->delete();
            if (!$deletedProduct) {
                throw new \Exception('Failed to delete Product');
            }
            return $this->successResponse(null, 'Product deleted successfully', 200);
        }catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage(),500);
        }
    }
}
