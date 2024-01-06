<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        $data = TransactionCategory::all();
        return ResponseFormatter::success($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25|min:3|unique:\App\Models\TransactionCategory,name',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->all());
        }
        
        try {
            $data = TransactionCategory::create([
                'name' => ucfirst($request->name),
            ]);

            return ResponseFormatter::success($data, 201, 'Category successfully saved');
        } catch (\Exception $errors) {
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }

    public function show($id)
    {
        $data = TransactionCategory::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }
        
        return ResponseFormatter::success($data);
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25|min:3|unique:\App\Models\TransactionCategory,name,'.$id.',id',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->all());
        }

        $data = TransactionCategory::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }
        
        try {
            $data->update([
                'name' => ucfirst($request->name),
            ]);

            return ResponseFormatter::success(null, 200, 'Category successfully updated');
        } catch (\Exception $errors) {
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }

    public function destroy($id)
    {
        $data = TransactionCategory::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }

        try {
            $data->delete();
            return ResponseFormatter::success($data, 200, 'Category successfully deleted');
        } catch (\Exception $errors) {
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }
}
