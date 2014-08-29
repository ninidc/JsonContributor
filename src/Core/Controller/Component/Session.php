<?php
//--------------------------------------------------------------//
//              Session components
//--------------------------------------------------------------//
namespace Core\Controller\Component;

class Session {

	public function __construct() 
	{

	}

	public function setNotification($message)
	{
		$_SESSION["message_notification"] = $message;
	}

	public function getNotification()
	{
		if(isset($_SESSION["message_notification"])) {
			$notif = $_SESSION["message_notification"];
			unset($_SESSION["message_notification"]);
			return $notif;
		}
		return null;
	}

	public function write($key, $value)
	{
		$_SESSION[ $key ] = $value;
	}

	public function read($key) 
	{
		if(isset($_SESSION[ $key ])){
			return $_SESSION[ $key ];
		}
		return null;
	}

	public function delete($key) 
	{
		if(isset($_SESSION[ $key ])) {
			unset($_SESSION[ $key ]);
			return true;
		}

		return false;
	}

	public function check($key)
	{
		if(isset($_SESSION[ $key ])) {
			return true;
		}
		return false;
	}

}
//--------------------------------------------------------------//
?>