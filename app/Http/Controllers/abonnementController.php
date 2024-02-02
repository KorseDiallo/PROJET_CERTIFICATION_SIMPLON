<?php

namespace App\Http\Controllers;

use App\Http\Requests\abonnementRequest;
use App\Models\abonnement;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;


/**
 *@OA\SecurityScheme(
 *securityScheme="bearerAuthsss",
 *type="http",
 *scheme="bearer",
 *bearerFormat="JWT",
 *)
 */

class abonnementController extends Controller
{


    /**
 * @OA\Post(
 *     path="/api/sabonner/{fondationId}",
 *     summary="S'abonner ou se réabonner à une fondation",
 *     description="S'abonner ou se réabonner à une fondation en fonction de l'ID de fondation fourni",
 *     operationId="sabonner",
 *     tags={"Sabonner à une fondation"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="fondationId",
 *         in="path",
 *         required=true,
 *         description="ID de la fondation à laquelle s'abonner",
 *         @OA\Schema(type="integer"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Abonnement réussi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Félicitations, abonnement réussi."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Entrée invalide."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Non autorisé. Jeton non fourni ou invalide."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Interdit",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Interdit. Permissions insuffisantes."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Fondation non trouvée."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur interne du serveur."),
 *         ),
 *     ),
 * )
 */
    
    public function sabonner( $fondationId)
    {
       
        $donateur = auth()->user();
       
        $abonnementExist = abonnement::where('donateur_id', $donateur->id)
            ->where('fondation_id', $fondationId)
            ->first();


        if ($abonnementExist && $abonnementExist->suivre == true) {
            return response()->json([
                'status' => false,
                'message' => 'Vous êtes déjà abonné à cette fondation.',
            ]);
        }else if($abonnementExist && $abonnementExist->suivre == false){
            $abonnementExist->update(['suivre' => true]);
            return response()->json([
                'status' => false,
                'message' => 'Vous êtes réabonné',
            ]);
        }


        abonnement::create([
            'donateur_id' => $donateur->id,
            'fondation_id' => $fondationId,
            'suivre' => true, 
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Felicitation Abonnement Reussi.',
        ]);
    }


    /**
 * @OA\Post(
 *     path="/api/sedesabonner/{fondationId}",
 *     summary="Se désabonner d'une fondation",
 *     description="Se désabonner d'une fondation en fonction de l'ID de fondation fourni",
 *     operationId="sedesabonner",
 *     tags={"Se Desabonner à une fondation"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="fondationId",
 *         in="path",
 *         required=true,
 *         description="ID de la fondation dont se désabonner",
 *         @OA\Schema(type="integer"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Désabonnement réussi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Désabonnement réussi de la fondation."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas abonné à cette fondation."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Non autorisé. Jeton non fourni ou invalide."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Interdit",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Interdit. Permissions insuffisantes."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Fondation non trouvée."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur interne du serveur."),
 *         ),
 *     ),
 * )
 */

    public function sedesabonner($fondationId)
{
    $donateur = auth()->user();

   
    $abonnement = abonnement::where('donateur_id', $donateur->id)
        ->where('fondation_id', $fondationId)
        ->first();

    if (!$abonnement) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'êtes pas abonné à cette fondation.',
        ], 400);
    }

       // Vérification si déjà désabonné
       if (!$abonnement->suivre) {
        return response()->json([
            'status' => false,
            'message' => 'Vous êtes déjà désabonné à cette fondation.',
        ], 400);
    }

   
    $abonnement->update(['suivre' => false]);

    return response()->json([
        'status' => true,
        'message' => 'Désabonnement réussi de la fondation.',
    ]);
}

/**
 * @OA\Get(
 *     path="/api/listeAbonner",
 *     summary="Liste des abonnés à une fondation",
 *     description="Récupère la liste des abonnés à la fondation actuelle",
 *     operationId="listeAbonnerAUneFondation",
 *     tags={"Fondation: Liste des Abonnés à une fondation"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des abonnés récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Voici la liste des Abonnée"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="nom_donateur", type="string", example="John"),
 *                     @OA\Property(property="prenom_donateur", type="string", example="Doe"),
 *                     @OA\Property(property="telephone_donateur", type="string", example="123-456-789"),
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun abonné trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'avez pas encore d'Abonné."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Non autorisé. Jeton non fourni ou invalide."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Interdit",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Interdit. Permissions insuffisantes."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur interne du serveur."),
 *         ),
 *     ),
 * )
 */
public function listeAbonnerAUneFondation(){
    $fondation= auth()->user();
    $abonnements= abonnement::where('fondation_id',$fondation->id)
            ->where('suivre',true)->get();
    $data=[];

    if ($abonnements->isEmpty()) {
        return response()->json([
            "status" => false,
            "message" => "Vous avez pas encore d'Abonné.",
        ]);
    }

    foreach ($abonnements as $abonnement) {
        $data[]=[
            'nom_donateur'=>$abonnement->donateur->nom,
            'prenom_donateur'=>$abonnement->donateur->prenom,
            'telephone_donateur'=>$abonnement->donateur->telephone,
        ];
    }

    return response()->json([
        "status" => true,
        "message" => "Voici la liste des Abonnée",
        'data' => $data,
    ]);
}


/**
 * @OA\Get(
 *     path="/api/listeFondationAbonner",
 *     summary="Liste des fondations auxquelles le donateur est abonné",
 *     description="Récupère la liste des fondations auxquelles le donateur est abonné",
 *     operationId="listeFondationAbonner",
 *     tags={"Donateur:Liste des fondations aux quelles le donateur est abonné"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des fondations récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Voici la liste des fondations dans laquelle vous êtes abonné"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="nom_fondation", type="string", example="Fondation XYZ"),
 *                     @OA\Property(property="description_fondation", type="string", example="Description de la fondation XYZ"),
 *                     @OA\Property(property="telephone_donateur", type="string", example="123-456-789"),
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune fondation trouvée",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Vous n'êtes pas encore abonné à une fondation."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Non autorisé. Jeton non fourni ou invalide."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Interdit",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Interdit. Permissions insuffisantes."),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erreur interne du serveur."),
 *         ),
 *     ),
 * )
 */

public function listeFondationAbonner(){
    $donateur= auth()->user();
    $abonnements= abonnement::where('donateur_id',$donateur->id)
        ->where('suivre',true)->get();
    $data=[];

    if ($abonnements->isEmpty()) {
        return response()->json([
            "status" => false,
            "message" => "Vous n'êtes pas encore abonné à une fondation.",
        ], 404);
    }

    foreach ($abonnements as $abonnement) {
        $data[]=[
            'nom_fondation'=>$abonnement->fondation->nom,
            'description_fondation'=>$abonnement->fondation->description,
            'telephone_donateur'=>$abonnement->fondation->telephone,
        ];
    }

    return response()->json([
        "status" => true,
        "message" => "Voici la liste des fondations dans laquelle vous êtes abonné",
        'data' => $data,
    ]);
}

}
