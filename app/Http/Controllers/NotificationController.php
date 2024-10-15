<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;

class NotificationController extends Controller
{

     /**
     * Récupérer les notifications de l'utilisateur authentifié.
     */
    public function getUserNotifications()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer les notifications associées à cet utilisateur
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        // Retourner les notifications sous forme de JSON
        return response()->json([
            'message' => 'Notifications récupérées avec succès',
            'données' => $notifications,
            'status' => 200
        ]);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead($id)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Trouver la notification par ID
        $notification = $user->notifications()->findOrFail($id);

        // Mettre à jour les champs is_read et read_at
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Notification marquée comme lue',
            'status' => 200
        ]);
    }


    /**
     * Marquer une notification comme non lue.
     */
    public function markAsUnread($id)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Trouver la notification par ID
        $notification = $user->notifications()->findOrFail($id);

        // Mettre à jour les champs is_read et read_at
        $notification->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return response()->json([
            'message' => 'Notification marquée comme non lue',
            'status' => 200
        ]);
    }
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
    public function store(StoreNotificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
