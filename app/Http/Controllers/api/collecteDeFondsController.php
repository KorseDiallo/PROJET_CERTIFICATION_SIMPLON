<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\collecteDeFondsRequest;
use App\Models\collecteDeFonds;
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

        if($collecteDeFond){
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
