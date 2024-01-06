<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Helper\Utils;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $data = Card::all();
        return ResponseFormatter::success($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25|min:3|unique:\App\Models\Card,name',
            'type' => 'required|string|max:50|min:3',
            'image' => 'required|image|max:1000',
            'description' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->all());
        }

        if ($request->hasFile('image')) {
            $oriFileName = $request->file('image')->getClientOriginalName();
            $oriFileNameArr = explode('.', $oriFileName);
            $fileExt = end($oriFileNameArr);
            $path = './cards/';
            $image = 'CARD-' . time() . '.' . $fileExt;

            if ($request->file('image')->move($path, $image)) {
                $recordFile = '/cards/' . $image;
            } else {
                $recordFile = false;
            }
        }

        if (!$recordFile) {
            return ResponseFormatter::error(null, 400, 'Failed when upload file, please try again');
        }
        
        try {
            $data = Card::create([
                'name' => strtoupper($request->name),
                'type' => ucfirst($request->type),
                'image' => $recordFile,
                'description' => $request->description
            ]);

            return ResponseFormatter::success($data, 201, 'Card successfully saved');
        } catch (\Exception $errors) {
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }

    public function show($id)
    {
        $data = Card::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }
        
        return ResponseFormatter::success($data);
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25|min:3|unique:\App\Models\Card,name,'.$id.',id',
            'type' => 'required|string|max:50|min:3',
            'image' => 'nullable|image|max:1000',
            'description' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->all());
        }

        $data = Card::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }

        $recordFile = $data->image;

        if ($request->hasFile('image')) {
            $oriFileName = $request->file('image')->getClientOriginalName();
            $oriFileNameArr = explode('.', $oriFileName);
            $fileExt = end($oriFileNameArr);
            $path = './cards/';
            $image = 'CARD-' . time() . '.' . $fileExt;

            if ($request->file('image')->move($path, $image)) {
                $recordFile = '/cards/' . $image;
                if (File::exists(Utils::public_path($data->image))) {
                    File::delete(Utils::public_path($data->image));
                }
            } else {
                $recordFile = false;
            }
        }
        
        try {
            $data->update([
                'name' => strtoupper($request->name),
                'type' => ucfirst($request->type),
                'image' => $recordFile,
                'description' => $request->description
            ]);

            return ResponseFormatter::success(null, 200, 'Card successfully updated');
        } catch (\Exception $errors) {
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }

    public function destroy($id)
    {
        $data = Card::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }

        try {
            if (File::exists(Utils::public_path($data->image))) {
                File::delete(Utils::public_path($data->image));
            }

            $data->delete();

            return ResponseFormatter::success($data, 200, 'Card successfully deleted');
        } catch (\Exception $errors) {
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }
}
