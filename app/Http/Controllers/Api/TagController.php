<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag_name'=> 'required|unique:tags,tag_name',
        ],[
           'tag_name.required'=>'tag name is required',
           'tag_name.unique'=>'this tag name already exists'
        ]);

        $tags = Tag::create([
            'tag_name'=> $validated['tag_name']
        ]);
        return response()->json($tags , 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            return response()->json(['msg'=>'tag does not exist']);
        }

        return response()->json($tag , 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            return response()->json(['msg'=>'tag does not exist']);
        }
        else{
            $validated=$request->validate([
                'tag_name'=>'required|unique:tags,tag_name,' .$id,
            ],[
                'tag_name.required'=>'tag name is required',
                'tag_name.unique'=>'This tag name already exists',
            ]);

            $tagUpdate = $tag->update([
               'tag_name'=>$validated['tag_name'],
            ]);
            return response()->json($tagUpdate , 200);
        }

       

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            return response()->json(['msg'=>'tag is not found'] , 404);
        }
        $tag->delete();
        return response()->json(['msg'=>'Tag deleted successfully'] , 200);
    }
}
