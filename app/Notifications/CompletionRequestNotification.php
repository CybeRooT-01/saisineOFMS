<?php

namespace App\Notifications;

use App\Models\Saisine;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompletionRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Saisine $saisine,
        public User $requestor,
        public string $reason
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
            ->subject('Complément demandé : ' . $this->saisine->reference_number)
            ->line('Des informations complémentaires ont été demandées pour votre saisine.')
            ->line('Demande : ' . $this->saisine->title)
            ->line('Référence : ' . $this->saisine->reference_number)
            ->line('Demandé par : ' . $this->requestor->name)
            ->line('Raison : ' . $this->reason)
            ->action('Voir la demande', url('/saisines/' . $this->saisine->id))
            ->line('Veuillez fournir les informations demandées pour continuer le processus de validation.');
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
            'requestor_id' => $this->requestor->id,
            'requestor_name' => $this->requestor->name,
            'reason' => $this->reason,
            'message' => 'Complément demandé par ' . $this->requestor->name,
        ];
    }
}
