<?php 
if(!isset($activegw)){
    $query = "SELECT * FROM gw_status";
    $result = $connection->getResult($query);

    $array = mysqli_fetch_array($result);
    $activeGw = $array['active_gw'];
}

echo "<a href='index.php'>HOME</a>";

if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1){
    echo "<a href='status.php'>GW STATUS</a>";
    echo "<a href='updatePoints.php'>UPDATE POINTS</a>";
    
}
if(isset($_SESSION['id_user']) || isset($_SESSION['id_admin'])){ 
    if(isset($_SESSION['id_user'])){                
        if(isset($_SESSION['registration_gw'])&& $_SESSION['registration_gw'] != 0){
            echo "<a href='myteam.php'>MY TEAM</a>";
            if($_SESSION['registration_gw'] != $activeGw){
                echo "<a href='points.php'>POINTS</a>";
            }
            echo "<a href='transfers.php'>TRANSFERS</a>";
            echo "<a href='leagues.php'>LEAGUES</a>";
        }
        
    }    
    echo "<a style='float:right' href='logout.php'>LOG OUT</a>";
}
echo "<a href='fixtures.php'>FIXTURES</a>";

?>


