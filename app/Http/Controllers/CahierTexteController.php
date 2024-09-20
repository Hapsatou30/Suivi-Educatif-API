<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCahierTexteRequest;
use App\Http\Requests\UpdateCahierTexteRequest;
use App\Models\CahierTexte;

class CahierTexteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

   

    /**
     * Methode pour ajouter un cahier de texte
     */
    public function store(StoreCahierTexteRequest $request)
    {
        //ajouter un cahier de texte
        $cahierTexte = CahierTexte::create($request->all());
        return response()->json([
           'message' => 'Cahier de texte créé avec succès',
           'données' => $cahierTexte,
           'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CahierTexte $cahierTexte)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CahierTexte $cahierTexte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCahierTexteRequest $request, CahierTexte $cahierTexte)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CahierTexte $cahierTexte)
    {
        //
    }
}
