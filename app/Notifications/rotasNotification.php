<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class rotasNotification extends Notification
{
    use Queueable;
    private $rotaData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($rotaData)
    {
        $this->rotaData = $rotaData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
                    ->greeting($this->rotaData['name'])
                    ->line($this->rotaData['body'])
                    ->action($this->rotaData['text'], $this->rotaData['rotaUrl'])
                    ->line($this->rotaData['thanks']);
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
            'name' => $this->rotaData['name'],
            'text' => $this->rotaData['text'],
            'id' => $this->rotaData['id'],
            'user_id' => $this->rotaData['employee_id'],
            'user_name' => $this->rotaData['employee_name'],
            'receiver_name' => $this->rotaData['receiver_name'],
            'actionUrl'=> $this->rotaData['rotaUrl']
        ];
    }
}
