<?php
//funkcija za filtriranje user input-a
function test_input($data){
    $data = trim ($data);
    $data = stripcslashes($data);
    return $data;
}
?>