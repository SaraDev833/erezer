<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required',  
            'email'=>'required|unique:users,email',
            'password'=>'required',
        ],[
            'name.required'=>'user name is required', 
            'email.unique'=>'This email already exists',
            'password.required'=>'password is required',
        ]);

       try{
        $user = User::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'password'=>bcrypt($validated['password']) ,
         ]);
        return response()->json($user , 201);
       } catch(\Exception $e){
        return response()->json([
            'error' => 'Error creating user',
            'message' => $e->getMessage()
        ], 500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json(['error'=>'user not found' , 404]);
        }
        else{
            return response()->json($user , 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $validated = $request->validate([
                'name' => 'required', 
                'email' => 'required|unique:users,email,' . $id, // Ignore the current user's email
                'password' => 'required',
            ]);

            try {
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => bcrypt($validated['password']),
                ]);
                return response()->json($user, 200); // Use 200 for update
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Error updating user',
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    /**

     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json(['error'=>'user not found' , 404]);
        }
        else{
            $user->delete();
            return response(['msg' => 'user deleted'] , 200);
        }
    }
}
