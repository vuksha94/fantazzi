<?php
require_once 'dbconfig.php';
require_once 'Connection.inc.php';

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//echo "123";
$email = $_GET['q'];

$query = "SELECT email FROM login WHERE email='".$email."' LIMIT 1";
$result = $connection->getResult($query);
    if (mysqli_num_rows($result)==0){ 
        echo "1";   //message that email is not used by any other user
    }
    else {
        echo "0";   //message that email is taken by another user
    }


?>

