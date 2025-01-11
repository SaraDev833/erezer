<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = Size::all();
        return response()->json($sizes , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'size'=>'required',
            
        ],[
            'size.required'=>'size is required',
            
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()] , 422);
        }
        $validated= $validator->validated();
        $size = Size::create([
            'size'=>$validated['size'],
            
        ]);
        return response()->json($size , 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $size = Size::find($id);
      if(!$size){
        return response()->json(['error'=>'no size found'] , 404);
      }
      else{
        return response()->json($size , 201);
      }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $size = Size::find($id);
        if(!$size){
            return response()->json(['error'=>'no size found'] , 404);
          }
          else{
            $validator = Validator::make($request->all() , [
                'size'=>'required',
              
            ],[
                'size.required'=>'size is required',
            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()] , 422);
            }
            $validated= $validator->validated();

            $updatedSize = $size->update([
                'size'=>$validated['size'],
            ]);
            return response()->json($updatedSize , 201);
          }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $size = Size::find($id);
        if(!$size){
            return response()->json(['error'=>'no size found'] , 404);
          }
          else{
            $size->delete();
            return response()->json(['msg'=>'size is deleted'] , 200);
          }
    }
}
