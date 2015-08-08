<div id="chatroom">
	<div id="chat">
		<h3>Messages</h3>
		<div id="messages"></div>
		<textarea placeholder="Enter your message here" id="msg"></textarea>
		<input type="button" value="Send" id="send-button" />
	</div>
	<div id="members-container"><h4>Room Members</h4><div id="members"></div></div>
</div>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.2/socket.io.min.js"></script>
<script type="text/javascript">

$(function(){	
	var sampleRoom = 'sample_room'; //the one and only room for this sample app
	
	function addNotification(msg) {
		$('#messages').append('<div class="message"><div class="notification">' + msg + '</div></div>');
	}
	
	function addSelfMessage(msg) {
		$('#messages').append('<div class="message"><span class="self"><?php echo $current_user->username; ?>: </span>' + msg + '</div>');
	}

	function addUserMessage(username, msg) {
		$('#messages').append('<div class="message"><span class="user">' + username + ': </span>' + msg + '</div>');
	}
	
	function addUser(id, username, notify) {
		if (!$('#member-' + id.toString()).length) { //make sure user doesn't exist
			//add the user
			$('#members').append('<div class="member" id="member-' + id.toString() + '">' + username + '</div>');
			
			if (notify) {
				addNotification(username + ' just entered the room.');
			}
		}
	}
	
	function removeUser(id, username) {
		if ($('#member-' + id.toString()).length) { //make sure user exists
			//remove the user
			$('#member-' + id.toString()).remove();
			addNotification(username + ' just left the room.');
		}
	}

    var connected = false;
    var settings = {
        url: "<?php echo $chat_url; ?>"
    };
           
    var check_whos_in_the_room = function() {
        if (socket && connected) {
            socket.emit("whos_in_the_room", sampleRoom);
        }
    };
   
    var socket = io.connect(settings.url, {
		transports: [
			'websocket'
			, 'htmlfile'
			, 'xhr-polling'
			, 'jsonp-polling'           
		],
		'sync disconnect on unload': false
	});
    
    socket.on("connect", function() {
        //request whos online out of our user list
        connected = true;
        check_whos_in_the_room();   
        
		socket.emit("join_room", sampleRoom);
    });
    
    socket.on("reconnect", function() {
        connected = true;
    });

    socket.on("disconnect", function() {
        connected = false;
    });
    
    socket.on("users_in_room", function(users){          
        if (connected) {
            for (var i = 0; i < users.length; i++) {
				var user = users[i];
				addUser(user.id, user.username, false/*notify*/);
			}
        }
    });
    
    socket.on("user_joined", function (user) { //a new user just joined
        addUser(user.id, user.username, true/*notify*/);     
    });
  
    socket.on("user_left", function (user) { //a user just left the room
        removeUser(user.id, user.username);     
    });

    socket.on("on_user_message", function(data) { //a user just sent us a message     
        addUserMessage(data.user.username, data.message);
    });
      
	$('#send-button').click(function(){
		if (socket && connected) {
			var msg = $('#msg').val();
			addSelfMessage(msg);
			socket.emit("post_user_message", sampleRoom, msg);
			$('#msg').val(''); //clear the text box
		}
	});

	$( window ).on('beforeunload', function() {
		if (socket && connected) {
			socket.emit("leave_room", sampleRoom);
		}
	});
});

</script>
