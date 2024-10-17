<?php
namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Bulletin;
use App\Models\ClasseEleve;
use App\Models\AnneeClasse; // Assurez-vous que ce modèle existe
use App\Http\Requests\StoreBulletinRequest;
use App\Http\Requests\UpdateBulletinRequest;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function creerBulletinsPourTousLesEleves()
    {
        // Récupérer tous les élèves pour l'année scolaire
        $classeEleves = ClasseEleve::all();
    
        // Définir les périodes
        $periodes = ['1_semestre', '2_semestre'];
    
        // Tableau pour stocker les bulletins créés
        $bulletinsCrees = [];
    
        // Pour chaque élève, créer un bulletin pour chaque période
        foreach ($classeEleves as $classeEleve) {
            foreach ($periodes as $periode) {
                // Vérifier si un bulletin existe déjà pour cette élève et cette période
                $bulletinExiste = Bulletin::where('classe_eleve_id', $classeEleve->id)
                                          ->where('periode', $periode)
                                          ->exists();
    
                // Si le bulletin n'existe pas, on le crée
                if (!$bulletinExiste) {
                    $bulletin = Bulletin::create([
                        'classe_eleve_id' => $classeEleve->id,
                        'periode' => $periode,
                        'moyenne' => 0,  // Valeur par défaut, à mettre à jour plus tard
                        'commentaire' => 'Commentaire par défaut pour ' . $periode,  // À personnaliser plus tard
                    ]);
    
                    // Ajouter le bulletin créé au tableau
                    $bulletinsCrees[] = $bulletin;
                }
            }
        }
    
        // Retourner la réponse JSON avec les bulletins créés
        return response()->json([
            'message' => 'Les bulletins ont été créés avec succès pour tous les élèves.',
            'bulletins' => $bulletinsCrees
        ], 201);
    }

    /**
     * Afficher tous les bulletins.
     */
    public function index()
    {
        $bulletins = Bulletin::all();
        return response()->json($bulletins, 200);
    }

    /**
     * Afficher les bulletins par classeEleve.
     */
    public function getBulletinsParClasseEleve($classeEleveId)
    {
        $bulletins = Bulletin::where('classe_eleve_id', $classeEleveId)->get();
        return response()->json($bulletins, 200);
    }

    /**
     * Afficher les bulletins par anneeClasse.
     */
    public function getBulletinsParAnneeClasse($anneeClasseId)
    {
        // Récupérer tous les élèves de cette année classe
        $classeEleves = ClasseEleve::where('annee_classe_id', $anneeClasseId)->pluck('id');
        
        // Récupérer tous les bulletins des élèves de cette année classe
        $bulletins = Bulletin::whereIn('classe_eleve_id', $classeEleves)->get();
        return response()->json($bulletins, 200);
    }

    // Autres méthodes existantes

    public function store(StoreBulletinRequest $request)
    {
        //
    }

    public function show(Bulletin $bulletin)
    {
        //
    }

    public function edit(Bulletin $bulletin)
    {
        //
    }

    public function update(UpdateBulletinRequest $request, Bulletin $bulletin)
    {
        //
    }

    public function destroy(Bulletin $bulletin)
    {
        //
    }
}
