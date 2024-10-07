<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EleveCreated extends Mailable
{
    use Queueable, SerializesModels;
    
    public $eleve;
    public $password;
    /**
     * Create a new message instance.
     */
    public function __construct($eleve, $password)
    {
        $this->eleve = $eleve;
        $this->password = $password;
    }


    /**
     * Get the message content definition.
     */
    public function build()
    {
        $emailContent = '
        <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <h2 style="color: #4862C4;">Bonjour ' . $this->eleve->prenom . ',</h2>
            <p>Nous sommes ravis de vous informer que votre compte a été créé avec succès. Voici vos informations de connexion :</p>
            
            <ul style="list-style-type: none; padding: 0;">
                <li><strong>Adresse e-mail :</strong> ' . $this->eleve->user->email . '</li>
                <li><strong>Mot de passe :</strong> ' . $this->password . '</li>
            </ul>
            
            <p>Pour des raisons de sécurité, nous vous recommandons de <strong>changer votre mot de passe</strong> après votre première connexion.</p>
            <p>Vous pouvez vous connecter à votre compte en utilisant le lien suivant : <a href="https://votre-site.com" style="color: #4862C4;">Se connecter</a></p>
            
            <p>Cordialement, SAMA ECOLE</p>
            
        </div>';
    
        return $this->subject('Création de compte eleve')
                    ->html($emailContent);
    }
    

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}