<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();
        return response()->json($brands , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required',
        ]);
        $brands = Brand::create([
            'name'=>$validated['name'],
        ]);
        return response()->json($brands , 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);
        if(!$brand){
            return response()->json(['msg'=> 'Brand is not found'] , 404);
        }
        return response()->json($brand , 200);
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);
        if(!$brand){
            return response()->json(['msg'=>'brand is not found']);
        }
         $validated = $request->validate([
            'name'=>'required',
         ]) ;
      $brandUpdate = Brand::find($id)->update([
        'name'=>$validated['name'],
      ]);
      return response()->json($brandUpdate , 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        if(!$brand){
            return response()->json(['msg'=>'brand is not found'] , 404);
        }
        $brand->delete();
        return response()->json(['msg'=>'Brand deleted successfully'] , 200);
    }
}
