<?php

namespace App\Notifications;

use App\Models\Saisine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DelayAlertNotification extends Notification implements ShouldQueue
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
        $daysAtStage = $this->saisine->stage_entered_at->diffInDays(now());
        $expectedDuration = $this->saisine->currentStage->getExpectedDuration();

        return (new MailMessage)
            ->subject('Alerte de retard : ' . $this->saisine->reference_number)
            ->line('Une demande a dépassé la durée prévue à son étape actuelle.')
            ->line('Demande : ' . $this->saisine->title)
            ->line('Référence : ' . $this->saisine->reference_number)
            ->line('Étape actuelle : ' . $this->saisine->currentStage->display_name)
            ->line('Jours à cette étape : ' . $daysAtStage . ' (Prévu : ' . $expectedDuration . ')')
            ->line('Urgence : ' . $this->getUrgencyLabel($this->saisine->urgency_level))
            ->action('Voir la demande', url('/saisines/' . $this->saisine->id))
            ->line('Veuillez examiner cette demande de toute urgence pour éviter d\'autres retards.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysAtStage = $this->saisine->stage_entered_at->diffInDays(now());
        $expectedDuration = $this->saisine->currentStage->getExpectedDuration();

        return [
            'saisine_id' => $this->saisine->id,
            'reference_number' => $this->saisine->reference_number,
            'title' => $this->saisine->title,
            'current_stage' => $this->saisine->currentStage->display_name,
            'days_at_stage' => $daysAtStage,
            'expected_duration' => $expectedDuration,
            'urgency_level' => $this->saisine->urgency_level,
            'message' => 'Demande en retard à ' . $this->saisine->currentStage->display_name . ' (' . $daysAtStage . ' jours)',
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
