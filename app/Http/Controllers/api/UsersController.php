<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\inscriptionUsersRequest;
use App\Http\Requests\loginUsersRequest;
use App\Mail\demandeApprouver;
use App\Models\collecteDeFond;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;

/**
 
*@OA\SecurityScheme(
*securityScheme="bearerAuth",
*type="http",
*scheme="bearer",
*bearerFormat="JWT",
*)
*/

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

/**
 * @OA\Post(
 *      path="/api/login",
 *      operationId="loginUser",
 *      tags={"Authentification"},
 *      summary="Authentification d'un utilisateur",
 *      description="Connecte un utilisateur avec ses identifiants",
 *      @OA\RequestBody(
 *          required=true,
 *          description="Données d'authentification de l'utilisateur",
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(property="email", type="string", format="email"),
 *                  @OA\Property(property="password", type="string", format="password"),
 *              ),
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Authentification réussie",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=true),
 *              @OA\Property(property="message", type="string", example="Vous êtes connecté en tant qu'administrateur"),
 *              @OA\Property(property="token", type="string"),
 *              @OA\Property(property="role", type="string", enum={"admin", "donateur", "fondation"}),
 *              @OA\Property(property="datas", type="object"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Identifiants invalides",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=false),
 *              @OA\Property(property="message", type="string", example="Les Identifiants sont invalides"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Compte bloqué, demande refusée, ou en attente de vérification",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=false),
 *              @OA\Property(property="message", type="string", example="Désolé, mais votre compte a été bloqué, votre demande a été refusée ou est en attente de vérification."),
 *          )
 *      ),
 * )
 */

public function login(loginUsersRequest $request){
   
    $verifUser= User::where('email',request('email'))->first();
    
    if($verifUser->role=='fondation'){
        if($verifUser->statut=="enattente"){
            return response()->json([
                "status" => false,
                "message" => "Merci de patienter le temps qu'on verifie la veracité de votre fondation.",
            ]);
        }else if($verifUser->bloque==true){
            return response()->json([
                "status" => false,
                "message" => "Désoler mais votre compte a été bloquer.Merci de rentrer en contact avec L'Admin",
            ]);
        }else if($verifUser->role=='refuse'){
            return response()->json([
                "status" => false,
                "message" => "Désoler mais votre Demande à été Refusée.Merci de rentrer en contact avec L'Admin",
            ]);
        }
        else if($verifUser->is_deleted==true){
            return response()->json([
                "status" => false,
                "message" => "Désoler mais votre compte n'est plus actif.Merci de rentrer en contact avec L'Admin",
            ]);
        }
    }

    if($verifUser->role=='donateur'){
        if($verifUser->bloque==true){
            return response()->json([
                "status" => false,
                "message" => "Désoler mais votre compte a été bloquer.Merci de rentrer en contact avec L'Admin",
            ]);
        }else if($verifUser->is_deleted==true){
            return response()->json([
                "status" => false,
                "message" => "Désoler mais votre compte n'est plus actif.Merci de rentrer en contact avec L'Admin",
            ]);
        }
    }  
    
    
    $credentials = $request->only('email', 'password');

    if(!$token=JWTAuth::attempt($credentials)){
        return response()->json(['status' => 0, 'message' => 'Les Identifiants sont invalides'], 401);
    }


    $user= JWTAuth::user();

     // Filtrer les attributs avec des valeurs non nulles ou non vides
     $userFiltrer = array_filter($user->toArray(), function ($value) {
        return $value !== null && $value !== '';
    });

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
        'datas' => $userFiltrer,
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
/**
 * @OA\Post(
 *     path="/api/approuver/{user}",
 *     summary="Approuver une demande d'inscription pour une fondation",
 *     description="Cette endpoint permet d'approuver une demande en mettant à jour le statut de l'utilisateur.",
 *     operationId="approuverDemande",
 *     tags={"Approuver une demande d'inscription pour une fondation"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur dont la demande sera approuvée",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Demande approuvée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Demande approuvée avec succès"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur de validation des données",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur de validation des données"),
 *         ),
 *     ),
 * )
 */

    public function approuverDemande(User $user){
        $user->statut='accepte';
        if($user->save()){
            Mail::to($user->email)->send(new demandeApprouver($user));
            
            return response()->json([
                "status" => true,
                "message" => "Demande approuvée avec succès"
                
            ]);   
        }
    }
/**
 * @OA\Post(
 *     path="/api/refuserDemande/{user}",
 *     summary="Refuser une demande d'utilisateur",
 *     tags={"Refuser une demande d'inscription pour une fondation"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID de l'utilisateur à refuser"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Demande refusée avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Utilisateur non trouvé")
 *         )
 *     )
 * )
 *
 * Refuser une demande d'utilisateur.
 *
 * @param  \App\Models\User  $user
 * @return \Illuminate\Http\JsonResponse
 */

    public function refuserDemande(User $user){
        $user->statut='refuse';
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "Demande refuser avec succès"
                
            ]);   
        }
    }

   /**
 * @OA\Post(
 *     path="/api/bloquer/{user}",
 *     summary="Bloquer un utilisateur",
 *     tags={"Bloquer une fondation ou un donateur"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID de l'utilisateur à bloquer"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="L'utilisateur a été bloqué avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Utilisateur non trouvé")
 *         )
 *     )
 * )
 *
 * Bloquer un utilisateur.
 *
 * @param  \App\Models\User  $user
 * @return \Illuminate\Http\JsonResponse
 */

    public function bloquer(User $user){
        $user->bloque=true;
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "l'utilisateur a été bloqué  avec succès"
                
            ]);   
        }
    }

  /**
 * @OA\Post(
 *     path="/api/debloquer/{user}",
 *     summary="Débloquer un utilisateur",
 *     tags={"Débloquer une Fondation ou un Donateur"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID de l'utilisateur à débloquer"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="L'utilisateur a été débloqué avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Utilisateur non trouvé")
 *         )
 *     )
 * )
 *
 * Débloquer un utilisateur.
 *
 * @param  \App\Models\User  $user
 * @return \Illuminate\Http\JsonResponse
 */

    public function debloquer(User $user){
        $user->bloque=false;
        if($user->save()){
            return response()->json([
                "status" => true,
                "message" => "l'utilisateur a été debloqué  avec succès"
                
            ]);   
        }
    }


    /**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Déconnexion de l'utilisateur",
 *     tags={"Deconnexion d'un utilisateur"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Déconnexion effectuée avec succès")
 *         )
 *     )
 * )
 *
 * Déconnecte l'utilisateur.
 *
 * @return \Illuminate\Http\JsonResponse
 */

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Deconnexion Effectuée avec Succès',
        ]);
    }

/**
 * @OA\Get(
 *     path="/api/listeDonateur",
 *     summary="Liste de tous les donateurs",
 *     tags={"Liste de tous les donateurs"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la récupération de la liste des donateurs",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de tous les donateurs"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun donateur inscrit pour le moment",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Aucun donateur inscrit pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")) 
 *         )
 *     )
 * )
 *
 * Récupérer la liste de tous les donateurs.
 *
 * @return \Illuminate\Http\JsonResponse
 */

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


 /**
 * @OA\Get(
 *     path="/api/listeFondation",
 *     summary="Liste de toutes les fondations",
 *     tags={"Liste de toutes les fondations"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Succès de la récupération de la liste des fondations",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de toutes les fondations"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune fondation inscrite pour le moment",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Aucune fondation inscrite pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * Récupérer la liste de toutes les fondations.
 *
 * @return \Illuminate\Http\JsonResponse
 */
 

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
                "message" => "Vous avez aucune fondation inscrit pour le moment",
                "data" => []
                
            ]); 
        }
    }


    /**
 * @OA\Get(
 *     path="/api/listeFondations",
 *     summary="Liste des fondations pour les donateurs",
 *     description="Récupère la liste des fondations acceptées pour les donateurs",
 *     operationId="listeFondationPourLesDonateurs",
 *     tags={"Donateur: Liste De Toutes Les Fondations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des fondations récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de tous les fondations"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="other_attribute", type="string"),
 *                    
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune fondation inscrite pour le moment",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'avez aucune fondation inscrite pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         ),
 *     ),
 * )
 */


 public function listeFondationPourLesDonateurs(){
        $listeFondation= User::where('role','fondation')
               ->where('statut','accepte')->get(); 
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
                "message" => "Vous avez aucune fondation inscrit pour le moment",
                "data" => []
                
            ]); 
        }
    }



    /**
 * @OA\put(
 *     path="/api/supprimerCompteDonateur",
 *     summary="Supprimer le compte de l'utilisateur connecté",
 *     tags={"Donateur:Suppression d'un compte  "},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Le compte a été supprimé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Votre Compte a été supprimé avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Vous n'êtes pas autorisé à supprimer ce compte",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à supprimer ce compte")
 *         )
 *     )
 * )
 *
 * Supprimer le compte de l'utilisateur connecté.
 *
 * @return \Illuminate\Http\JsonResponse
 */

    public function supprimerCompteDonateur(){
       
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
 * @OA\put(
 *     path="/api/supprimerCompteFondation",
 *     summary="Supprimer le compte de l'utilisateur connecté",
 *     tags={"Fondation:Suppression d'un compte  "},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Le compte a été supprimé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Votre Compte a été supprimé avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Vous n'êtes pas autorisé à supprimer ce compte",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas autorisé à supprimer ce compte")
 *         )
 *     )
 * )
 *
 * Supprimer le compte de l'utilisateur connecté.
 *
 * @return \Illuminate\Http\JsonResponse
 */
    public function supprimerCompteDonateurFondation(){
       
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
 * @OA\Delete(
 *     path="/api/supprimer/{user}",
 *     summary="Supprimer un utilisateur",
 *     tags={"Suppression d'un donateur ou d'une fondation par l'administrateur"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID de l'utilisateur à supprimer"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="L'utilisateur a été supprimé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="L'utilisateur a été supprimé avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Utilisateur non trouvé")
 *         )
 *     )
 * )
 *
 * Supprimer un utilisateur.
 *
 * @param  \App\Models\User  $user
 * @return \Illuminate\Http\JsonResponse
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
    
    /**
 * @OA\Put(
 *      path="/api/reactiverCompte/{user}",
 *      operationId="reactiverCompte",
 *      tags={"Administrateur: Reactiver Un Compte Supprimer Par Une Fondation Ou Par Un Donateur"},
 *      summary="Réactiver un compte utilisateur",
 *      description="Réactive un compte utilisateur en mettant à jour le champ 'is_deleted' à false.",
 *      security={{ "bearerAuth":{} }},
 *      @OA\Parameter(
 *          name="user",
 *          in="path",
 *          description="ID de l'utilisateur",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Compte réactivé avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=true),
 *              @OA\Property(property="message", type="string", example="Votre Compte a été Réactivé avec succès")
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Utilisateur non trouvé",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example=false),
 *              @OA\Property(property="message", type="string", example="Utilisateur non trouvé")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Non autorisé, veuillez vous connecter avec un jeton valide",
 *          @OA\JsonContent(
 *              @OA\Property(property="error", type="string", example="Unauthenticated")
 *          )
 *      )
 * )
 *
 * @param \App\Models\User $user
 * @return \Illuminate\Http\JsonResponse
 */


    public function reactiverCompte(User $user){
       

        $user->is_deleted=false;

         if($user->update()){
            return response()->json([
                "status" => true,
                "message" => " Compte Réactivé avec succès"
                
            ]);   
         } else {
            return response()->json([
                "status" => false,
                "message" => "Échec de la réactivation du compte"
            ], 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/listeCompteAReactiver",
 *     summary="Liste des comptes à réactiver",
 *     tags={"Administrateur:Listes des comptes à réactiver"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response="200",
 *         description="Liste des comptes à réactiver récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de tous les comptes supprimés"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *         ),
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Aucun compte supprimé trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'avez aucun compte supprimé pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")) 
 *         ),
 *     ),
 * )
 */

    public function listeCompteAReactiver(){
        $listeCompte= User::where('is_deleted',true)->get();

        // Filtrer les attributs non vides avant de les renvoyer
        $listeCompteReactiver = $listeCompte->map(function ($user) {
          return collect($user->toArray())->filter(function ($value) {
              return !is_null($value) && $value !== '';
          })->all();
      });

      if($listeCompteReactiver->isNotEmpty()){
          return response()->json([
              "status" => true,
              "message" => "Liste de tous les comptes Supprimé",
              "data" => $listeCompteReactiver
              
          ]); 
      }else{
          return response()->json([
              "status" => false,
              "message" => "Vous avez aucun compte suprimé pour le moment",
              "data" => []
              
          ]); 
      }
    }

    /**
 * @OA\Get(
 *     path="/api/listeCompteBloquer",
 *     summary="Liste des comptes à débloquer",
 *     tags={"Administrateur:Listes des comptes à débloquer"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response="200",
 *         description="Liste des comptes à débloquer récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de tous les comptes bloqué"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *         ),
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Aucun compte supprimé trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'avez aucun compte bloqué pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")) 
 *         ),
 *     ),
 * )
 */

    public function listeCompteBloquer(){
        $listeBloquer= User::where('bloque',true)->get();

        // Filtrer les attributs non vides avant de les renvoyer
        $listeCompteBloquer = $listeBloquer->map(function ($user) {
          return collect($user->toArray())->filter(function ($value) {
              return !is_null($value) && $value !== '';
          })->all();
      });

      if($listeCompteBloquer->isNotEmpty()){
          return response()->json([
              "status" => true,
              "message" => "Liste de tous les comptes bloqués",
              "data" => $listeCompteBloquer
              
          ]); 
      }else{
          return response()->json([
              "status" => false,
              "message" => "Vous avez aucun compte bloqué pour le moment",
              "data" => []
              
          ]); 
      }
    }



    /**
 * @OA\Get(
 *     path="/api/voirHistoriqueDon",
 *     summary="Historique de tous les dons effectué par les donnateurs",
 *     operationId="voirHistoriqueDesDonsPourUnDonateur",
 *     tags={"Voir l'historique de tous les dons par l'administrateur"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Historique des dons récupéré avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Voici l'historique des dons"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="Montant Donné", type="number", example=100),
 *                     @OA\Property(property="Titre", type="string", example="Titre de la collecte"),
 *                     @OA\Property(property="Description Collecte", type="string", example="Description de la collecte"),
 *                     @OA\Property(property="Date Don Effectué", type="string", format="datetime", example="2024-02-01 12:34:56"),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé. Jeton manquant ou invalide."
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit. Autorisation insuffisante pour accéder à l'historique des dons."
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun historique de dons trouvé."
 *     )
 * )
 */

    public function VoirhistoriqueDesDonsPourUnDonateur()
{
    $user = auth()->user();

    if ($user && $user->role === 'admin') {
        $dons = Payment::all(); 
    } else {
       
        $donateur = auth()->user();
        $dons = $donateur->dons;
    }

    $tableCollecte = [];

    if ($dons->isEmpty()) {
        return response()->json([
            "status" => true,
            "message" => "Aucun historique de dons trouvé",
            'data' => [],
        ]);
    }

    foreach ($dons as $don) {
        if ($don->collecteDeFond) {
            $tableCollecte[] = [
                'Montant Donné' => $don->amount,
                'Titre' => $don->collecteDeFond->titre,
                'Description Collecte' => $don->collecteDeFond->description,
                'Date Don Effectué' => $don->created_at->format('j/m/Y H:i:s'),
            ];
        }
    }

    return response()->json([
        "status" => true,
        "message" => "Voici l'historique des dons",
        'data' => $tableCollecte,
    ]);
}


public function listeDonateurADesDons()
{
    $user = auth()->user();

   
    if ($user && $user->role === 'admin') {
        $collecteFonds = CollecteDeFond::with('dons')->get();
        dd($collecteFonds);
        $data = [];

        if ($collecteFonds->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Aucune collecte avec dons trouvée',
                'data' => [],
            ]);
        }

        foreach ($collecteFonds as $collecteFond) {
            $donsData = [];

            $dons = $collecteFond->dons;

            if ($dons->isEmpty()) {
                $data[] = [
                    'Collecte' => [
                        'Titre' => $collecteFond->titre,
                        'Description' => $collecteFond->description,
                        'Dons' => 'Aucun don associé à cette collecte pour le moment',
                    ],
                ];
            } else {
                foreach ($dons as $don) {
                    $donReçu = $don->amount;
                    $nomDonateur = $don->donateur->nom;
                    $prenomDonateur = $don->donateur->prenom;
                    $telephoneDonateur = $don->donateur->telephone;
                    $donsData[] = [
                        'Montant Donné' => $donReçu,
                        'Nom Donateur' => $nomDonateur,
                        'Prénom Donateur' => $prenomDonateur,
                        'Téléphone Donateur' => $telephoneDonateur
                    ];
                }

                $collecteData = [
                    'Collecte' => [
                        'Titre' => $collecteFond->titre,
                        'Description' => $collecteFond->description,
                        'Dons' => $donsData,
                    ],
                ];

                $data[] = $collecteData;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Liste Collectes De Fonds avec les donateurs associés',
            'data' => $data,
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Accès non autorisé. Vous devez être administrateur.',
            'data' => [],
        ], 403);
    }
}


}
