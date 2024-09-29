<?php

namespace App\Http\Controllers;

use App\Models\Horaire;
use App\Models\ClasseProf;
use App\Http\Requests\StoreHoraireRequest;
use App\Http\Requests\UpdateHoraireRequest;

class HoraireController extends Controller
{
    /**
     * liste des horaire d'une classe
     */
    public function horaireClasse($anneeClasseId)
    {
        // Récupérer toutes les classes pour l'année donnée, même sans horaires
        $classeProfs = ClasseProf::where('annee_classe_id', $anneeClasseId)
            ->with(['horaires', 'anneeClasse.classe', 'profMatiere.professeur', 'profMatiere.matiere'])
            ->get();
    
        $data = [];
        foreach ($classeProfs as $classeProf) {
            if ($classeProf->horaires->isEmpty()) {
                // Si aucune horaire n'est associée, on indique "Pas encore rempli"
                $data[] = [
                    'nom_classe' => $classeProf->anneeClasse->classe->nom,
                    'matiere' => $classeProf->profMatiere->matiere->nom,
                    'professeur' => $classeProf->profMatiere->professeur->prenom . ' ' . $classeProf->profMatiere->professeur->nom,
                    'horaire' => 'Pas encore rempli',
                    'jour' => '-',
                    'horaire_id' => null, // Pas d'ID car pas encore d'horaire
                    'classe_prof_id' => $classeProf->id // Ajout de l'ID de la classe_prof
                ];
            } else {
                // Si des horaires existent, on les ajoute avec leur ID
                foreach ($classeProf->horaires as $horaire) {
                    $data[] = [
                        'horaire_id' => $horaire->id, // Récupération de l'ID de l'horaire
                        'nom_classe' => $classeProf->anneeClasse->classe->nom,
                        'matiere' => $classeProf->profMatiere->matiere->nom,
                        'professeur' => $classeProf->profMatiere->professeur->prenom . ' ' . $classeProf->profMatiere->professeur->nom,
                        'horaire' => $horaire->heure_debut . ' - ' . $horaire->heure_fin,
                        'jour' => $horaire->jour,
                        'classe_prof_id' => $classeProf->id // Ajout de l'ID de la classe_prof
                    ];
                }
            }
        }
    
        return response()->json([
            'message' => 'Liste des horaires pour l\'année classe',
            'données' => $data,
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
    $classe_prof_id = $request->input('classe_prof_id');
    $jour = $request->input('jour');  // Le jour de la semaine
    $heure_debut = $request->input('heure_debut');
    $heure_fin = $request->input('heure_fin');

    // Vérifier si la classe ou le prof est déjà occupé à cette heure (en fonction de 'classe_prof_id')
    $conflitHoraire = Horaire::where('classe_prof_id', $classe_prof_id)
        ->where('jour', $jour)
        ->where(function($query) use ($heure_debut, $heure_fin) {
            $query->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                  ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                  ->orWhere(function($q) use ($heure_debut, $heure_fin) {
                      $q->where('heure_debut', '<', $heure_debut)
                        ->where('heure_fin', '>', $heure_fin);
                  });
        })
        ->exists();

    if ($conflitHoraire) {
        return response()->json([
            'error' => 'Cette classe ou ce professeur est déjà occupé à cet horaire',
        ], 400);
    }

    // Si pas de conflit, on peut créer l'horaire
    $horaire = Horaire::create($request->all());

    return response()->json([
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
        try {
            $horaire = Horaire::findOrFail($id);
            $horaire->update($request->validated());
    
            return response()->json([
                'message' => 'Horaire modifié avec succès',
                'données' => $horaire,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la modification de l\'horaire',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
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
