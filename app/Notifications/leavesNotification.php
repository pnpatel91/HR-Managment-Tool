<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class leavesNotification extends Notification
{
    use Queueable;
    private $leaveData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveData)
    {
        $this->leaveData = $leaveData;
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
                    ->greeting($this->leaveData['name'])
                    ->line($this->leaveData['body'])
                    ->action($this->leaveData['text'], $this->leaveData['leaveUrl'])
                    ->line($this->leaveData['thanks']);
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
            'name' => $this->leaveData['name'],
            'text' => $this->leaveData['text'],
            'id' => $this->leaveData['leave_id'],
            'user_id' => $this->leaveData['employee_id'],
            'user_name' => $this->leaveData['employee_name'],
            'receiver_name' => $this->leaveData['receiver_name'],
            'actionUrl'=> $this->leaveData['leaveUrl']
        ];
    }
}
