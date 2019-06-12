<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\VitalGym\Entities\Membership;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MembershipPaymentReminder extends Notification implements ShouldQueue
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
                    ->subject('Recordatorio pago membresía')
                    ->greeting("Hola {$notifiable->full_name}")
                    ->line("Solo queríamos recordarte que tu membresía {$this->membership->plan->name} va a expirar muy pronto")
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
