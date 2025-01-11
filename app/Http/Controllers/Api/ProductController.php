<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products= Product::with(['tags', 'variations.color' , 'variations.size' , 'gallery'])->get();
        return response()->json($products , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $requestData = $request->all();

        if ($request->has('tag_ids') && is_string($request->tag_ids)) {
            $requestData['tag_ids'] = json_decode($request->tag_ids, true);
        }
        $validator = Validator::make($requestData , [
            'name'=>'required',
            'category_id'=>'required|exists:categories,id',
            'subcategory_id'=>'required:exists:subcategories,id',
            'short_desp'=>'required',
            'long_desp'=>'required',
            'brand'=>'required',
            'prev_image'=>'required|image|mimes:jpg,jpeg,png',
            'gallery_images'=>'required|image|mimes:jpg,jpeg,png',
            'tag_ids'=>'array',
            'tag_ids.*'=>'exists:tags,id',
            'color_id'=>'required|exists:colors,id',
            'size_id'=>'required|exists:sizes,id',
            'quantity'=>'required',
            'price'=>'required',
        ],[
              'name.required'=>'The product name is required',
              'category_id.required'=>'The category is required',
              'category_id.exists'=>'The selected category does not exist',
              'subcategory_id.required'=>'The subcategory is required',
              'subcategory.exists'=>'The selected subcategory does not exist',
              'short_desp.required'=>"Short Description is required",
              'Long_desp.required'=>"Short Description is required",
              'prev_image.image'=>'The preview Image must be a valid image file',
              'prev_image.mimes'=>'The preview Image must be in jpg ,jpeg or png format',
              'gallery_images.image'=>'The Image must be a valid image file',
              'gallery_images.mimes'=>'The Image must be in jpg ,jpeg or png format',
              'tag_ids.*.exists'=>'One or more selected tags are invalid',
              'color_id.required'=>'The color is required',
              'color_id.exists'=>'The selected color does not exist',
              'size_id.required'=>'The size is required',
              'size_id.exists'=>'The selected size does not exist',
              'quantity.required'=>'Quantity is required',
              'price.required'=>'The price is required',
        ]);
        
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()] , 422);
        }
        $validated = $validator->validated();
        $slug= Str::slug(($validated['name'].'-'.random_int(200000, 999999)));
       $previewImagePath = null;
       if($request->hasFile('prev_image')){
        $previewImage = $request->file('prev_image');
        $previewImageName = uniqid() . '-' . time() . '.' . $previewImage->getClientOriginalExtension();  // Generate unique name
        $previewImagePath = $previewImage->storeAs('products/preview', $previewImageName, 'public');  // Store image with unique name
    }
    
       $product= Product::create(array_merge($validated , [
        'slug'=>$slug,
        'prev_image'=>$previewImagePath,
       ]));
       if(isset($validated['tag_ids'])){
        $product->tags()->sync($validated['tag_ids']);
       }

       $product->variations()->create([
        'color_id'=>$validated['color_id'],
        'size_id'=>$validated['size_id'],
         'price'=>$validated['price'],
         'quantity'=>$validated['quantity'],
       ]);
       if ($request->hasFile('gallery_images')) {
        foreach ($request->file('gallery_images') as $galleryImage) {
            $galleryImageName = uniqid() . '-' . time() . '.' . $galleryImage->getClientOriginalExtension();  // Generate unique name
            $galleryImagePath = $galleryImage->storeAs('products/gallery', $galleryImageName, 'public');  // Store gallery image with unique name
            
            \Log::info("Stored gallery image: $galleryImagePath");
            $product->gallery()->create([
                'images' => $galleryImagePath,
                'product_id' => $product->id,  // Associate with the current product
            ]);
        }
    }
    

     if (isset($validated['tag_ids'])) {
        $product->tags()->sync($validated['tag_ids']);
    }
    
      return response()->json($product->load('tags' , 'variations.color' , 'variations.size' , 'gallery') , 201);
    }

    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        $product= Product::with('tags', 'variations.color','variations.size' , 'variation.price' , 'variations.quantity' , 'gallery')->find($id);
        if(!$product){
            return response()->json(['error'=>'product is not found'] , 404);
        }
        else{
            return response()->json($product , 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {    
       
        $product = Product::find($id);
        $requestData = $request->all();

        if ($request->has('tag_ids') && is_string($request->tag_ids)) {
            $requestData['tag_ids'] = json_decode($request->tag_ids, true);
        }
        $validator = Validator::make($requestData , [
            'name'=>'required',
            'category_id'=>'required|exists:categories,id',
            'subcategory_id'=>'required:exists:subcategories,id',
            'short_desp'=>'required',
            'long_desp'=>'required',
            'brand'=>'required',
            'prev_image'=>'required|image|mimes:jpg,jpeg,png',
            'gallery_images'=>'required|image|mimes:jpg,jpeg,png',
            'tag_ids'=>'array',
            'tag_ids.*'=>'exists:tags,id',
            'color_id'=>'required|exists:colors,id',
            'size_id'=>'required|exists:sizes,id',
            'quantity'=>'required',
            'price'=>'required',
        ],[
              'name.required'=>'The product name is required',
              'category_id.required'=>'The category is required',
              'category_id.exists'=>'The selected category does not exist',
              'subcategory_id.required'=>'The subcategory is required',
              'subcategory.exists'=>'The selected subcategory does not exist',
              'short_desp.required'=>"Short Description is required",
              'Long_desp.required'=>"Short Description is required",
              'prev_image.image'=>'The preview Image must be a valid image file',
              'prev_image.mimes'=>'The preview Image must be in jpg ,jpeg or png format',
              'gallery_images.image'=>'The Image must be a valid image file',
              'gallery_images.mimes'=>'The Image must be in jpg ,jpeg or png',
              'tag_ids.*.exists'=>'One or more selected tags are invalid',
              'color_id.required'=>'The color is required',
              'color_id.exists'=>'The selected color does not exist',
              'size_id.required'=>'The size is required',
              'size_id.exists'=>'The selected size does not exist',
              'quantity.required'=>'Quantity is required',
              'price.required'=>'The price is required',
        ]);
          if($validator->fails()){
            return response()->json(['error'=>$validator->errors()] , 422);
          }
         $validated = $validator->validated();
         $slug = Str::slug($validated['name'].'-'.random_int(200000, 999999));
         
         if($request->hasFile('prev_image')){
            if($product->prev_image){
                Storage::disk('public')->delete('products/preview/' . $product->preview_image);
                
                $previewImage = $request->file('prev_image');
                $previewImageName = uniqid().'-'.$previewImage->getClientOrOriginalExtension();
                $previewImagePath= $previewImage->storeAs('products/preview' , $previewImageName ,'public');
            }
         }

      $updatedProduct = $product->update(array_merge($validated) , [
           'slug'=>$slug,
           'prev_image'=>$previewImagePath
      ]);

      if (isset($validated['tag_ids'])) {
        $product->tags()->sync($validated['tag_ids']);
    }

       $product->variations()->delete();
       $product->variations()->create([
        'color_id' => $validated['color_id'],
        'size_id' => $validated['size_id'],
        'price' => $validated['price'],
        'quantity' => $validated['quantity'],
    ]);
          
    if ($request->hasFile('gallery_images')) {
        foreach ($request->file('gallery_images') as $galleryImage) {
            $galleryImageName = uniqid() . '-' . time() . '.' . $galleryImage->getClientOriginalExtension();  // Generate unique name
            $galleryImagePath = $galleryImage->storeAs('products/gallery', $galleryImageName, 'public');  // Store gallery image with unique name
            
            // Create the gallery record and associate it with the product
            $product->gallery()->create([
                'images' => $galleryImagePath,
                'product_id' => $product->id,  // Associate with the current product
            ]);
        }
    }
    return response()->json($product->load('tags' , 'variations.color' , 'variations.size' ,'variations.price', 'variations.quantity', 'gallery') , 201);
    }

    /**
     * Remove the specified resource from storage.


     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if($product->prev_image){
            Storage::disk('public')->delete('products/preview/' . $product->prev_image);
        }
        $product->gallery()->each(function($gallery){
          Storage::disk('public')->delete('products/gallery/' .$gallery->images);
        });

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }
}
