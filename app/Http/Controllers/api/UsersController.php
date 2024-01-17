<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'telephone' => 'required|string|max:20',
            'role' => 'required|in:admin,donateur,fondation',
            'bloque' => 'boolean',
        ]);

        $user = new User();
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->description = $request->input('description');
        $user->adresse = $request->input('adresse');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->telephone = $request->input('telephone');
        $user->role = $request->input('role');
        
        
        if ($request->input('role') == 'admin' || $request->input('role') == 'donateur') {
            $user->statut = 'accepte';
        } else {
            $user->statut = 'enattente'; 
        }
        if ($user->save()) {
            return response()->json([
                "status" => true,
                "message" => "Inscription effectuée avec succès",
                "data" => $user
            ]);   
    }else{
        return response()->json([
            "status" => false,
            "message" => "Erreur lors de l'inscription",
            "data" => null
        ]);
    }
}

public function login(Request $request){
    $request->validate([
        'email'=> 'required |email',
        'password'=> 'required',
    ]);

    $credentials = $request->only('email', 'password');

    if(!$token=JWTAuth::attempt($credentials)){
        return response()->json(['status' => 0, 'message' => 'Les Identifiants sont invalides'], 401);
    }


    $user= JWTAuth::user();

    $role= $user->role;

    if ($role === 'admin') {
        $message = 'Vous êtes connecté en tant qu\'administrateur';
    } elseif ($role === 'donateur') {
        $message = 'Vous êtes connecté en tant que donateur';
    } else{
        $message = 'Vous êtes connecté en tant que fondation';
    }

    return response()->json([
        'status' => 1,
        'message' => $message,
        'token' => $token,
        'role' => $role,
        'datas' => $user,
    ]);
}

public function dashboardAdmin(){
    $user= auth()->user();

    return response()->json([
        "message"=>"vous êtes connecter en tant que Administrateur",
        "data"=>$user
    ]);
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
