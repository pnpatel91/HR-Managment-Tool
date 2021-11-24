<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Rota;
use App\Rota_template;
use App\User;
use App\Branch;
use App\Holiday;
use App\Leave;

class RotasNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($rotaData)
    {
        $this->rotaData = $rotaData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        return $this->subject($this->rotaData['subject'])->markdown('mail.rotasNotification.view', 
                                                [
                                                    'name' => $this->rotaData['name'],
                                                    'text' => $this->rotaData['body'],
                                                    'id' => $this->rotaData['id'],
                                                    'employee_id' => $this->rotaData['employee_id'],
                                                    'employee_name' => $this->rotaData['employee_name'],
                                                    'receiver_name' => $this->rotaData['receiver_name'],
                                                    'actionUrl'=> $this->rotaData['rotaUrl']
                                                ]);
    }
}
