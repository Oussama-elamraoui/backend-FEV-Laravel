<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssistantC;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AssistantComController extends Controller
{

    public function AddAssC(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'cin' => 'required|string|unique:users',
            'phone' => 'required|string|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:6',
            'dateN' => 'required|date',
            'sexe' => 'nullable|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif',
            'identifier' => 'required|string',
            'region' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $user = User::create([
                'fullName' => $request->input('fullName'),
                'cin' => $request->input('cin'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'dateN' => $request->input('dateN'),
                'sexe' => $request->input('sexe'),
                'role' => 'AssC',
            ]);
            if ($request->hasFile('image')) {
                $ImageFile = $request->file('image');
                $ImagePath = 'users';
                $ImageName = $user->id . '.' . $ImageFile->getClientOriginalExtension();
                $ImageFile->storeAs($ImagePath, $ImageName);
                $user->update(['image' => $ImageName]);
            }

            $medecin = AssistantC::create([
                'identifier' => $request->input('identifier'),
                'region' => $request->input('region'),
                'id_user' => $user->id,
            ]);

            return response()->json(['message' => 'Assistant Communautaire created successfully', 'data' => $medecin], 200);
        }
    }

    // Update Assistant Communotaire
    public function updateAssC(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'sometimes|required|string',
            'cin' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string',
            'email' => 'sometimes|email',
            'password' => 'sometimes|required|string|min:6',
            'dateN' => 'sometimes|date',
            'sexe' => 'sometimes|string',
            'role' => 'sometimes|string',
            'identifier' => 'sometimes|required|string',
            'region' => 'sometimes|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        } else {
            $AssistantC = AssistantC::find($id);

            if (!$AssistantC) {
                return response()->json(['message' => 'Assistant Communautaire not found'], 404);
            }

            $user = $AssistantC->user;

            // Update user
            $user->update([
                'fullName' => $request->input('fullName', $user->fullName),
                'cin' => $request->input('cin', $user->cin),
                'phone' => $request->input('phone', $user->phone),
                'email' => $request->input('email', $user->email),
                'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password,
                'dateN' => $request->input('dateN', $user->dateN),
                'sexe' => $request->input('sexe', $user->sexe),
                'role' => $request->input('role', $user->role),
            ]);

            // Update medecin
            $AssistantC->update([
                'identifier' => $request->input('identifier', $AssistantC->identifier),
                'region' => $request->input('region', $AssistantC->region),
            ]);

            return response()->json(['message' => 'Assistant Communautaire updated successfully', 'data' => $AssistantC], 200);
        }
    }

     // Delete Medecin
     public function deleteAssC($id)
     {
         $AssistantC = AssistantC::find($id);

         if (!$AssistantC) {
             return response()->json(['message' => 'Assistant Communautaire n\'existe pas'], 404);
         }
         $AssistantC->delete(); // Delete AssistantC
         $AssistantC->user->delete(); // Delete associated user

         return response()->json(['message' => 'Compte Assistant Communautaire spprimé avec succée'], 200);
     }
}
