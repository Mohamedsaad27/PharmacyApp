<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow , WithValidation
{
    use Importable , ApiResponseTrait;
    public function model(array $row)
    {
        $category = Category::where('name',$row['category_name'])->first();
        if(!$category){
            return $this->errorResponse('Invalid Category Name',404);
        }
        return new Product([
            'name' => $row['name'],
            'category_id' => $category->id,
            'description' => $row['description'],
            'price' => $row['price'],
            'quantity' => $row['quantity'],
            'image' => $row['image'] ?? 'default.jpg',
            'dosage' => $row['dosage'],
            'effective_material' => $row['effective_material'],
            'side_effects' => $row['side_effects'],
            'pharmacy_id' => Auth::id()
        ]);
    }
    public  function rules(): array
    {
        return [
            '*.category_name' => 'required|exists:categories,name',
            '*.name' => 'required|string',
            '*.description' => 'required|string',
            '*.price' => 'required|numeric',
            '*.quantity' => 'required|integer',
            '*.image' => 'nullable|string',
            '*.dosage' => 'required|string',
            '*.effective_material' => 'required|string',
            '*.side_effects' => 'required|string',
        ];
    }
}
