<template>

  <div class="chat-container clearfix">
    <div class="people-list" id="people-list">
      <!-- <div class="search">
        <input type="text" placeholder="search" />
        <i class="fa fa-search"></i>
      </div> -->

    <ul class="list">
      
      
        <li v-for="(chatfriend, index) in chatfriends" v-on:click="getThread(chats[index].id)" :id="'thread'+chats[index].id" class="chathead clearfix" v-bind:class="(chat.id == chats[index].id) ?'active' :''">

          <div v-if="chats[index].group_name" style="position:relative">

            <span class="notification-alert" v-if="newmessages.includes(chats[index].id)">
              {{newmessages.filter(function(value){
                    return value === chats[index].id;
                }).length  }}
            </span>

            <img :alt="chats[index].group_name" src="/images/group_chat.png"/>

            <div class="about">
              <div class="name">{{chats[index].group_name}}</div>
              <div class="status">Group Chat</div>             
            </div>
            
          </div>

          <div v-else v-for="chatfriend in chatfriend" style="position:relative">

            <span class="notification-alert" v-if="newmessages.includes(chats[index].id)">
              {{newmessages.filter(function(value){
                    return value === chats[index].id;
                }).length  }}
            </span>
            
            <div>
              <img :alt="chatfriend.name" :src="'/images/profile_images/'+chatfriend.id+'/'+chatfriend.img"  v-if="chatfriend.img"/>
              <img :alt="chatfriend.name" src="/images/profile_images/no-profile-picture.jpg"  v-else />
            </div>         

            <div class="about">
              <div class="name">{{chatfriend.name}}</div>
              <div class="status">

                <onlinestatus v-bind:onlineusers="onlineusers" v-bind:userid="chatfriend.id"></onlinestatus>
                
              </div>
            </div>

          </div>

        </li>
       
    </ul>
  </div>

  <div class="chat">
    <div class="chat-line">

      <div class="chat-header clearfix">
          <div style="display: inline-block;margin-right:20px;" v-for="chatWith in chatfriendson">
            <img src=""  :alt="chatWith.name" :src="'/images/profile_images/'+chatWith.id+'/'+chatWith.img"  v-if="chatWith.img"/>
            <img src=""  :alt="chatWith.name" src="/images/profile_images/no-profile-picture.jpg"  v-else />
            <div class="chat-about">
              <div class="chat-with">Chat with <a :title="'see '+chatWith.name+'\'s profile'" :href="'/profile/'+chatWith.id">{{chatWith.name}}</a></div>

              <onlinestatus v-bind:onlineusers="onlineusers" v-bind:userid="chatWith.id"></onlinestatus>

            </div>
          </div>

          <p v-if="chat.group_name" style="text-align:center;font-size:20px;font-weight:bold;margin-top:30px;"><a href="" onclick="event.preventDefault()">Add members to chat</a></p>

      </div> <!-- end chat-header -->   

      <div class="chat-history" id="chat-history" style="overflow-y: scroll;">
        <div v-if="messages.length != 0">

            <ul v-for="msg in messages">

                <li class="clearfix" v-if="msg.author == userid">
                  <div class="message-data align-right">
                    <span class="message-data-time" >{{msg.created_at}}</span> &nbsp; &nbsp;
                    <span class="message-data-name" >You</span> <i class="fa fa-circle me"></i>
                    
                  </div>
                  <div class="message other-message float-right">
                    {{msg.message}}
                  </div>
                </li>

                <li v-else>
                  <div class="message-data">
                    <span class="message-data-name">
                      <i class="fa fa-circle" :class="(CheckUserOnline(msg.author))? 'online' :'offline'"></i> 
                      {{authors.filter(function(value){
                          return value.id === msg.author;
                      })[0].name  }}
                    </span>
                    <span class="message-data-time">{{msg.created_at}}</span>
                  </div>
                  <div class="message my-message">
                    {{msg.message}}
                  </div>
                </li>

            </ul>
        </div>
        <div v-else>
            There are no Messages
        </div>

        <chat-sender v-bind:userid="userid" v-bind:chatid="chat.id" v-bind:messages="messages"></chat-sender>
      </div>
      
    </div> <!-- end chat -->
    
  </div> <!-- end container -->
  
  </div> <!-- end container -->

</template>

<script>
    export default {
        props: ['messages', 'newmessages', 'authors', 'userid', 'friendid', 'chatfriendson', 'chatfriends', 'chats', 'chat', 'onlineusers'],
        methods: {
          
          getThread: function(thread_id) {

            if ($('#thread'+thread_id).is('.active')) return 0;

            axios.post('/chat/'+thread_id).then( (response)=>{

              this.$emit( 'changemessages', response.data[0] );
              this.$emit( 'changeauthors', response.data[1] );
              this.$emit( 'changechatfriendson', response.data[2] );
              this.$emit( 'changechat', response.data[3] );
              this.$emit( 'changechatId', response.data[3].id );
              this.$emit( 'changenewmessages' , this.newmessages.filter(function(n) { return n !== thread_id }));

              $('meta[name="chatId"]').attr('content', thread_id);
              $('.people-list ul.list li.active').removeClass('active');
              $('#thread'+thread_id).addClass('active');

              if(document.location.href.search("chat") == -1)
                window.history.replaceState("", "","chat/"+thread_id);
              else window.history.replaceState("", "",thread_id);

            });
          },
          CheckUserOnline: function(userid){
            return _.find(this.onlineusers, {id: userid})
          }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

