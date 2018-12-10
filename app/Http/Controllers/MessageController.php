<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\SeenmessagesUsers;
use App\Chat;
use Auth;
use App\Events\BroadcastChatEvent;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function new(Request $request)
    {
    	$msg = new Message; // instantiate new Model
		$msg->chat_id = $request->chat_id;
		$msg->author = $request->author;
		$msg->message = $request->message;
		$msg->save();

		foreach (Chat::find($request->chat_id)->user->where('id', '!=', $msg->author) as $user) {
			$seenmsg = new SeenmessagesUsers;
                $seenmsg->chat_id = $msg->chat_id;
                $seenmsg->message_id = $msg->id;
                $seenmsg->user_id = $user->id;
            $seenmsg->save();
            $msg->receiver = $user->id;
		event(new BroadcastChatEvent($msg,$user->id));
		}
	
    	return $msg;
    }

    public function unseen(){
    	return Auth::user()->unseenMessages->pluck('chat_id');
    }

    public function markSeen($message_id){
    	return SeenmessagesUsers::where('message_id', $message_id )
                ->update([
                    'seen'=> true
                ]);;
    }

}
