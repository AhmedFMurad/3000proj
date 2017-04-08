<?php
session_start();
include 'serverClass.php';
$_SESSION["fullresult"] = '';
echo '
<style>
body {
    background-color: #212020;
    color: white;
}
.server {
    display: inline-block;
    height: auto;
    width: auto;
    margin: 5px;
    padding: 8px;
    border-radius: 15px;
    cursor: pointer;
    box-shadow: 1px 2px 1px #460517;
    color: #121212;
    /* text-shadow: 1px 1px 1px lightgrey; */
    font-size: larger;
}
form.server.online {
    background-color: lightgreen;
}

.server.offline {
	background-color: #e23a3a;
}
.server.offline:hover {
	background-color: #c70000;
}
form.server.online:hover {
    background-color: #23d823;
}
.serversDiv {
    float: right;
    width: 85%;
}

.formsDiv {
    float: left;
    width: 14%;
    border-right: 1px solid lightgray;
    height: 100%;
}

input[type="text"] {
    margin: 5px;
    border-color: lightgrey;
    border-radius: 5px;
    padding: 10px;
}

input[type="submit"] {
    background-color: #e23a3a;
    border: 0;
    padding: 10px;
    margin: 5px 0 5px 0;
    border-radius: 5px;
    cursor: pointer;
	font-size: large;
}

input[type="submit"]:hover {
    background-color: #c70000;
}
input[type="text"]:focus {
    box-shadow: 1px 1px 4px 3px #e23a3a;
    outline: none;
}
.onlineExp {
    border-left: 20px solid lightgreen;
    padding-left: 10px;
}

.oflineExp {
    border-left: 20px solid #e23a3a;
    padding-left: 10px;
}

.onlineExp, .oflineExp{
    margin:10px;
}
</style>';

$serversArray = array();
if (!(filesize('servers.json') == 0)){
	$serversArray = json_decode(file_get_contents('servers.json'), true);
}
if(empty($serversArray)){
	echo '<div class="serversDiv">No servers added! Please add a new server!</div>';
}
else {
	echo '<div class="serversDiv">';
	foreach($serversArray as $server) {
		error_reporting(E_ERROR | E_PARSE);
		$command = 'echo New connection!';
		$socket = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_set_timeout($socket, 1);
		$result = socket_connect($socket, $server['ip'], $server['port']);
		socket_write($socket, $command, strlen($command));
		socket_close($socket);
		if($result == 1) {
		echo '<form class="server online" action="redirect.php" method="POST"><div onclick="javascript:this.parentNode.submit();" >Sever name ' . $server['username'] .'<br>';
		echo 'Address: ' . $server['ip']. ':' . $server['port'] . '<br>';
		echo '
		
		  <input type="hidden" name="ip" value="' . $server['ip'] . '">
		  <input type="hidden" name="port" value="' . $server['port'] . '">
		  <input type="hidden" name="command" value="ls">
			</form></div>';
		} else {
			echo '<div class="server offline" alt="Server offline!">Sever name ' . $server['username'] .'<br>
				Address: ' . $server['ip']. ':' . $server['port'] . '<br></div>';
		}
	}
	echo '</div>';
}

echo '<div class="formsDiv"><div class="addServer">Add new server: <br>';
echo '<form action="addServer.php" method="POST">
		
		  <input type="text" name="username" placeholder="Enter server name">
		  <input type="text" name="ip" placeholder="Enter IP here">
		  <input type="text" name="port" placeholder="Enter port">
		  <br><input type="submit" value="Add">
			</form></div>';
			
echo '<div class="quickConnect">Quick Connect<br>';
echo '<form  action="redirect.php" method="POST">
		
		  <input type="text" name="ip" placeholder="Enter IP here">
		  <input type="text" name="port" placeholder="Enter port">
		  <br><input type="submit" value="Connect">
			</form></div><br><br> <div class="onlineExp"> = Online</div><div class="oflineExp"> = Offline</div></div>';
			
			
?>
