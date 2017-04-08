<?php
class Server {
	public $online = false;
	public $ip;
	public $port;
	public $username;


	public function __construct($ip, $port, $username) {
		$this->ip = $ip;
		$this->port = $port;
		$this->username = $username;
	}
}


?>
