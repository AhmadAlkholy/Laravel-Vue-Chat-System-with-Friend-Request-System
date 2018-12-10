@extends('layouts.newApp')

@section('styles')

  <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/chat.css') }}">

@endsection

  
@section('content')

<div class="chat-container clearfix">
  

<div class="chat" style="width:100%;height:100vh">
  <div class="chat-line">

    <div class="chat-header clearfix">
        
    </div> <!-- end chat-header -->   

    <br>
    <h2 style="font-size:1.5em;text-align:center;">You have no messages</h2>
    
  </div> <!-- end chat -->
  
</div> <!-- end container -->

</div> <!-- end container -->

@endsection

