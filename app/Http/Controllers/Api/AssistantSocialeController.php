<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssistantC;
use App\Models\AssistantSocial;
use App\Models\Medecin;
use App\Models\Psychologue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AssistantSocialeController extends Controller
{
    public function addAssistantSociale(Request $request)
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
            'centre' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            // Create a new user
            $user = User::create([
                'fullName' => $request->input('fullName'),
                'cin' => $request->input('cin'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'dateN' => $request->input('dateN'),
                'sexe' => $request->input('sexe'),
                'role' => 'assistant sociale',
            ]);
            if ($request->hasFile('image')) {
                $ImageFile = $request->file('image');
                $ImagePath = 'users';
                $ImageName = $user->id . '.' . $ImageFile->getClientOriginalExtension();
                $ImageFile->storeAs($ImagePath, $ImageName);
                $user->update(['image' => $ImageName]);
            }

            // Create a new assistant social associated with the user
            $assistantSocial = AssistantSocial::create([
                'identifier' => $request->input('identifier'),
                'centre' => $request->input('centre'),
                'id_user' => $user->id,
            ]);

            return response()->json([
                'message' => 'Compte Assistant Social a été créés avec succès',
                'user' => $user,
                'assistant_social' => $assistantSocial,
            ], 201);
        }
    }

    public function updateAssistantSociale(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'cin' => 'required|string|unique:users,cin,'.$id,
            'phone' => 'required|string|unique:users,phone,'.$id,
            'email' => 'nullable|email|unique:users,email,'.$id,
            'dateN' => 'required|date',
            'sexe' => 'nullable|string',
            'identifier' => 'required|string',
            'centre' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        } else {
            $assistant = AssistantSocial::find($id);

            if (!$assistant) {
                return response()->json(['message' => 'Assistant Sociale not found'], 404);
            }

            $user = $assistant->user;

            // Update the user
            $user->update([
                'fullName' => $request->input('fullName'),
                'cin' => $request->input('cin'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'dateN' => $request->input('dateN'),
                'sexe' => $request->input('sexe'),
                'role' => 'assistant sociale',
            ]);

            // Update the assistant social
            $assistant->update([
                'identifier' => $request->input('identifier'),
                'centre' => $request->input('centre'),
            ]);

            return response()->json(
                ['message' => 'Assistant a été mis à jour avec Succès',
                 'data' => $assistant
                ], 200);
        }
    }


    public function deleteAssistantSociale($id)
    {
        $assistant = AssistantSocial::find($id);
        if (!$assistant) {
            return response()->json(['message' => 'Cet assistant n\'existe pas'], 404);
        }
        $user = $assistant->user;

        $assistant->delete();
        $user->delete();

        return response()->json(['message' => 'assistant a été supprimé avec Succès'], 200);
    }


    //Kayna get lte7t Choufha w khtar li rta7ity fiha
    public function getAllEmployees()
    {
        $medecins = Medecin::with('user')->get();
        $psychologues = Psychologue::with('user')->get();
        $assistants = AssistantSocial::with('user')->get();
        $assistantsComm = AssistantC::with('user')->get();

        $employees = [
            'medecins' => $medecins,
            'psychologues' => $psychologues,
            'assistants' => $assistants,
            'assistantComm'=> $assistantsComm
        ];

        return response()->json(['employees' => $employees]);
    }
    // public function getAllEmployees()
    // {
    //     $medecins = Medecin::with('user')->get();
    //     $psychologues = Psychologue::with('user')->get();
    //     $assistants = AssistantSocial::with('user')->get();
    //     $assistantsComm = AssistantC::with('user')->get();

    //     $allEmployees = $medecins->merge($psychologues)
    //                             ->merge($assistants)
    //                             ->merge($assistantsComm);

    //     return response()->json(['employees' => $allEmployees], 201);
    // }

    public function updateProfile(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'cin' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'dateN' => 'required|date',
            'sexe' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'identifier' => 'required|string',
            'specialite' =>'nullable|string',
            'centre' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = $user->role;
        $old_image = $user->image;

        $userUpdates = [
            'fullName' => $request->input('fullName'),
            'cin' => $request->input('cin'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'dateN' => $request->input('dateN'),
            'sexe' => $request->input('sexe'),
        ];

        switch ($role) {
            case 'medecin':
                $medecinUpdates = [
                    'identifier' => $request->input('identifier'),
                    'specialite' => $request->input('specialite'),
                    'centre' => $request->input('centre'),

                ];
                Medecin::where('id_user', $user->id)->update($medecinUpdates);
                break;

            case 'assistant sociale':

                $assistantSocialeUpdates = [
                    'identifier' => $request->input('identifier'),
                    'centre' => $request->input('centre'),
                ];
                AssistantSocial::where('id_user', $user->id)->update($assistantSocialeUpdates);
                break;

            case 'psy':

                $psychologueUpdates = [
                    'identifier' => $request->input('identifier'),
                    'centre' => $request->input('centre'),
                ];
                Psychologue::where('id_user', $user->id)->update($psychologueUpdates);
                break;

            case 'AssC':
                $AssisCUpdates = [
                    'identifier' => $request->input('identifier'),
                    'region' => $request->input('region'),
                ];
                AssistantC::where('id_user', $user->id)->update($AssisCUpdates);
                break;

            default:
                return response()->json([
                    'message' => 'Role non pris en charge.'
                ]);
        }

        $user->update($userUpdates);
        if ($request->hasFile('image')) {
            $ImageFile = $request->file('image');
            $ImagePath = 'users';
            $ImageName = $user->id . '.' . $ImageFile->getClientOriginalExtension();

            // Delete the old image
            if ($user->image) {
                unlink('assets/images/users/' . $old_image);
            }

            $ImageFile->storeAs($ImagePath, $ImageName);
            $user->update(['image' => $ImageName]);
        }
        return response()->json([
            'message' => 'Profil mis à jour avec succès.'
        ]);
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.'
            ]);
        }
        $role = $user->role;

        switch ($role) {
            case 'medecin':
                    if($user->image)
                    {
                        unlink('assets/images/users/'.$user->image);
                        $medecinFolderPath = 'assets/images/users/' . $user->id;
                        if (File::isDirectory($medecinFolderPath)) {
                            File::deleteDirectory($medecinFolderPath);
                        }
                    }
                $user->delete();
                Medecin::where('id_user', $user->id)->delete();
                break;

            case 'assistant sociale':
                if($user->image)
                {
                unlink('assets/images/users/'.$user->image);
                    $medecinFolderPath = 'assets/images/users/' . $user->id;
                    if (File::isDirectory($medecinFolderPath)) {
                        File::deleteDirectory($medecinFolderPath);
                    }
                }
                $user->delete();
                AssistantSocial::where('id_user', $user->id)->delete();
                break;

            case 'psy':
                if($user->image)
                {
                unlink('assets/images/users/'.$user->image);
                    $medecinFolderPath = 'assets/images/users/' . $user->id;
                    if (File::isDirectory($medecinFolderPath)) {
                        File::deleteDirectory($medecinFolderPath);
                    }
                }
                $user->delete();
                Psychologue::where('id_user', $user->id)->delete();
                break;

            case 'AssC':
                unlink('assets/images/users/'.$user->image);
                    $medecinFolderPath = 'assets/images/users/' . $user->id;
                    if (File::isDirectory($medecinFolderPath)) {
                        File::deleteDirectory($medecinFolderPath);
                    }
                $user->delete();
                AssistantC::where('id_user', $user->id)->delete();
                break;

            default:
                return response()->json([
                    'message' => 'Role non pris en charge.'
                ]);
        }

        return response()->json([
            'message' => 'Compte Supprimé avec succès.'
        ]);
    }

    public function getuserById($userId)
    {
        $Searchuser = User::find($userId);
        if (!$Searchuser) {
            return response()->json([
                'message' => 'Utilisateur introuvable.'
            ]);
        }
        $role = $Searchuser->role;

        switch ($role) {
            case 'medecin':
                $user= User::with('medecin')->get();
                break;

            case 'assistant sociale':
                $user= User::with('assistantSocial')->get();
                break;

            case 'psy':
                $user= User::with('psychologue')->get();
                break;

            case 'AssC':
                $user= User::with('assistantC')->get();
                break;

            default:
                return response()->json([
                    'message' => 'Role non pris en charge.'
                ]);

        }
        return response()->json([
            'user' => $user,
        ]);
    }

    public function getAllUsers()
    {
        $users=User::get()->all();
        return response()->json([
            'users' => $users,
        ]);
    }

}
