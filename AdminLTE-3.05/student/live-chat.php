live-chat.php 

<?php include('includes/auth.php'); ?>
<?php checkRole('student'); ?>
<?php include('includes/config.php'); ?>
<?php //include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('includes/functions.php'); ?>

<?php 
$user_id=$_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>AI Student Chat</title>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>

body{
    background:#f1f5f9;
}

.chat-container{
    width:90%;
    max-width:900px;
    margin:30px auto;
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

.chat-header{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    padding:20px;
    font-size:24px;
    font-weight:bold;
}

.chat-box{
    padding:20px;
}

.msg-user{
    background:#2563eb;
    color:#fff;
    padding:12px 18px;
    border-radius:15px 15px 0 15px;
    margin-bottom:15px;
    width:fit-content;
    max-width:70%;
    margin-left:auto;
    word-wrap:break-word;
}

.msg-ai{
    color:#0f172a;
    margin-bottom:15px;
    word-wrap:break-word;
}

.chat-input{
    display:flex;
    padding:20px;
    gap:10px;
    border-top:1px solid #ddd;
}

.chat-input input{
    flex:1;
    padding:15px;
    border-radius:12px;
    border:1px solid #cbd5e1;
    font-size:16px;
}

.chat-input button{
    background:#16a34a;
    color:#fff;
    border:none;
    padding:15px 25px;
    border-radius:12px;
    cursor:pointer;
    font-size:16px;
    font-weight:bold;
}

.chat-input button:hover{
    background:#15803d;
}
/* Mobile Responsive */

@media (max-width:768px){

.chat-container{
    width:95%;
    margin:15px auto;
    border-radius:12px;
}

.chat-header{
    font-size:20px;
    padding:15px;
    text-align:center;
}

.chat-box{
    padding:15px;
    height:60vh;
    overflow-y:auto;
}

.msg-user,
.msg-ai{
    max-width:90%;
    font-size:14px;
}

.chat-input{
    flex-direction:column;
    padding:15px;
}

.chat-input input{
    width:100%;
    font-size:15px;
}

.chat-input button{
    width:100%;
    padding:12px;
    font-size:15px;
}

}
  </style>
</head>

<body>

<div class="chat-container">

  <div class="chat-header">
    🎓 ERP AI Assistant
  </div>

  <div class="chat-box" id="chat-box"></div>

  <div class="chat-input">

    <input type="text"
    id="message"
    placeholder="Ask your question">


<button onclick="startVoice()">🎤 Speak</button>
    <button id="send">
      Send
    </button>

  </div>

</div>

<script>
function startVoice(){

const recognition = new webkitSpeechRecognition();
recognition.lang = "en-US";



recognition.start();
recognition.onresult = function(event){

document.getElementById("message").value =
event.results[0][0].transcript;

};
    }
</script>
<script>

function loadchat()
{
    $('#chat-box').load('load-chat.php',function(){

        $('#chat-box').scrollTop(
            $('#chat-box')[0].scrollHeight
        );

    });
}

$('#send').click(function(){

    let q = $('#message').val();

    console.log("Sending:", q);

    if(q == '') return;

    $.ajax({
        url: 'ask_ai.php',
        type: 'POST',
        data: {message: q},
        success: function(response){
            console.log("Response:", response);

            $('#message').val('');
            loadchat();
        },
        error: function(xhr){
            console.log("ERROR:", xhr.responseText);
        }
    });

});
//setInterval(loadchat,2000);

loadchat();

</script>

<?php include('footer.php'); ?>
</body>
</html>