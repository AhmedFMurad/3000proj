<?php

include 'serverClass.php';


$newServer = new Server($_POST["ip"], $_POST["port"], $_POST["username"]);

if (filesize('servers.json') == 0){
    $serversArray = array();
} else {
	$serversArray = json_decode(file_get_contents('servers.json'), true);
}
array_push($serversArray, $newServer);
file_put_contents("servers.json",json_encode($serversArray));
header("Location: index.php"); 
exit();
?>
