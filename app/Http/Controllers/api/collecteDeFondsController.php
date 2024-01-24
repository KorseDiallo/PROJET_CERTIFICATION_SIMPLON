<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\collecteDeFondsRequest;
use App\Http\Requests\modificationProfilRequest;
use App\Http\Requests\modifierCollecteDeFondsRequest;
use App\Models\collecteDeFonds;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;

/**
*@OA\SecurityScheme(
*securityScheme="bearerAuths",
*type="http",
*scheme="bearer",
*bearerFormat="JWT",
*)
*/
class collecteDeFondsController extends Controller
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
 *     path="/api/creerCollecte",
 *     summary="Créer une nouvelle collecte de fonds",
 *     description="Cette endpoint permet à une fondation de créer une nouvelle collecte de fonds.",
 *     operationId="createCollecteDeFonds",
 *     tags={"Creer Une Collecte De Fonds"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données de la collecte de fonds à créer",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="titre", type="string", description="Titre de la collecte"),
 *                 @OA\Property(property="description", type="string", description="Description de la collecte"),
 *                 @OA\Property(property="image", type="file", description="Image de la collecte"),
 *                 @OA\Property(property="objectifFinancier", type="string", description="Objectif financier de la collecte"),
 *                 @OA\Property(property="numeroCompte", type="string", description="Numéro de compte de la collecte"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La Collecte de Fonds a été bien créée",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La Collecte de Fonds a été bien créée"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur lors de la création de la collecte",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur lors de la création de la collecte"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
   
    public function store(collecteDeFondsRequest $request)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId = $fondation->id;

        $collecteDeFond = new collecteDeFonds();
        $collecteDeFond->titre = $request->input('titre');
        $collecteDeFond->description = $request->input('description');
        $collecteDeFond->image = $this->storeImage($request->image);
        $collecteDeFond->objectifFinancier = $request->input('objectifFinancier');
        $collecteDeFond->numeroCompte = $request->input('numeroCompte');
        $collecteDeFond->user_id = $fondationId;

        if ($collecteDeFond->save()) {
            return response()->json([
                "status" => true,
                "message" => "La Collecte de Fonds a été bien crée",
                "data" => $collecteDeFond
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Erreur lors de la création de la collecte",
                "data" => null
            ]);
        }
    }



    private function storeImage($image)
    {
        return $image->store('imagesCollecte', 'public');
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
 * @OA\Post(
 *     path="/api/modifierCollecte/{collecteDeFond}",
 *     summary="Modifier une collecte de fonds",
 *     description="Cette endpoint permet à une fondation de modifier une collecte de fonds existante.",
 *     operationId="updateCollecteDeFonds",
 *     tags={"Modifier Une Collecte De Fonds"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="collecteDeFond",
 *         in="path",
 *         required=true,
 *         description="ID de la collecte de fonds à modifier",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données de la collecte de fonds à modifier",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="titre", type="string", description="Titre de la collecte"),
 *                 @OA\Property(property="description", type="string", description="Description de la collecte"),
 *                 @OA\Property(property="image", type="file", description="Nouvelle image de la collecte"),
 *                 @OA\Property(property="objectifFinancier", type="string", description="Nouvel objectif financier de la collecte"),
 *                 @OA\Property(property="numeroCompte", type="string", description="Nouveau numéro de compte de la collecte"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La Collecte de Fonds a été bien modifiée",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La Collecte de Fonds a été bien modifiée"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur lors de la modification de la collecte de fonds",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur lors de la modification de la collecte de fonds"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="La collecte de fonds ne vous appartient pas",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds ne vous appartient pas"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function update(modifierCollecteDeFondsRequest $request, collecteDeFonds $collecteDeFond)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId = $fondation->id;

        if ($fondationId == $collecteDeFond->user_id) {
            $collecteDeFond->titre = $request->titre;
            $collecteDeFond->description = $request->description;
            if ($request->hasFile("image")) {
                $collecteDeFond->image = $this->storeImage($request->image);
            }
            $collecteDeFond->objectifFinancier = $request->input('objectifFinancier');
            $collecteDeFond->numeroCompte = $request->input('numeroCompte');
            $collecteDeFond->user_id = $fondationId;

            if ($collecteDeFond->update()) {
                // dd($collecteDeFond);
                return response()->json([
                    "status" => true,
                    "message" => "La Collecte de Fonds a été bien modifiée",
                    "data" => $collecteDeFond
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Erreur lors de la modification de la collecte de fonds ",

                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "la collecte de fonds vous appartient pas du coup vous pouvez pas le modifier",
            ]);
        }
    }

    /**
 * @OA\Post(
 *     path="/api/modifierProfil",
 *     summary="Modifier le profil de l'utilisateur connecté",
 *     description="Cette endpoint permet à un utilisateur de modifier son propre profil.",
 *     operationId="modifierProfil",
 *     tags={"Modifier le Profil"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données du profil à modifier",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="nom", type="string", description="Nom "),
 *                 @OA\Property(property="prenom", type="string", description="Prénom "),
 *                 @OA\Property(property="image", type="file", description="Image"),
 *                 @OA\Property(property="description", type="string", description="Description"),
 *                 @OA\Property(property="numeroEnregistrement", type="string", description="Numéro d'enregistrement"),
 *                 @OA\Property(property="adresse", type="string", description="Adresse de l'utilisateur"),
 *                 @OA\Property(property="email", type="string", format="email", description="Email"),
 *                 @OA\Property(property="password", type="string", format="password", description="password"),
 *                 @OA\Property(property="telephone", type="string", description="Téléphone"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Le profil a été modifié avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Le profil a été modifié avec succès"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur lors de la modification du profil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur lors de la modification du profil"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Vous n'êtes pas propriétaire du profil",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas propriétaire du profil"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function modifierProfil(modificationProfilRequest $request)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId = $fondation->id;
        $profil = User::findOrFail($fondationId);
        if ($profil->id == $fondationId) {
            $profil->nom = $request->nom;
            $profil->prenom = $request->prenom;
            if ($request->hasFile("image")) {
                $profil->image = $this->storeImage($request->image);
            }
            $profil->description = $request->description;
            $profil->numeroEnregistrement = $request->numeroEnregistrement;
            $profil->adresse = $request->adresse;
            $profil->email = $request->email;
            $profil->password = bcrypt($request->password);
            $profil->telephone = $request->telephone;
            if ($profil->role == "fondateur") {
                $profil->role = "fondateur";
            } else if ($profil->role == "donateur") {
                $profil->role = "donateur";
            }

            if ($profil->update()) {
                return response()->json([
                    "status" => true,
                    "message" => "Votre profil a été modifié avec succès",
                    "data" => $profil
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Vous n'etes pas propriètaire",
            ]);
        }
    }

    /**
 * @OA\Put(
 *     path="/api/cloturerUneCollecte/{collecteDeFond}",
 *     summary="Clôturer une collecte de fonds",
 *     description="Cette endpoint permet à une fondation de clôturer une collecte de fonds existante.",
 *     operationId="cloturerCollecteDeFonds",
 *     tags={"Clôturer Une Collecte De Fonds"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="collecteDeFond",
 *         in="path",
 *         required=true,
 *         description="ID de la collecte de fonds à clôturer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La collecte de fonds a été clôturée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds a été clôturée avec succès"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur lors de la clôture de la collecte de fonds",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur lors de la clôture de la collecte de fonds"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="La collecte de fonds ne vous appartient pas",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds ne vous appartient pas"),
 *         )
 *     )
 * )
 */


    public function cloturerUneCollecte(collecteDeFonds $collecteDeFond)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId = $fondation->id;


        if ($fondationId == $collecteDeFond->user_id) {
            $collecteDeFond->statut = "cloturer";
            if ($collecteDeFond->save()) {
                return response()->json([
                    "status" => true,
                    "message" => "La collecte de Fonds a été clôturé avec succès"
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Vous n'etes pas propriètaire de cette collecte",
            ]);
        }
    }

    /**
 * @OA\Put(
 *     path="/api/decloturerUneCollecte/{collecteDeFond}",
 *     summary="Déclôturer une collecte de fonds",
 *     description="Cette endpoint permet à une fondation de déclôturer une collecte de fonds existante.",
 *     operationId="decloturerCollecteDeFonds",
 *     tags={"Déclôturer Une Collecte De Fonds"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="collecteDeFond",
 *         in="path",
 *         required=true,
 *         description="ID de la collecte de fonds à déclôturer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La collecte de fonds a été déclôturée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds a été déclôturée avec succès"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur lors du déclôture de la collecte de fonds",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur lors du déclôture de la collecte de fonds"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="La collecte de fonds ne vous appartient pas",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds ne vous appartient pas"),
 *         )
 *     )
 * )
 */


    public function decloturerUneCollecte(collecteDeFonds $collecteDeFond)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId = $fondation->id;

        if ($fondationId == $collecteDeFond->user_id) {
            $collecteDeFond->statut = "encours";
            if ($collecteDeFond->save()) {
                return response()->json([
                    "status" => true,
                    "message" => "La collecte de Fonds a été declôturé avec succès"

                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Vous n'etes pas propriètaire de cette collecte",
            ]);
        }
    }

    /**
 * Récupérer la liste de toutes les collectes de fonds clôturées pour la personne connectée.
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Get(
 *     path="/api/listeCollecteEnCours",
 *     summary="Liste de toutes les collectes de fonds en cours pour la personne connectée",
 *     tags={"Liste de toutes les collectes de fonds en cours"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Liste des collectes de fonds en cours",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de toutes les collectes de fonds en cours"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="autre_propriete", type="string"),
 *                   
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune collecte de fond en cours pour le moment",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'avez aucune collecte de fond en cours pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */

    public function listeCollecteEnCours()
    {
         // personne connectée
         $fondation = auth()->user();
         $fondationId = $fondation->id;

        $listeCollecteEnCours = collecteDeFonds::where('statut', 'encours')
                ->where('user_id',$fondationId)->get();
      

        if ($listeCollecteEnCours->isNotEmpty()) {
            return response()->json([
                "status" => true,
                "message" => "Liste de toutes les collectes de fonds en cours",
                "data" => $listeCollecteEnCours

            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Vous avez aucune collecte de fond en cours pour le moment",
                "data" => []

            ]);
        }
    }

/**
 * Récupérer la liste de toutes les collectes de fonds clôturées pour la personne connectée.
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Get(
 *     path="/api/listeCollecteCloturer",
 *     summary="Liste de toutes les collectes de fonds clôturées pour la personne connectée",
 *     tags={"Liste de toutes les collectes de fonds Cloturer"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Liste des collectes de fonds clôturées",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Liste de toutes les collectes de fonds clôturées"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="autre_propriete", type="string"),
 *                   
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune collecte de fond clôturée pour le moment",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'avez aucune collecte de fond clôturée pour le moment"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */

    public function listeCollecteCloturer()
    {
         // personne connectée
         $fondation = auth()->user();
         $fondationId = $fondation->id;

        $listeCollecteCloturer = collecteDeFonds::where('statut', 'cloturer')
                ->where('user_id',$fondationId)->get();


        if ($listeCollecteCloturer->isNotEmpty()) {
            return response()->json([
                "status" => true,
                "message" => "Liste de toutes les collectes de fonds clôturer",
                "data" => $listeCollecteCloturer

            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Vous avez aucune collecte de fond cloturer pour le moment",
                "data" => []

            ]);
        }
    }


 /**
 * Supprimer une collecte de fonds.
 *
 * @param \App\Models\CollecteDeFonds $collecteDeFond
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Delete(
 *     path="/api/supprimerCollecte/{collecteDeFond}",
 *     summary="Supprimer une collecte de fonds",
 *     tags={"Suppression d'une Collecte de Fonds par une Fondation"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="collecteDeFond",
 *         in="path",
 *         required=true,
 *         description="ID de la collecte de fonds à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La collecte de fonds a été bien supprimée",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds a été bien supprimée"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="La collecte de fonds ne vous appartient pas, vous ne pouvez pas la supprimer",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds ne vous appartient pas, vous ne pouvez pas la supprimer"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="La collecte de fonds n'a pas été trouvée dans la base de données",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La collecte de fonds n'a pas été trouvée dans la base de données"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur lors de la suppression de la collecte de fonds",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur lors de la suppression de la collecte de fonds"),
 *         )
 *     )
 * )
 */
public function destroy(collecteDeFonds $collecteDeFond)
{
    // personne connectée
    $fondation = auth()->user();
    $fondationId = $fondation->id;

    if ($fondationId == $collecteDeFond->user_id) {
        // Vérifier si la collecte de fonds existe avant de la supprimer
        $collecteExiste= collecteDeFonds::findOrFail($collecteDeFond->id);

        if ($collecteExiste) {
            if ($collecteExiste->delete()) {
                return response()->json([
                    "status" => true,
                    "message" => "La Collecte de Fonds a été bien supprimée",
                    "data" => $collecteExiste
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Erreur lors de la suppression de la collecte de fonds",
                ], 500);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "La collecte de fonds n'a pas été trouvée dans la base de données",
            ], 404);
        }
    } else {
        return response()->json([
            "status" => false,
            "message" => "La collecte de fonds ne vous appartient pas, vous ne pouvez pas la supprimer",
        ], 403);
    }
}


}
