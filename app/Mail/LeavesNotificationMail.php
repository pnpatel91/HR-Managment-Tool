<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeavesNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->leaveData['subject'])->markdown('mail.leavesNotification.view', 
                                                [
                                                    'name' => $this->leaveData['name'],
                                                    'text' => $this->leaveData['body'],
                                                    'leave_id' => $this->leaveData['leave_id'],
                                                    'employee_id' => $this->leaveData['employee_id'],
                                                    'employee_name' => $this->leaveData['employee_name'],
                                                    'receiver_name' => $this->leaveData['receiver_name'],
                                                    'actionUrl'=> $this->leaveData['leaveUrl']
                                                ]);
    }
}
