<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use App\Models\NotifMed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MedecinController extends Controller
{
    public function AddMedecin(Request $request)
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
            'specialite' => 'nullable|string',
            'centre' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }else{
            $user = User::create([
                'fullName' => $request->input('fullName'),
                'cin' => $request->input('cin'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'dateN' => $request->input('dateN'),
                'sexe' => $request->input('sexe'),
                'role' => 'medecin',
            ]);
            if ($request->hasFile('image')) {
                $ImageFile = $request->file('image');
                $ImagePath = 'users';
                $ImageName = $user->id . '.' . $ImageFile->getClientOriginalExtension();
                $ImageFile->storeAs($ImagePath, $ImageName);
                $user->update(['image' => $ImageName]);
            }

            $medecin = Medecin::create([
                'identifier' => $request->input('identifier'),
                'specialite' => $request->input('specialite'),
                'centre' => $request->input('centre'),
                'id_user' => $user->id,
            ]);

            return response()->json(['message' => 'Medecin created successfully', 'data' => $medecin], 200);
        }
    }


    // Update Medecin
    public function updateMedecin(Request $request, $id)
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
            'specialite' => 'sometimes|string',
            'centre' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }else{
            $medecin = Medecin::find($id);

            if (!$medecin) {
                return response()->json(['message' => 'Medecin not found'], 404);
            }

            $user = $medecin->user;

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
            $medecin->update([
                'identifier' => $request->input('identifier', $medecin->identifier),
                'specialite' => $request->input('specialite', $medecin->specialite),
                'centre' => $request->input('centre', $medecin->centre),
            ]);

            return response()->json(['message' => 'Medecin updated successfully', 'data' => $medecin], 200);
        }
    }


    // Delete Medecin
    public function deleteMedecin($id)
    {
        $medecin = Medecin::find($id);

        if (!$medecin) {
            return response()->json(['message' => 'Medecin n\'existe pas'], 404);
        }
        else if($medecin){
            unlink('assets/images/users/'.$medecin->image);
            // delete the id car folder
            $medecinFolderPath = 'assets/images/users/' . $medecin->id;
            if (File::isDirectory($medecinFolderPath)) {
                File::deleteDirectory($medecinFolderPath);
            }
        }
        $medecin->delete(); // Delete medecin
        $medecin->user->delete(); // Delete associated user

        return response()->json(['message' => 'Compte Medecin spprimé avec succée'], 200);
    }

    public function addNotification(Request $request)
    {
        $request->validate([
            'id_dec' => 'required|exists:declarations,id',
            'id_med' => 'required|exists:medecins,id',
            'message' => 'required|string',
        ]);

        $notification = NotifMed::create([
            'id_dec' => $request->input('id_dec'),
            'id_med' => $request->input('id_med'),
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => 'Notification saved successfully']);
    }
}
