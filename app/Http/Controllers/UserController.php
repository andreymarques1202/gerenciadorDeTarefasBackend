<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function Register(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "username" => "required|string|max:150",
            "password" => "required|string|min:8|confirmed"
        ],
        [
            "name.required" => "O nome é obrigatório.",
            "username.required" => "O nome de usuário é obrigatório.",
            "password.required" => "A senha é obrigatória.",
            "password.min" => "A senha deve ter no mínimo 8 caracteres.",
            "password.confirmed" => "As senhas não conferem.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], 400);
        }

        User::create([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "message" => "Usuário cadastrado com sucesso!"
        ], 201);
    }

    public function Login(Request $request) {
        $validator = Validator::make($request->all(), [
            "username_or_email" => "required|string",
            "password" => "required|string"
        ],
        [
            "username_or_email.required" => "Campo não pode ser vázio!",
            "password.required" => "Campo obrigatorio!"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], 400);
        }

        $credentials = [
            "password" => $request->password,
        ];

        if(filter_var($request->username_or_email, FILTER_VALIDATE_EMAIL)) {
            $credentials["email"] = $request->username_or_email;
        } else {
            $credentials["username"] = $request->username_or_email;
        }

        $user = User::where("email", $request->username_or_email)
        ->orWhere("username", $request->username_or_email)
        ->first();

        if (!$user) {
        return response()->json([
            "error" => "Credenciais inválidas"
        ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            "error" => "Credenciais inválidas"
        ], 401);
        }

        $token = $user->createToken("API Token")->plainTextToken;

        return response()->json([
            "token" => $token,
            "user" => $user
        ], 200);

    }

    public function logout(Request $request) {
        $user = Auth::user();

        if($user) {
            $user->tokens->each(function($token) {
                $token->delete();
            });

            return response()->json([
                "message" => "Logout realizado com sucesso."
            ], 200);
        }

        return response()->json([
            "error" => "Usuário não autenticado."
        ], 401);

    }

    public function updatePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "current_password" => "required|string",
            "new_password" => "required|string|min:8|confirmed",
        ], [
            "current_password.required" => "A senha atual é obrigatória ser a mesma ja registrada.",
            "new_password.required" => "A nova senha é obrigatória.",
            "new_password.min" => "A nova senha deve ter pelo menos 6 caracteres.",
            "new_password.confirmed" => "A confirmação da nova senha não confere.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], 400);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(["error" => "Usuário não autenticado."], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(["error" => "A senha atual está incorreta."], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(["message" => "Senha atualizada com sucesso!"]);
    }

    public function update(Request $request) {
        $request->validate([
            "name" => "nullable|string|max:255",
            "username" => "nullable|string|max:255|unique:users,username," . Auth::id(),
            "email" => "nullable|email|max:255|unique:users,email," . Auth::id(),
        ], [
            "email.email" => "Informe um email válido.",
            "username.unique" => "Este nome de usuário já está em uso.",
            "email.unique" => "Este email já está em uso.",
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update(array_filter([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
        ]));

        return response()->json(["message" => "Dados atualizados com sucesso!", "user" => $user]);
    }
}
 