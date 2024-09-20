<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHoraireRequest;
use App\Http\Requests\UpdateHoraireRequest;
use App\Models\Horaire;

class HoraireController extends Controller
{
    /**
     * liste des horaire d'une classe
     */
    public function horaireClasse($anneeClasseId)
    {
        // Récupérer les horaires pour une classe spécifique via l'année de la classe
        $horaires = Horaire::whereHas('classeProf', function ($query) use ($anneeClasseId) {
            $query->where('annee_classe_id', $anneeClasseId);
        })->get();
    
        // Vérifier si des horaires existent pour l'année classe donnée
        if ($horaires->isEmpty()) {
            return response()->json([
                'message' => 'Aucun horaire trouvé pour cette année classe.',
                'status' => 404
            ]);
        }
    
        return response()->json([
            'message' => 'Liste des horaires pour l\'année classe',
            'données' => $horaires,
            'status' => 200
        ]);
    }
    
   
    //horaires pour un prof
    public function horaireProf($professeurId)
    {
        // Récupérer les horaires en fonction du professeur via la relation prof_matiere
        $horaires = Horaire::whereHas('classeProf.profMatiere', function ($query) use ($professeurId) {
            $query->where('professeur_id', $professeurId); // Assurez-vous que la colonne 'prof_id' existe dans 'prof_matiere'
        })->get();
    
        // Vérifier s'il y a des horaires pour ce professeur
        if ($horaires->isEmpty()) {
            return response()->json([
                'message' => 'Aucun horaire trouvé pour ce professeur.',
                'status' => 404
            ]);
        }
    
        return response()->json([
            'message' => 'Liste des horaires pour le professeur',
            'données' => $horaires,
            'status' => 200
        ]);
    }
    

    /**
     * Methode  ajouter une horaire
     */
    public function store(StoreHoraireRequest $request)
    {
        //ajouter une horaire
        $horaire = Horaire::create($request->all());
        return response()->json ([
           'message' => 'Horaire créé avec succès',
           'données' => $horaire,
           'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Horaire $horaire)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHoraireRequest $request, $id)
    {
        //modifier une horaire
        $horaire = Horaire::find($id);
        $horaire->update($request->all());
        return response()->json([
           'message' => 'Horaire modifié avec succès',
           'données' => $horaire,
           'status' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //supprimer une horaire
        $horaire = Horaire::find($id);
        $horaire->delete();
        return response()->json([
           'message' => 'Horaire supprimé avec succès',
           'status' => 204
        ]);
    }
}
