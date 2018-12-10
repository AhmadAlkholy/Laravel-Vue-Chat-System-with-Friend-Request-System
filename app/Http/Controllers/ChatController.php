<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Chat;
use App\User;
use App\Message;
use App\SeenmessagesUsers;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function markMsgSeen($chat){
        if (count($chat->chatUnseenMessages)>0) {
            SeenmessagesUsers::whereIn('id', $chat->chatUnseenMessages->pluck('id') )
                ->update([
                    'seen'=> true
                ]);
        }
    }

    public function chat($chat_id=null)
    {      

        $user = Auth::user();
        $chats = $user->chat;
        $chat = (is_null($chat_id)) ?$chats->first() :$chats->find($chat_id);
        
        if ( empty($chat) && is_null($chat_id)) return view( 'chat.empty' );
        
        if ( empty($chat) && !is_null($chat_id) ) return redirect('404');

        $chat_with = $chat->user->where('id', '!=', Auth::user()->id);

        $chatFriends = [];
        foreach ($chats as $thread) {
            $chatFriends[] = $thread->user->where('id', '!=', Auth::user()->id)->toArray();
        }
        $chatFriends = json_encode($chatFriends);


        $this->markMsgSeen($chat);

    	return view( 'chat.chat', compact( 'chats', 'chat','user', 'chat_with','chatFriends' ) );
    }

    public function chatUser($user_id)
    {

        $friendId = $user_id;
        $userId = Auth::user()->id;

        if ($friendId == $userId || empty(User::find($user_id)) ) {
            return redirect('404');
        }

        $chat = Chat::query()
                ->whereHas('user', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->whereHas('user', function ($query) use ($friendId) {
                    $query->where('users.id', $friendId);
                })
                ->first();

        if ( empty($chat) ) {
            $chat = new Chat;
            $chat->save();
            $chat->user()->attach( [$userId, $friendId] );
        }

        return redirect('chat/'.$chat->id);
        
    }

    public function getMsg($chat_id=null, Request $request){

        $user = Auth::user();
        $chats = $user->chat;
        $chat = (is_null($chat_id)) ?$chats->first() :$chats->find($chat_id);
        $chat_with = $chat->user->where('id', '!=', Auth::user()->id);
        $messages = $chat->messages;
        $authors = $chat->user;
        
        $this->markMsgSeen($chat);

        return [$messages, $authors, $chat_with, $chat];
    }


}
