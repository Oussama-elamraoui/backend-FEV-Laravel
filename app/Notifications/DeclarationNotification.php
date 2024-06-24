<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeclarationNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public $id_dec;
    public $id_med;
    public $message;
    public $notifiableEmp;

    public function __construct($id_dec, $id_med, $message)
    {
        $this->id_dec = $id_dec;
        $this->id_med = $id_med;
        $this->message = $message;
    }

    public function toDatabase($notifiable)
    {
        return [
            'id_dec' => $this->id_dec,
            'id_med' => $this->id_med,
            'message' => $this->message,
            'notifiableEmp' => $this->notifiableEmp,
        ];
    }



    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id_dec' => $this->id_dec,
            'id_med' => $this->id_med,
            'message' => $this->message,
            'notifiableEmp' => $this->notifiableEmp,
        ]);
    }

    // public function broadcastOn()
    // {
    //     return ['notification-declaration'];
    // }

    // public function broadcastAs()
    // {
    //     return 'declaration-notif';
    // }


    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
