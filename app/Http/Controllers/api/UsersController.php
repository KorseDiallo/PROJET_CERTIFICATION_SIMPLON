<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\inscriptionUsersRequest;
use App\Http\Requests\loginUsersRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;

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
 * @OA\Post(
 *      path="/api/register",
 *      operationId="storeUser",
 *      tags={"Inscription"},
 *      summary="Inscription d'un utilisateur",
 *      description="Enregistre un nouvel utilisateur",
 *      @OA\RequestBody(
 *          required=true,
 *          description="Données d'inscription de l'utilisateur",
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(property="nom", type="string"),
 *                  @OA\Property(property="prenom", type="string"),
 *                  @OA\Property(property="image", type="string", format="binary"),
 *                  @OA\Property(property="description", type="string"),
 *                  @OA\Property(property="numeroEnregistrement", type="string"),
 *                  @OA\Property(property="adresse", type="string"),
 *                  @OA\Property(property="email", type="string", format="email"),
 *                  @OA\Property(property="password", type="string", format="password"),
 *                  @OA\Property(property="telephone", type="string"),
 *                  @OA\Property(property="role", type="string", enum={"admin", "donateur", "fondation"}),
 *              ),
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Inscription réussie",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=true),
 *              @OA\Property(property="message", type="string", example="Inscription effectuée avec succès"),
 *              @OA\Property(property="data", type="object"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Erreur lors de l'inscription",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=false),
 *              @OA\Property(property="message", type="string", example="Erreur lors de l'inscription"),
 *          )
 *      ),
 * )
 */

    public function store(inscriptionUsersRequest $request)
    {
       

        $user = new User();
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->image = $this->storeImage($request->image);
        $user->description = $request->input('description');
        $user->numeroEnregistrement= $request->input('numeroEnregistrement');
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
        // Filtrer les attributs non vides avant de les renvoyer
        $userData = collect($user->toArray())->filter(function ($value) {
            return !is_null($value) && $value !== '';
        })->all();

        return response()->json([
            "status" => true,
            "message" => "Inscription effectuée avec succès",
            "data" => $userData
        ]);
    } else {
        return response()->json([
            "status" => false,
            "message" => "Erreur lors de l'inscription",
            "data" => null
        ]);
    }
}

public function login(loginUsersRequest $request){
   

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
        //"data"=>$user
    ]);
}

    private function storeImage($image)
    {
        return $image->store('images', 'public');
    }

    public function approuverDemande(User $user){
        $user->statut='accepte';
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "Demande approuvée avec succès"
                
            ]);   
        }
    }

    public function refuserDemande(User $user){
        $user->statut='refuse';
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "Demande refuser avec succès"
                
            ]);   
        }
    }

    public function bloquer(User $user){
        $user->bloque=true;
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "l'utilisateur a été bloqué  avec succès"
                
            ]);   
        }
    }

    public function debloquer(User $user){
        $user->bloque=false;
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "l'utilisateur a été debloqué  avec succès"
                
            ]);   
        }
    }

   

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Deconnexion Effectuée avec Succès',
        ]);
    }

    public function listeDonateur(){
        $listeDonateur= User::where('role','donateur')->get();

          // Filtrer les attributs non vides avant de les renvoyer
          $listeDonateur = $listeDonateur->map(function ($user) {
            return collect($user->toArray())->filter(function ($value) {
                return !is_null($value) && $value !== '';
            })->all();
        });

        if($listeDonateur->isNotEmpty()){
            return response()->json([
                "status" => true,
                "message" => "Liste de tous les donateurs",
                "data" => $listeDonateur
                
            ]); 
        }else{
            return response()->json([
                "status" => false,
                "message" => "Vous avez aucun donnateur inscrit pour le moment",
                "data" => []
                
            ]); 
        }
    }

    public function listeFondation(){
        $listeFondation= User::where('role','fondation')->get();

         // Filtrer les attributs non vides avant de les renvoyer
        $listeFondation = $listeFondation->map(function ($user) {
            return collect($user->toArray())->filter(function ($value) {
                return !is_null($value) && $value !== '';
            })->all();
        });

       

        if($listeFondation->isNotEmpty()){
            return response()->json([
                "status" => true,
                "message" => "Liste de tous les fondations",
                "data" => $listeFondation
                
            ]); 
        }else{
            return response()->json([
                "status" => false,
                "message" => "Vous avez aucun donnateur inscrit pour le moment",
                "data" => []
                
            ]); 
        }
    }

    public function supprimerCompte(){
       
        $userressource=User::where('id',Auth::user()->id)->first();

        $userressource->is_deleted=true;

         if($userressource->update()){
            return response()->json([
                "status" => true,
                "message" => "Votre Compte a été supprimé avec succès"
                
            ]);   
         }else{
            return response()->json([
                "status" => false,
                "message" => "Vous n'etes pas proritaire du compte"
                
            ]);   
         }
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
    public function destroy(User $user)
    {
        if($user->delete()){
             return response()->json([
                "status" => true,
                "message" => "l'utilisateur a été supprimé avec succès"
                
            ]); 
        }
    }
}
