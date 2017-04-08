
<?php
session_start();
if(!isset($_POST["command"])) $_POST["command"] = $_SESSION["command"];
if(!isset($_SESSION["fullresult"])) $_SESSION["fullresult"] = '';
echo '<div class="terminal" id="scrollbar">';
$ip =  $_SESSION["host"];
$port = (int) $_SESSION["port"];

$command = $_POST["command"];

	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	$user = socket_connect($socket, $ip, $port) or die("Could not connect to server\n");  
	$userM = 'whoami';
	socket_write($socket, $userM, strlen($userM)) or die("Could not send data to server\n");
	$user = socket_read ($socket, 10000) or die("Could not read server response\n");
	socket_close($socket);
	
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	$ipname = socket_connect($socket, $ip, $port) or die("Could not connect to server\n");  
	$userM = 'hostname';
	socket_write($socket, $userM, strlen($userM)) or die("Could not send data to server\n");
	$ipname = socket_read ($socket, 10000) or die("Could not read server response\n");
	socket_close($socket);

 
if($command != ''){
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	$result = socket_connect($socket, $ip, $port) or die("Could not connect to server\n");  
	socket_write($socket, $command, strlen($command)) or die("Could not send data to server\n");
	$result = socket_read ($socket, 10000) or die("Could not read server response\n");
	$fullResult =  $user . '@' . $ipname . ': ' . $command . "<pre>".$result."</pre>";
	$_SESSION["fullresult"] =  $_SESSION["fullresult"] . $fullResult;	
	echo $_SESSION["fullresult"];
	socket_close($socket);
	if($command == 'remotoDisconnect') {
		header("Location: index.php"); 
		exit();
	}
}
echo '<style>
body {
    background: url("https://puu.sh/vchSn/346805ec09.jpg");
}
.terminal {

    width: 60%;
    height: 60%;
    padding: 6px;
    margin: auto;
    border-radius: 5px;
	background-color: black;
	color: #00FF00;
	overflow-y: scroll;
	overflow-x: hidden;
}

#scrollbar::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	border-radius: 10px;
	background-color: #F5F5F5;
}

#scrollbar::-webkit-scrollbar
{
	width: 12px;
	background-color: #F5F5F5;
}

#scrollbar::-webkit-scrollbar-thumb
{
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
	background-color: #555;
}

	

input:focus {
	outline-width: 0;
	
}
input {
	background-color:black; 
	border: none; 
	color: #00FF00; 
	width: 89%; 
}
.name {
	/*position: absolute;*/
}
</style>


<form  action="connect.php" method="post">
    <span class="name">' . $user . '@' . $ipname . ': </span> <input  onkeyup="Expand(this);" onblur="this.focus()" autofocus type="text" name="command" /><br /></div>
	<script>
	var element = document.getElementById("scrollbar");
element.scrollTop = element.scrollHeight;
</script>';
?>

