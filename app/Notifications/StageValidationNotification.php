<?php

namespace App\Notifications;

use App\Models\Saisine;
use App\Models\WorkflowStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StageValidationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Saisine $saisine,
        public WorkflowStage $stage
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
            ->subject('Nouvelle demande en attente de validation : ' . $this->saisine->reference_number)
            ->line('Une nouvelle demande a été assignée à votre étape pour validation.')
            ->line('Demande : ' . $this->saisine->title)
            ->line('Référence : ' . $this->saisine->reference_number)
            ->line('Étape : ' . $this->stage->display_name)
            ->line('Urgence : ' . $this->getUrgencyLabel($this->saisine->urgency_level))
            ->action('Voir la demande', url('/saisines/' . $this->saisine->id))
            ->line('Veuillez examiner et valider cette demande dès que possible.');
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
            'stage_id' => $this->stage->id,
            'stage_name' => $this->stage->display_name,
            'urgency_level' => $this->saisine->urgency_level,
            'message' => 'Nouvelle demande en attente de validation à ' . $this->stage->display_name,
        ];
    }

    /**
     * Get urgency label in French.
     */
    private function getUrgencyLabel(string $level): string
    {
        return match($level) {
            'critical' => 'Critique',
            'priority' => 'Prioritaire',
            'standard' => 'Standard',
            default => ucfirst($level),
        };
    }
}
