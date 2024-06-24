<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\admin;
use App\Models\AssistantC;
use App\Models\AssistantSocial;
use App\Models\Medecin;
use App\Models\Ong;
use App\Models\Psychologue;
use App\Models\tpme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function addEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'cin' => 'required|string|unique:users,cin',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'dateN' => 'required|date',
            'sexe' => 'required|string',
            'role' => 'required|string',
            'identifier' => 'nullable|string',
            //'specialite' => 'required_if:role,medecin|string',
            'centre' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'fullName' => $request->input('fullName'),
            'cin' => $request->input('cin'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'dateN' => $request->input('dateN'),
            'sexe' => $request->input('sexe'),
            'role' => $request->input('role'),
        ]);

        switch ($request->input('role')) {
            case 'medecin':
                $medecin = Medecin::create([
                    'identifier' => $request->input('identifier'),
                    'specialite' => $request->input('specialite'),
                    'centre' => $request->input('centre'),
                    'id_user' => $user->id,
                ]);
                break;
            case 'psy':
                $psychologue = Psychologue::create([
                    'identifier' => $request->input('identifier'),
                    'centre' => $request->input('centre'),
                    'id_user' => $user->id,
                ]);
                break;
            case 'assistant sociale':
                $assistantSocial = AssistantSocial::create([
                    'identifier' => $request->input('identifier'),
                    'centre' => $request->input('centre'),
                    'id_user' => $user->id,
                ]);
                break;
            case 'assistant comm':
                $assistantComm =  AssistantC::create([
                    'identifier' => $request->input('identifier'),
                    //'region' => $request->input('region'),
                    'id_user' => $user->id,
                ]);
                    break;
            case 'ong':
                $ong =  Ong::create([
                    'identifier' => $request->input('identifier'),
                    'id_user' => $user->id,
                    ]);
                break;
            case 'tpme':
                $tpme =  tpme::create([
                    'id_user' => $user->id,
                ]);
                break;
            case 'admin':
                $admin =  admin::create([
                    'id_user' => $user->id,
                ]);
            break;
            default:
                return response()->json([
                    'message' => 'Role non pris en charge.'
                ]);
        }

        return response()->json(['message' => 'Employee added successfully'], 200);

    }


    public function updateEmploye(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.'
            ]);
        }

        $role = $user->role;
        $userUpdates = [
            'fullName' => $request->input('fullName'),
            'cin' => $request->input('cin'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'dateN' => $request->input('dateN'),
            'sexe' => $request->input('sexe'),
        ];

        // Handle specific role updates
        switch ($role) {
            case 'medecin':
                $medecinUpdates = [
                    'identifier' => $request->input('identifier'),
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

            case 'assistant comm':
                $AssisCUpdates = [
                    'identifier' => $request->input('identifier'),
                    // 'region' => $request->input('region'),
                ];
                AssistantC::where('id_user', $user->id)->update($AssisCUpdates);
                break;

            default:
                return response()->json([
                    'message' => 'Role non pris en charge.'
                ]);
        }

        // Update user profile
        $user->update($userUpdates);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.'
        ]);

        //return $request->all();
        // return response()->json([
        //     'message' => 'Profil mis à jour avec succès.'
        // ]);
    }
}



