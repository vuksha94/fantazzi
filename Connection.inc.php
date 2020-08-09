<?php


class Connection {
    private $link;
    private $result;   
    
    function getLink() {
        return $this->link;
    }

    public function connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME){
        $this->link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS) or die("Greska u konekciji!");
        mysqli_select_db($this->link, $DB_NAME) or die("Greska u selektovanju baze podataka!");
        mysqli_set_charset($this->link, 'utf8');
    }
    
    public function getResult($query){
        $this->result = mysqli_query($this->link, $query);                 
        return $this->result;
    }
   
    public function close(){
        mysqli_close($this->link);
        unset($this->link);
    }
}


?>