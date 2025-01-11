<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class colorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::all();
        return response()->json($colors , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'name'=>'required',
            'color_code'=>'required',
        ],[
            'name.required'=>'color name is required',
            'color_code.required'=>'please select color code',
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()] , 422);
        }
        $validated= $validator->validated();
        $color = Color::create([
            'name'=>$validated['name'],
            'color_code'=>$validated['color_code'],
        ]);
        return response()->json($color , 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $color = Color::find($id);
      if(!$color){
        return response()->json(['error'=>'no color found'] , 404);
      }
      else{
        return response()->json($color , 201);
      }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $color = Color::find($id);
        if(!$color){
            return response()->json(['error'=>'no color found'] , 404);
          }
          else{
            $validator = Validator::make($request->all() , [
                'name'=>'required',
                'color_code'=>'required',
            ],[
                'name.required'=>'color name is required',
                'color_code.required'=>'please select color code',
            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()] , 422);
            }
            $validated= $validator->validated();

            $updatedColor = $color->update([
                'name'=>$validated['name'],
                'color_code'=>$validated['color_code'],
            ]);
            return response()->json($updatedColor , 201);
          }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $color = Color::find($id);
        if(!$color){
            return response()->json(['error'=>'no color found'] , 404);
          }
          else{
            $color->delete();
            return response()->json(['msg'=>'color is deleted'] , 200);
          }
    }
}
