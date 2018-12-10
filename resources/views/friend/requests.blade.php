@extends('layouts.app')

@section('styles')
	<meta name="_token" content="{!! csrf_token() !!}" />
	<style type="text/css">
		.list-content{
		 min-height:300px;
		}
		.list-content .list-group .title{
		  background:#5bc0de;
		  border:2px solid #DDDDDD;
		  font-weight:bold;
		  color:#FFFFFF;
		}
		.list-group-item img {
		    height:80px; 
		    width:80px;
		}

		.jumbotron .btn {
		    padding: 5px 5px !important;
		    font-size: 12px !important;
		}
		.prj-name {
		    color:#5bc0de;    
		}
		.break{
		    width:100%;
		    margin:20px;
		}
		.name {
		    color:#5bc0de;    
		}
		.btn{color: white;font-weight: 500}
	</style>
@endsection
@section('content')
	

<link href="http://getbootstrap.com/examples/jumbotron-narrow/jumbotron-narrow.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
<div class="container bootstrap snippet">

  <div class="header">
    <h3 class="text-muted prj-name">
        <span class="fa fa-users fa-2x principal-title"></span>
        Friend Requests
    </h3>
  </div>


  <div class="jumbotron list-content">
    <ul class="list-group">
      <li href="#" class="list-group-item title">
        List of Friend Requests
      </li>
      @forelse($users as $user)
	      <li href="#" class="list-group-item text-left user-{{$user->id}}">
	        <img class="img-thumbnail" src="@if( !is_null($user->img) ) {{ URL::asset('images/profile_images') }}/{{$user->id}}/{{$user->img}} @else {{ URL::asset('images/profile_images/no-profile-picture.jpg') }} @endif">
	        <label class="name">
	            <a href="{{url('profile')}}/{{$user->id}}">{{ $user->name }}</a> <br>
	        </label>
	        <label class="pull-right" style="line-height: 90px;">
	        	
				<button class="except-{{$user->id}} btn btn-info  btn-xs" onclick="except_friend({{$user->id}})">Confirm</button>

	            <a  class="btn btn-success btn-xs glyphicon glyphicon-ok" href="{{url('profile')}}/{{$user->id}}" title="View">View Profile</a>
	            <!-- <a  class="btn btn-danger  btn-xs glyphicon glyphicon-trash" href="#" title="Delete">Reject</a> -->
	            <a  class="btn btn-info btn-xs" href="{{url('chat/user')}}/{{$user->id}}" title="Send message">Send a Message</a> 
	        </label>
	        <div class="break"></div>
	      </li>
      	@empty
			<div class="text-center">
				<h3>
					You don't have pending Friend Requests
				</h3>
			</div>
		@endforelse
    </ul>
  </div>
  </div>
</div>                                                                                


@endsection

@section('scripts')
	<script type="text/javascript">
		function except_friend(user_id){
			var btn = $('.except-'+user_id);
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			    }
			});
			$.ajax({
                url: "{{url('except_friend')}}",
                type: 'POST',
                data:{'user_id':user_id},
                success: function (response) {
                    if(response == 1){
                    	btn.remove();
                    	$('.list-group li.user-'+user_id+' label.pull-right').prepend('<button class="remove-'+user_id+' btn btn-danger  btn-xs" onclick="remove_friend('+user_id+')">Unfriend</button>')
                    	alert("Friend Request has been Excepted");
                    }
                    else{
                    	alert("sorry some error occured and friend request wasn't Excepted");
                    }
                    
                }
            });
		}

		function remove_friend(user_id){

			var btn = $('.remove-'+user_id);
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			    }
			});
			$.ajax({
		        url: "{{url('remove_friend')}}",
		        type: 'POST',
		        data:{'user_id':user_id},
		        success: function (response) {
		            if(response == 1){
		            	btn.removeClass('remove-'+user_id);
		            	btn.removeClass('btn-danger');
		            	btn.addClass('btn-success');
		            	btn.addClass('add-'+user_id);
		            	btn.attr('onclick', 'add_friend('+user_id+')');
		            	btn.html('Add Friend');
		            }
		            else{
		            	alert("sorry some error occured and friend wasn't sent");
		            }
		            
		        }
		    });

		}
		function add_friend(user_id) {

		var btn = $('.add-'+user_id);
		if (btn.is('.added')) {
			return 0;
		}
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		    }
		});
		$.ajax({
	        url: "{{url('add_friend')}}",
	        type: 'POST',
	        data:{'user_id':user_id},
	        success: function (response) {
	            if(response == 1){
	            	btn.html('Friend Request Sent');
	            	btn.addClass('added');
	            	btn.prop('disabled', true);
	            }
	            else{
	            	alert("sorry some error occured and friend request wasn't sent");
	            }
	            
	        }
	    });
	}

	</script>
@endsection