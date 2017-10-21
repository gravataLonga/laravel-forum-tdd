<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class UserNotificationsController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function index(User $user)
    {
        return auth()->user()->unreadNotifications;
    }
	
    public function destroy(User $user, DatabaseNotification $notification)
    {
        // If some one guess an ID for the DatabaseNotification, maybe can read other
        // notification.
    	$notification->markAsRead();
    	if (request()->wantsJson()) {
    		return response(['status' => 'The notification was marked has read'], 200);
    	}

    	return redirect('/threads');
    }
}
