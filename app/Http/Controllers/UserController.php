<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Listar todos los usuarios (solo para administradores)
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permiso para ver esta lista de usuarios.'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        $user = Auth::user();

        if ($user->id != $id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permiso para ver este usuario.'], 403);
        }

        $userToShow = User::find($id);

        if (is_null($userToShow)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($userToShow);
    }

    // Actualizar un usuario existente
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->id != $id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permiso para actualizar este usuario.'], 403);
        }

        $userToUpdate = User::find($id);

        if (is_null($userToUpdate)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('name')) {
            $userToUpdate->name = $request->name;
        }


        if ($request->has('password')) {
            $userToUpdate->password = Hash::make($request->password);
        }

        $userToUpdate->save();

        return response()->json($userToUpdate);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->id != $id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permiso para eliminar este usuario.'], 403);
        }

        $userToDelete = User::find($id);

        if (is_null($userToDelete)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $userToDelete->delete();

        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }

    
}
