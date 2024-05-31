<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Pharmacy;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        try {
           $categories = Category::where('pharmacy_id',Auth::id())->get();
           if ($categories->isEmpty()){
               return $this->errorResponse('No Categories For This Pharmacy',404);
           }
            return $this->successResponse($categories,'Categories Retrieved Successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),500);
        }
    }
    public function getCategoriesByPharmacyId($pharmacy_id){
        try {
            $pharmacy = User::where('id', $pharmacy_id)
                ->where('role', 'pharmacy')
                ->first();
            if (!$pharmacy) {
                return $this->errorResponse('Pharmacy Not Found', 404);
            }
            $categories = Category::where('pharmacy_id',$pharmacy_id)->get();
            if ($categories->isEmpty()){
                return $this->errorResponse('No Categories For This Pharmacy',404);
            }
            return $this->successResponse(
                $categories,
                'Categories Retrieved Successfully',
                200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(),[
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'pharmacy_id' => 'exists:users,id',
            ]);
            if($validateData->fails()){
                return $this->errorResponse($validateData->errors(), 422);
            }
            $imageUrl = 'default.jpg';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath =  $image->storeAs('CategoriesImages', $imageName);
                $imageUrl = Storage::url($imagePath);

            }
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageUrl,
            'pharmacy_id' => Auth::id()
        ]);
        return  $this->successResponse(
            $category,
            'Category Created Successfully',
            201
        );
        }
        catch (\Exception $exception){
            return $this->errorResponse(['message'=>$exception->getMessage()],200);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            if(!$category){
                return $this->errorResponse('Category Not Found',404);
            }
            $validateData = Validator::make($request->all(), [
                'name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);
            if ($validateData->fails()) {
                return $this->errorResponse($validateData->errors(), 422);
            }

            $data = [
                'name' => $request->name ? $request->name : $category->name,
                'description' => $request->description ? $request->description : $category->description
            ];
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('categories', $imageName);
                $data['image'] = $imageName;
            }
            $category->update($data);
            return $this->successResponse(
                $category,
                'Category Updated Successfully',
                200
            );

        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
    public function delete(Request $request , $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return $this->errorResponse('Category not found', 404);
            }
            $deleted = $category->delete();
            if (!$deleted) {
                throw new \Exception('Failed to delete category');
            }
            return $this->successResponse(null, 'Category deleted successfully', 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 500);
        }
    }
}
