<?php

namespace App\Notifications;

use App\Models\Saisine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributorAddedNotification extends Notification implements ShouldQueue
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
            ->subject('Ajouté comme contributeur : ' . $this->saisine->reference_number)
            ->line('Vous avez été ajouté comme contributeur à une saisine.')
            ->line('Demande : ' . $this->saisine->title)
            ->line('Référence : ' . $this->saisine->reference_number)
            ->line('Étape actuelle : ' . $this->saisine->currentStage->display_name)
            ->action('Voir la demande', url('/saisines/' . $this->saisine->id))
            ->line('Vous pouvez maintenant consulter et contribuer à cette demande.');
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
            'message' => 'Vous avez été ajouté comme contributeur à ' . $this->saisine->reference_number,
        ];
    }
}
