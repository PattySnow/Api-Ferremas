<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    private function registerUser(Request $request, $roleName)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Si la validación falla, devolver un mensaje de error personalizado
        if ($validator->fails()) {
            return response()->json(['message' => 'Debe ingresar todos los datos.'], 422);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar el rol al usuario
        $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();
        if ($role) {
            $user->assignRole($role);
        } else {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Crear un token para el usuario
        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json([
            'message' => "Usuario registrado exitosamente con el rol de {$role->name}",
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function register(Request $request)
    {
        return $this->registerUser($request, 'client');
    }

    public function registerWorker(Request $request)
    {
        return $this->registerUser($request, 'worker');
    }

    public function registerAdmin(Request $request)
    {
        return $this->registerUser($request, 'admin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Intentar autenticar al usuario
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Si las credenciales son incorrectas, enviar un mensaje de error personalizado
            return response()->json(['error' => 'Las credenciales son incorrectas'], 401);
        }
    
        // Si las credenciales son correctas, generar un token y devolverlo en la respuesta
        $user = Auth::user();
        $token = $user->createToken('token-name')->plainTextToken;
    
        return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Has cerrado sesión'], 200);
    }

    public function revokeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Todos los tokens revocados'], 200);
    }
}
