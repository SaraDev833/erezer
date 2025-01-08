<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SubcategoryController extends Controller {
    /**
    * Display a listing of the resource.
    */

    public function index() {
        $subcategories = Subcategory::all();
        return response()->json( $subcategories, 200 );
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( Request $request ) {
        $validated = $request->validate( [
            'subcategory_name'=>'required',
            'category_slug'=>'required',
        ] );
        $slug = Str::slug( $validated[ 'subcategory_name' ] ).'-'.random_int( 200000, 999999 );
        if (Category::where('slug', $validated['category_slug'])->exists()) {
         
            $subcategory = Subcategory::create([
                'subcategory_name' => $validated['subcategory_name'],
                'slug' => $slug,
                'category_slug' => $validated['category_slug'],
            ]);
   
            return response()->json($subcategory, 201);
        } else{
            return response()->json(['msg'=>'category not found'] , 404);
        }

      
    }
    // get subcategories by category id

    public function getByCategory($slug){
        $subcategory = Subcategory::where('category_slug' , $slug)->get();
        if($subcategory->isEmpty()){
            return response()->json(['msg'=> 'no subcategories found for this category'], 404);
        }
        else{
            return response()->json($subcategory , 200);
        }
    }

    /**
    * Display the specified resource.
    */

    public function show( string $id ) {
        $subcategory = Subcategory::find($id);
        if(!$subcategory){
            return response()->json(['msg'=>"subcategory not found"] , 404);
        }

        return response()->json($subcategory , 200);
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( Request $request, string $id ) {
        $subcategory = Subcategory::find($id);
        if(!$subcategory){
            return response()->json(['msg' => 'subcategory not found'] ,404);
        }

        $validated = $request->validate([
            'subcategory_name'=> 'required',
            'category_slug'=> 'required',
        ]);
        $slug = Str::slug( $validated[ 'subcategory_name' ] ).'-'.random_int( 200000, 999999 );
        if(Category::where('slug' , $validated['category_slug'])->exists()){
            $subcategory = Subcategory::findOrFail($id)->update([
                'subcategory_name'=>$validated['subcategory_name'],
                'slug'=>$slug,
                'category_slug'=>$validated['category_slug'],
            ]);
            return response()->json($subcategory , 200);
        } else{
            return response()->json(['msg'=>'category not found'] , 404);
        }
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found.'], 404);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully.'], 200);
    }
}
