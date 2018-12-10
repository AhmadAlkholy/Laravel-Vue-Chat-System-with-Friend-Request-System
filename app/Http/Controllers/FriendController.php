<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Friend;
use App\FriendRequest;
use Illuminate\Http\Request;
use Auth;

class FriendController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = Auth::user()->friends();
        $notFriend = false;
        return view('friend.index', compact('users', 'notFriend') );
    }

    public function find()
    {
        // $friends = Auth::user()->friends();
        

        /*$users = ( count( Auth::user()->friends() ) ) ?  DB::select("select DISTINCT users.* from users 
            inner join friends on friends.friend_id = ".Auth::user()->id.
            " WHERE users.id != friends.user_id
            AND users.id !=" .Auth::user()->id)
        : User::where('id','!=',Auth::user()->id)->get() ;*/


        $friends = Auth::user()->friends()->pluck('id');
        $requests = Auth::user()->sentRequests->pluck('receiver');
        
        $users = User::where('id','!=',Auth::user()->id)->get()
                ->whereNotIn('id',  $friends)
                ->whereNotIn('id',  $requests);


        $notFriend = true;
        return view('friend.index', compact('users', 'notFriend') );
    }

    public function requests_list(){
        $users = Auth::user()->receivedRequests;
        FriendRequest::where('receiver', Auth::user()->id)
                ->where('seen', false)
                ->update(['seen' => true]);
        return view('friend.requests', compact('users'));
    }

    
    public function friend_request(Request $request)
    {

        $receiver_id = $request->input('user_id');
        if ( 
            empty($receiver_id) || 
            empty(User::find($receiver_id)) || 
            $receiver_id == Auth::user()->id ||
            !empty( Auth::user()->friends()->find($receiver_id) ) ||
            !empty( FriendRequest::where('sender',Auth::user()->id)->where('receiver',$receiver_id)->first() )
        )
        {
            return 0;
        }

        $friendRequest = new FriendRequest;
        $friendRequest->sender = Auth::user()->id;
        $friendRequest->receiver = $receiver_id;
        $friendRequest->save();

        return 1;

    }

    public function except(Request $request)
    {
        $sender_id = $request->user_id;

        $is_request = count( FriendRequest::where('receiver',Auth::user()->id)->where('sender',$sender_id)->get() );

        if ( 
            empty($sender_id) ||
            empty(User::find($sender_id)) || 
            $sender_id == Auth::user()->id ||
            !empty( Auth::user()->friends()->find($sender_id) ) ||
            !$is_request
        )
        {
            return 0;
        }

        FriendRequest::where("sender", $sender_id)->delete();

        Friend::create([
            'user_id' => $sender_id,
            'friend_id' => Auth::user()->id,
        ]);
        Friend::create([
            'user_id' => Auth::user()->id,
            'friend_id' => $sender_id,
        ]);

        // return  FriendRequest::where("sender",)->first()->sender;
        /*Friend::create([
            'user_id' =>,
            'friend_id' => 
        ]);*/
        return 1;
    }

    public function remove(Request $request)
    {
        $friend_id =  $request->input('user_id');
        $user_id = Auth::user()->id;
        
        if (
            empty($friend_id) || 
            empty(User::find($friend_id)) ||
            $friend_id == Auth::user()->id ||
            empty( Auth::user()->friends()->find($friend_id) )
        ) 
        {
            return 0;
        }

        Friend::where('user_id', $friend_id)->where('friend_id', $user_id)->delete();
        Friend::where('user_id', $user_id)->where('friend_id', $friend_id)->delete();

        return 1;

    }



}
