<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Presence;
use App\Models\ClasseEleve;
use Illuminate\Http\Request;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;

class PresenceController extends Controller
{
    use NotificationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index($classProfId)
    {
        // Récupérer les absences pour la classe avec les informations des élèves, du professeur et de la matière
        $absences = Presence::where('classe_prof_id', $classProfId)
            ->where('status', 'absent')
            ->with([
                'classeEleve.eleve',
                'classeProf.profMatiere.professeur',
                'classeProf.profMatiere.matiere'
            ])
            ->get();

        // Retourner la liste des absences avec les infos des élèves, du prof et de la matière
        return response()->json([
            'message' => 'Liste des absences pour ce cours',
            'data' => $absences,
            'status' => 200
        ]);
    }



    public function getAbsences($classeEleveId)
    {
        // Récupérer les absences de l'élève spécifié
        $absences = Presence::where('classe_eleve_id', $classeEleveId)
            ->where('status', 'absent')
            ->with([
                'classeProf.profMatiere.professeur',
                'classeProf.profMatiere.matiere'
            ])
            ->get();

        // Structurer la réponse en JSON
        return response()->json([
            'message' => 'Liste des absences par eleve',
            'données' => $absences,
            'status' => 200
        ]);
    }
    public function store(StorePresenceRequest $request)
    {
        // Récupérer les données validées sans date_absence
        $validated = $request->validated();

        // Ajouter la date_absence automatiquement
        $validated['date_absence'] = now(); // Ajouter la date actuelle

        // Vérifier si le statut est "absent"
        if ($validated['status'] === 'absent') {
            // Créer une nouvelle absence
            $absence = Presence::create($validated);


            // Récupérer l'élève via la relation avec 'classe_eleve'
            $classeEleve = ClasseEleve::find($validated['classe_eleve_id']);

            if ($classeEleve && $classeEleve->eleve && $classeEleve->eleve->parent) {
                // Récupérer l'élève et son parent
                $eleve = $classeEleve->eleve;
                $parent = $eleve->parent;

                // Récupérer l'utilisateur associé au parent
                $userParent = $parent->user;

                // Envoyer la notification au parent
                $this->sendNotification($userParent, "Votre enfant {$eleve->prenom} est absent le {$validated['date_absence']}.");

                // Retourner une réponse JSON avec succès
                return response()->json([
                    'message' => 'Absence attribuée avec succès et notification envoyée au parent.',
                    'data' => $absence,
                    'status' => 201
                ]);
            } else {
                // Si l'élève ou le parent n'existe pas, retourner une erreur
                return response()->json([
                    'message' => "Impossible d'envoyer la notification : élève ou parent introuvable.",
                    'status' => 404
                ]);
            }
        }

        // Si le statut n'est pas "absent", retourner une erreur
        return response()->json([
            'message' => 'Le statut doit être "absent" pour enregistrer une absence.',
            'status' => 400
        ]);
    }
    public function getAbsencesSemaine()
    {
        // Déterminer les dates de début et de fin de la semaine courante
        $debutSemaine = now()->startOfWeek(); // Début de la semaine (lundi)
        $finSemaine = now()->endOfWeek(); // Fin de la semaine (dimanche)

        // Récupérer les absences de la semaine courante avec les informations des élèves, du professeur et de la matière
        $absences = Presence::where('status', 'absent')
            ->whereBetween('date_absence', [$debutSemaine, $finSemaine]) // Filtrer par date dans la semaine courante
            ->get();

        // Retourner la liste des absences de la semaine courante
        return response()->json([
            'message' => 'Liste des absences de la semaine courante',
            'data' => $absences,
            'status' => 200
        ]);
    }



    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     //voir details d'une absence
    //     $presence = Presence::find($id);
    //     if (!$presence) {
    //         return response()->json([
    //            'message' => 'Absence introuvable',
    //            'status' => 404
    //         ]);
    //     }
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Récupérer l'absence par ID
        $absence = Presence::find($id);
        if (!$absence) {
            return response()->json([
                'message' => 'Absence introuvable',
                'status' => 404
            ]);
        }
    
        // Mettre à jour les données de l'absence
        $absence->motif = $request->input('motif', $absence->motif);
    
        // Vérifier si un nouveau fichier de justification a été uploadé
        if ($request->hasFile('justification')) {
            // Supprimer l'ancien fichier
            if ($absence->justification) {
                Storage::disk('public')->delete($absence->justification);
            }
    
            // Stocker la nouvelle justification
            $justificationPath = $request->file('justification')->store('justifications', 'public');
            $absence->justification = $justificationPath; // Stocke le chemin relatif de l'image
    
            // Sauvegarder les modifications
            $absence->save();
    
            // Envoyer une notification au professeur associé
            if ($absence->classeProf && $absence->classeEleve) {
                // Récupérer le professeur via la relation `profMatiere`
                $prof = $absence->classeProf->profMatiere->professeur;
                $userProf = $prof->user;
    
                // Récupérer le nom de l'élève
                $eleve = $absence->classeEleve->eleve; 
    
                // Envoyer la notification avec le nom de l'élève
                $this->sendNotification($userProf, "Un parent a soumis une justification pour l'absence de l'élève : " . $eleve->nom);
    
                // Retourner une réponse JSON
                return response()->json([
                    'message' => 'Absence mise à jour avec succès et notification envoyée au professeur.',
                    'data' => $absence,
                    'status' => 200
                ]);
            }
        }
    
        // Sauvegarder les modifications si aucune justification n'a été fournie
        $absence->save();
    
        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Absence mise à jour avec succès.',
            'data' => $absence,
            'status' => 200
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $presence = Presence::findOrFail($id);

            $presence->delete();

            DB::commit();

            return response()->json([
                'message' => 'Absence supprimée avec succès.',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la suppression de l\'absence.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function getAbsencesParAnneeClasse($anneeClasseId)
    {
        // Récupérer les absences pour l'année de classe spécifiée
        $absences = Presence::whereHas('classeProf', function ($query) use ($anneeClasseId) {
            $query->where('annee_classe_id', $anneeClasseId);
        })
            ->where('status', 'absent')
            ->with([
                'classeEleve.eleve',
                'classeProf.profMatiere.professeur',
                'classeProf.profMatiere.matiere'
            ])
            ->get();

        // Retourner la liste des absences avec les infos des élèves, du prof et de la matière
        return response()->json([
            'message' => 'Liste des absences pour l\'année de classe spécifiée',
            'data' => $absences,
            'status' => 200
        ]);
    }
    //liste des absences pour les élèves par parent
    public function getAbsencesParParent($parentId)
    {
        // Récupérer tous les élèves du parent spécifié
        $eleves = Eleve::where('parent_id', $parentId)->pluck('id');

        // Récupérer les absences des élèves de ce parent
        $absences = Presence::whereHas('classeEleve', function ($query) use ($eleves) {
            $query->whereIn('eleve_id', $eleves);
        })
            ->where('status', 'absent')
            ->with([
                'classeEleve.eleve',
                'classeProf.profMatiere.professeur',
                'classeProf.profMatiere.matiere'
            ])
            ->get();

        // Retourner les absences avec les informations des élèves, du professeur et de la matière
        return response()->json([
            'message' => 'Liste des absences pour les enfants du parent spécifié',
            'data' => $absences,
            'status' => 200
        ]);
    }
}
