<?php

namespace App\Notifications;

use App\Models\Saisine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CriticalUrgencyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Saisine $saisine
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('URGENT : Demande critique nécessitant une attention immédiate - ' . $this->saisine->reference_number)
            ->line('Une demande d\'urgence CRITIQUE nécessite votre attention immédiate.')
            ->line('Demande : ' . $this->saisine->title)
            ->line('Référence : ' . $this->saisine->reference_number)
            ->line('Étape actuelle : ' . $this->saisine->currentStage->display_name)
            ->line('Niveau d\'urgence : CRITIQUE')
            ->action('Voir la demande immédiatement', url('/saisines/' . $this->saisine->id))
            ->line('Cette demande nécessite une validation immédiate. Veuillez la prioriser par rapport aux autres tâches.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'saisine_id' => $this->saisine->id,
            'reference_number' => $this->saisine->reference_number,
            'title' => $this->saisine->title,
            'current_stage' => $this->saisine->currentStage->display_name,
            'urgency_level' => 'critical',
            'message' => 'CRITIQUE : ' . $this->saisine->reference_number . ' nécessite une attention immédiate',
        ];
    }
}
