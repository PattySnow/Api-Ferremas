<?php
namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:100',
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

        $permission = Permission::create($validatedData);

        return response()->json([
            'message' => "Permiso creado exitosamente!",
            'permission' => $permission
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::find($id);
        if ($permission) {
            return response()->json($permission, 200);
        } else {
            return response()->json(['error' => 'Permiso no encontrado'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::find($id);

        if ($permission) {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|min:5|max:100',
            ], [
                'name.required' => 'El campo nombre es obligatorio',
                'name.string' => 'El campo nombre debe ser una cadena de caracteres',
                'name.min' => 'El campo nombre debe tener al menos 5 caracteres',
                'name.max' => 'El campo nombre no puede tener más de 100 caracteres',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Asignar manualmente guard_name a 'api'
            $validatedData = $validator->validated();
            $validatedData['guard_name'] = 'api';

            $permission->update($validatedData);

            return response()->json($permission, 200);
        } else {
            return response()->json(['error' => 'Permiso no encontrado'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);

        if ($permission) {
            $permissionName = $permission->name;
            $permission->delete();
            return response()->json([
                'message' => "Permiso '{$permissionName}' eliminado con éxito."
            ], 200);
        } else {
            return response()->json(['error' => 'Permiso no encontrado'], 404);
        }
    }

    /**
     * Asignar permiso a rol.
     */
    public function assignPermissionToRole(Request $request, $role_id)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
        ], [
            'permission_id.required' => 'El ID del permiso es obligatorio',
            'permission_id.exists' => 'El permiso no existe',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::find($role_id);

        if (!$role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $permission_id = $request->input('permission_id');
        $permission = Permission::find($permission_id);

        if ($role->hasPermissionTo($permission)) {
            return response()->json(['error' => "El rol ya tiene el permiso '{$permission->name}'"], 400);
        }

        $role->givePermissionTo($permission);

        return response()->json(['message' => "Permiso '{$permission->name}' asignado al rol '{$role->name}' exitosamente."], 200);
    }

    /**
     * Asignar permiso a usuario.
     */
    public function assignPermissionToUser(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
        ], [
            'permission_id.required' => 'El ID del permiso es obligatorio',
            'permission_id.exists' => 'El permiso no existe',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $permission_id = $request->input('permission_id');
        $permission = Permission::find($permission_id);

        if ($user->hasPermissionTo($permission)) {
            return response()->json(['error' => "El usuario ya tiene el permiso '{$permission->name}'"], 400);
        }

        $user->givePermissionTo($permission);

        return response()->json(['message' => "Permiso '{$permission->name}' asignado al usuario '{$user->name}' exitosamente."], 200);
    }

    /**
     * Revocar permiso de rol.
     */
    public function revokePermissionFromRole(Request $request, $role_id)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
        ], [
            'permission_id.required' => 'El ID del permiso es obligatorio',
            'permission_id.exists' => 'El permiso no existe',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::find($role_id);

        if (!$role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $permission_id = $request->input('permission_id');
        $permission = Permission::find($permission_id);

        if (!$role->hasPermissionTo($permission)) {
            return response()->json(['error' => "El rol no tiene el permiso '{$permission->name}'"], 400);
        }

        $role->revokePermissionTo($permission);

        return response()->json(['message' => "Permiso '{$permission->name}' revocado del rol '{$role->name}' exitosamente."], 200);
    }

    /**
     * Revocar permiso de usuario.
     */
    public function revokePermissionFromUser(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
        ], [
            'permission_id.required' => 'El ID del permiso es obligatorio',
            'permission_id.exists' => 'El permiso no existe',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $permission_id = $request->input('permission_id');
        $permission = Permission::find($permission_id);

        if (!$user->hasPermissionTo($permission)) {
            return response()->json(['error' => "El usuario no tiene el permiso '{$permission->name}'"], 400);
        }

        $user->revokePermissionTo($permission);

        return response()->json(['message' => "Permiso '{$permission->name}' revocado del usuario '{$user->name}' exitosamente."], 200);
    }
}
