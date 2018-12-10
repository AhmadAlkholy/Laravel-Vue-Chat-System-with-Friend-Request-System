@extends('layouts.newApp')

@section('styles')
    <meta name="_token" content="{!! csrf_token() !!}" />
    <meta name="chatId" content="{{$chat->id}}">
    
    @foreach( $chat_with as $friend )      
      <meta class="chatWith" name="chatWith{{$friend->id}}" content="{{$friend->id}}">
    @endforeach
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/chat.css') }}">

@endsection

  
@section('content')

    <chat 
      v-bind:messages="messages" 
      v-bind:newmessages="newMessages" 
      v-bind:authors="authors" 
      v-bind:userid="{{ Auth::user()->id }}"   
      v-bind:friendid="{{ $chat->user->where('id', '!=', Auth::user()->id)->first()->id }}"
      v-bind:chatfriendson="chatfriendson"
      v-bind:chats="{{$chats}}" 
      v-bind:chat="chat" 
      v-bind:chatfriends="{{$chatFriends}}"
      v-bind:onlineusers="onlineUsers"

      v-on:changemessages="messages = $event"
      v-on:changenewmessages="newMessages = $event"
      v-on:changeauthors="authors = $event"
      v-on:changechatfriendson="chatfriendson = $event"
      v-on:changechat="chat = $event"
      v-on:changechatId="chatId = $event">
    </chat>   

@endsection