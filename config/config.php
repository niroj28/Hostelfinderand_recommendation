<?php 

$db = new mysqli('localhost','root','','hostelfinder');

if($db->connect_error){
	echo "Error connecting database";
}

 ?>