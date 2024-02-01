<?php

namespace App\Http\Controllers;

use App\Http\Requests\abonnementRequest;
use App\Models\abonnement;
use Illuminate\Http\Request;

class abonnementController extends Controller
{
    
    public function sabonner( $fondationId)
    {
       
        $donateur = auth()->user();

        //  dd($donateur);   
       
        $abonnementExist = abonnement::where('donateur_id', $donateur->id)
            ->where('fondation_id', $fondationId)
            ->first();

        if ($abonnementExist) {
            return response()->json([
                'status' => false,
                'message' => 'Vous êtes déjà abonné à cette fondation.',
            ], 400);
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


}
