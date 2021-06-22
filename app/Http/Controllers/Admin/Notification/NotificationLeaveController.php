<?php

namespace App\Http\Controllers\Admin\Notification;

use Illuminate\Http\Request;
use App\User;
use Notification;
use App\Notifications\leavesNotification;

class NotificationLeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    public function index()
    {
        return view('product');
    }
    
    public function sendLeaveNotification() {
        $userSchema = User::first();
  
        $leaveData = [
            'name' => 'BOGO',
            'body' => 'You received an offer.',
            'thanks' => 'Thank you',
            'offerText' => 'Check out the offer',
            'offerUrl' => url('/'),
            'offer_id' => 007
        ];
  
        Notification::send($userSchema, new leavesNotification($leaveData));
    }
}