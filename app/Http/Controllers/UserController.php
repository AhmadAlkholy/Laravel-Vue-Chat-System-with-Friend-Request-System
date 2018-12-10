<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {

    	$user    = User::find($id);
    	$is_auth = ( Auth::user()->id == $id ) ? 1 : 0;

    	return view('user.profile',compact('user', 'is_auth') );
    }

    public function change()
    {
    	return view('user.edit')->withuser( Auth::user() );
    }

    public function saveChanges(Request $request)
    {
    	$validateData = [];
        $user = Auth::user();
    	if ( $user->name != $request->name)
    		$validateData['name'] = 'required|string|max:255';

    	if ( $user->email != $request->email) 
    		$validateData['email'] = 'required|string|email|max:255|unique:users';

    	if ( $user->birth_date != $request->birth_date)
    		$validateData['birth_date'] = 'required|date';

    	if( $request->profile_image )
    		$validateData['profile_image'] = 'image|mimes:jpeg,jpg,png|min:50|max:2000';

    	if( $request->cover_image )
    		$validateData['cover_image'] = 'image|mimes:jpeg,jpg,png|min:50|max:2000';

    	$this->validate($request, $validateData);


        if( $request->profile_image ){
            $img = $request->file( 'profile_image' );

            $newName = time().'.'.$img->getClientOriginalExtension();
            $img->move( public_path('images/profile_images/'.$user->id), $newName );
            $user->img = $newName;
        }
        if( $request->cover_image ){
            $cover_img = $request->file( 'cover_image' );

            $newName = time().'.'.$cover_img->getClientOriginalExtension();
            $cover_img->move( public_path('images/cover_images/'.$user->id), $newName );
            $user->cover_img = $newName;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->birth_date = $request->birth_date;
        $user->save();

        return redirect('/profile/'.$user->id);
    	
    }
}
