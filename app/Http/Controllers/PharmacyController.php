<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

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
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }
    public function searchOnDictionary(Request $request){
        try {
            $request->validate([
                'query' => 'nullable|string|max:255',
            ]);
            $query = $request->query('query');
            $pharmacy = auth()->user();
            $matchRecord = Product::where('name', 'LIKE', "%{$query}%")
                ->where('pharmacy_id',$pharmacy->id)
                ->get()
                ->makeHidden(['created_at','updated_at']);

            if($matchRecord->isEmpty()){
                return $this->errorResponse('NO match record',404);
            }
            return $this->successResponse(
                $matchRecord ,
                null,
                200
            );
        }catch (\Exception $exception){
            return $this->errorResponse(['message'=>$exception->getMessage()],500);
        }
    }
    public function uploadExcelSheet(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'dictionary' => ['required', 'file', 'mimes:xls,xlsx']
            ], [
                'dictionary.required' => 'The dictionary field is required.',
                'dictionary.file' => 'The file must be a valid file.',
                'dictionary.mimes' => 'The file must be a file of type: xls, xlsx.',
            ]);
            if ($validateData->fails()) {
                return $this->errorResponse(['errors' => $validateData->errors()], 422);
            }
            try {
                Excel::import(new ProductsImport, $request->file('dictionary'));
                return $this->successResponse(null, 'Products imported successfully', 200);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $excelExceptions) {
                $failures = $excelExceptions->failures();
                return response()->json(['success' => false, 'message' => 'Some rows failed to import', 'failures' => $failures], 400);
            }
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }

}
