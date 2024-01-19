<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\collecteDeFondsRequest;
use App\Models\collecteDeFonds;
use App\Models\User;
use Illuminate\Http\Request;

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
     * Store a newly created resource in storage.
     */
    public function store(collecteDeFondsRequest $request)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId= $fondation->id;

        $collecteDeFond = new collecteDeFonds();
        $collecteDeFond->titre = $request->input('titre');
        $collecteDeFond->description = $request->input('description');
        $collecteDeFond->image = $this->storeImage($request->image);
        $collecteDeFond->objectifFinancier= $request->input('objectifFinancier');
        $collecteDeFond->numeroCompte= $request->input('numeroCompte');
        $collecteDeFond->user_id = $fondationId;

        if($collecteDeFond->save()){
            return response()->json([
                "status" => true,
                "message" => "La Collecte de Fonds a été bien crée",
                "data" => $collecteDeFond
            ]);
        }else{
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, collecteDeFonds $collecteDeFond)
    {
         // personne connectée
         $fondation = auth()->user();
         $fondationId= $fondation->id;

         if($fondationId==$collecteDeFond->user_id){
            $collecteDeFond->titre=$request->titre;
            $collecteDeFond->description=$request->description;
            if($request->hasFile("image")){
                $collecteDeFond->image=$this->storeImage($request->image);
            }
            $collecteDeFond->objectifFinancier=$request->input('objectifFinancier');
            $collecteDeFond->numeroCompte=$request->input('numeroCompte');
            $collecteDeFond->user_id = $fondationId;

            if($collecteDeFond->update()){
                // dd($collecteDeFond);
                return response()->json([
                    "status" => true,
                    "message" => "La Collecte de Fonds a été bien modifiée",
                    "data" => $collecteDeFond
                ]);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Erreur lors de la modification de la collecte de fonds ",
                   
                ]);
            }


         }else{
            return response()->json([
                "status" => false,
                "message" => "la collecte de fonds vous appartient pas du coup vous pouvez pas le modifier",
            ]);
         }

         
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(collecteDeFonds $collecteDeFond)
    {
        // personne connectée
        $fondation = auth()->user();
        $fondationId= $fondation->id;

        if($fondationId==$collecteDeFond->user_id){
            if($collecteDeFond->delete()){
                return response()->json([
                    "status" => true,
                    "message" => "La Collecte de Fonds a été bien supprimée",
                    "data" => $collecteDeFond
                ]);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Erreur lors de la suppression de la collecte de fonds ",
                   
                ]);
            }
        }else{
            return response()->json([
                "status" => false,
                "message" => "la collecte de fonds vous appartient pas du coup vous pouvez pas la supprimer",
            ]);
        }
    }
}
