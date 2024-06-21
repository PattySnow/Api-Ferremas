<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los campos
        $request->validate([
            'name' => 'required|string|min:4|max:100',
         
        ], [
            'name.required' => 'El campo nombre es obligatorio',
        ]);
    
        // Crear el rol con 'guard_name' configurado como 'api'
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api',
        ]);
    
        return response()->json([
            'message' => "Rol creado exitosamente!",
            'rol' => $role
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        if ($role) {
            return response()->json($role, 200);
        } else {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
    
        if ($role) {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|min:4|max:100',
            ], [
                'name.required' => 'El campo nombre es obligatorio',
                'name.string' => 'El campo nombre debe ser una cadena de caracteres',
                'name.min' => 'El campo nombre debe tener al menos 4 caracteres',
                'name.max' => 'El campo nombre no puede tener más de 100 caracteres',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Asignar manualmente guard_name a 'api'
            $validatedData = $validator->validated();
            $validatedData['guard_name'] = 'api';
    
            $role->update($validatedData);
    
            return response()->json($role, 200);
        } else {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if ($role) {
            $roleName = $role->name;
            $role->delete();
            return response()->json([
                'message' => "Rol '{$roleName}' eliminado con éxito."], 200);
        } else {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }
    
    }

    public function assignRoleToUser(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ], [
            'role_id.required' => 'El ID del rol es obligatorio',
            'role_id.exists' => 'El rol no existe',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $role_id = $request->input('role_id');
        $role = Role::find($role_id);

        $user->assignRole($role->name);

        return response()->json(['message' => "Rol '{$role->name}' asignado al usuario '{$user->name}' exitosamente."], 200);
    }
}
