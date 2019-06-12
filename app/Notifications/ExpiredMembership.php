<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\VitalGym\Entities\Membership;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExpiredMembership extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * @var Membership
     */
    public $membership;

    /**
     * Create a new notification instance.
     *
     * @param Membership $membership
     */
    public function __construct(Membership $membership)
    {
        $this->membership = $membership;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Membresía Expirada')
                    ->greeting("Hola {$notifiable->full_name}")
                    ->line("Tu membresía {$this->membership->plan->name} ha caducado")
                    ->line("Fecha de inicio: {$this->membership->date_start->toDateString()}")
                    ->line("Fecha de vencimiento: {$this->membership->date_end->toDateString()}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
