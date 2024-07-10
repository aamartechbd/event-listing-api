<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            // 'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'website' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'website' => $request->website,
        ]);

        return response()->json($user, 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
            'token_type' => 'Bearer',
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    } 

    public function getProfile()
    {
        $user = Auth::user();
        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
      
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            // 'profile_image' => 'nullable|string', 
            'website' => 'nullable|url|max:255',
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->description = $request->input('description');
        $user->website = $request->input('website');

        $imagePath = null;
        if ($request->filled('profile_image_url')) {
            if ($user->profile_image) {
                $oldImagePath = public_path($user->profile_image);
            
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $base64Image = $request->input('profile_image_url');
            $extension = explode('/', mime_content_type($base64Image))[1]; // Extract extension from base64 string

            // Generate a unique filename
            $filename = time() . '.' . $extension;
            $imagePath = 'profile_images/' . $filename;

            // Decode and save the base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            file_put_contents(public_path($imagePath), $imageData);

            // Update the user's profile_image field
            $user->profile_image = $imagePath;
            
        }
        $user->save();

        return response()->json($user);
    }
}
