<?php

namespace App\Traits;

use App\Models\Notification;

trait NotificationTrait
{
    /**
     * Marque une notification comme lue.
     * 
     * Cette méthode prend en paramètre une instance de Notification et met à jour
     * les champs `is_read` à true et `read_at` avec la date et l'heure actuelles 
     * pour indiquer que la notification a été lue.
     *
     * @param Notification $notification
     * @return void
     */
    public function markNotificationAsRead(Notification $notification)
    {
        // Mise à jour de la notification avec les informations de lecture
        $notification->update([
            'is_read' => true,    // Marque la notification comme lue
            'read_at' => now(),   // Enregistre la date et l'heure de la lecture
        ]);
    }

    /**
     * Envoie une notification personnalisée à un utilisateur.
     * 
     * Cette méthode crée une nouvelle notification pour l'utilisateur spécifié
     * avec le contenu fourni. Elle utilise la relation entre l'utilisateur et 
     * ses notifications pour l'envoi.
     *
     * @param \App\Models\User $user
     * @param string $content
     * @return void
     */
    public function sendNotification($user, $content)
    {
        // Création d'une notification pour l'utilisateur avec le contenu fourni
        $user->notifications()->create([
            'contenu' => $content,  // Texte de la notification
        ]);
        
    }

    /**
     * Récupère les notifications non lues pour un utilisateur.
     * 
     * Cette méthode retourne une collection de notifications qui n'ont pas été
     * lues (`is_read` est à false) pour un utilisateur donné.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications($user)
    {
        // Récupère les notifications où `is_read` est à false (notifications non lues)
        return $user->notifications()->where('is_read', false)->get();
    }
}
