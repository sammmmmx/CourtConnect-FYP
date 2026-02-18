<?php

define('DB_SERVER', 'localhost');   
define('DB_USERNAME', 'root');      
define('DB_PASSWORD', '');          
define('DB_NAME', 'courtconnect_db'); 

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


if($link === false){
    die("ERROR: Could not connect to the database. " . mysqli_connect_error());
}
else {
    
    date_default_timezone_set('Asia/Kuala_Lumpur');
    

    
}
?>
