<?php 
$addr = shell_exec("/sbin/ifconfig wlo1 | grep 'inet addr:' | cut -d: -f2 | awk '{print $1}'");
$port = 3000;

$prefix = '';
$suffix = shell_exec('pwd') . "\n";
$suffix = trim($suffix);

set_time_limit(0);
$input = '';

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

$result = socket_bind($socket, $addr, $port) or die("Could not bind to socket\n");

$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");


while(!(strpos($input, 'remotoDisconnect'))) {

$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");

$input = socket_read($spawn, 1024) or die("Could not read input\n");

if($input == 'sudo su'){
	$output = 'Please enter the password to: root.';

	socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
	echo 'the waiting starts...';
	while(1) {
		$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
		$password = socket_read($spawn, 1024) or die("Could not read input\n");
		$input = 'echo "' . $password . '" | sudo -kS whoami';
		$output = shell_exec($input) . "\n";
		$output = trim($output);
		echo 'user entered: ' . $input . '<br>';
		echo 'the output is infact: ' . $output;
		
		if(strcmp($output, 'root') == 0)	{ 
			$output = 'success!'; 
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
			$prefix = 'echo	"' . $password . '" | sudo -kS'	;
			break; 
		}
		else { $output = 'failed! try again!'; 
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
			continue;
		}
	}
	
}
if($input == 'pwd'){
	socket_write($spawn, $suffix, strlen ($suffix)) or die("Could not write output\n");
} else if($input == 'exit'){
	if($prefix == ''){
		$tmpOut = 'You\'re not root!';
		socket_write($spawn,  $tmpOut, strlen ($tmpOut)) or die("Could not write output\n");	
	} 
	else {
		
		$prefix = '';
		$tmpOut = 'You\'re no longer root!';
		socket_write($spawn,  $tmpOut, strlen ($tmpOut)) or die("Could not write output\n");	
	}
} 
 if($input == 'cd ..'){
	$suffix = dirname($suffix);
	socket_write($spawn,  $suffix, strlen ($suffix)) or die("Could not write output\n");
} else if(strpos($input, 'cd /') !== false && $input != "cd ..") {
	$input = str_replace('cd ', '', $input);
	$tmpIn = 'ls '  . $input;
		$tmpOut = shell_exec($tmpIn);
		if ( $tmpOut == NULL) {			
			$tmpOut = 'No such file or directory';
			socket_write($spawn, $tmpOut, strlen ($tmpOut)) or die("Could not write output\n");
		}
		
		else {
			$suffix = $input;
			socket_write($spawn, $suffix, strlen ($suffix)) or die("Could not write output\n");
		}
} else if (strpos($input, 'cd ') !== false && $input != "cd ..") {
	$input = str_replace('cd ', '', $input);
	if($suffix == '/') { 		
		$tmpIn = 'ls ' . $suffix . $input;
		$tmpOut = shell_exec($tmpIn);
		if ( $tmpOut == NULL) {			
			$tmpOut = 'No such file or directory';
			socket_write($spawn, $tmpOut, strlen ($tmpOut)) or die("Could not write output\n");
		}
		else {
			$suffix = $suffix . $input;
			socket_write($spawn, $suffix, strlen ($suffix)) or die("Could not write output\n");
		}
	}
	else { 
		$tmpIn = 'ls ' . $suffix . '/' . $input;
		$tmpOut = shell_exec($tmpIn);
		if ( $tmpOut == NULL) {	
			$tmpOut = 'No such file or directory';
			socket_write($spawn, $tmpOut, strlen ($tmpOut)) or die("Could not write output\n");
		}
		else {
			$suffix = $suffix . '/' . $input; 
			socket_write($spawn, $suffix, strlen ($suffix)) or die("Could not write output\n");
		}
	}
} 

$input = trim($input);
echo "Client Command : ".$input;
if(strpos($input, 'cat ') !== false) {
	$fileinput = str_replace('cat ', '', $input);
	$input = $prefix . ' cat ' . $suffix . '/' . $fileinput;	
} else if(strpos($input, 'mkdir ') !== false) {
	$fileinput = str_replace('mkdir ', '', $input);
	$input = $prefix . ' mkdir ' . $suffix . '/' . $fileinput;	
} else if(strpos($input, 'rm ') !== false) {
	$fileinput = str_replace('rm ', '', $input);
	$input = $prefix . ' rm ' . $suffix . '/' . $fileinput;	
} else if($input != 'whoami' && $input != 'hostname' && $input != 'ps'){
	$input = $prefix . ' ' . $input . ' ' . $suffix;
} else {
	$input = $prefix . ' ' . $input;
}
$output = shell_exec($input) . "\n";

socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
}
echo 'closing sockets';
socket_close($spawn);
socket_close($socket);

?>
