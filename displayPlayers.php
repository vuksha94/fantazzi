<?php

require_once 'dbconfig.php';
require_once 'Connection.inc.php';

session_start();

if(!isset($_SESSION['id_user']) || !isset($_GET['club']) || !isset($_GET['position']) || !isset($_GET['players'])){ 
    header("Location: index.php");
    exit();   
}

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT * FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$id_gw = $array['active_gw'];//active_gw -> {1,2,...38,39(POSTSEASON)}
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: index.php?gameUpdating");
    exit();
}

$club = $_GET['club'];
$position = $_GET['position'];
$sort = $_GET['sort'];
$playerName = $_GET['playerName'];

$players = explode("|", $_GET['players']);//make an array of id_players

$page = $_GET['page'];
$limit = $_GET['limit'];


//query ako nema parametara pretrage
if (empty($club) && empty($position) && empty($playerName)){
    $query = "SELECT * FROM players,positions WHERE players.position=positions.position_shortname";
}
//ako je unet neki parametar pretrage
else {
    $query = "SELECT * FROM players,positions WHERE";
    if(!empty($club)){
        $query .= " club='".$club."'";
    }
    if(!empty($position)){
        if(substr($query,-5) == "WHERE"){//$club is empty
            $query .= " position='".$position."'";
        }
        else{
            $query .= " AND position='".$position."'";
        }
    }
    if(!empty($playerName)){
        if(substr($query,-5) == "WHERE"){//$club and $position are empty
            $query .= " (name LIKE '%".$playerName."%' OR last_name LIKE '%".$playerName."%')";
        }
        else{
            $query .= " AND (name LIKE '%".$playerName."%' OR last_name LIKE '%".$playerName."%')";
        }
    }
    $query .= " AND players.position=positions.position_shortname";
}
if(!empty($sort)){
    $query .= " ORDER BY ".$sort." DESC";
}
else{
    $query .= " ORDER BY players.club ASC, positions.id_position ASC";
}

//echo $query;

$result = $connection->getResult($query);
$total = mysqli_num_rows($result);
if($total > $limit){
    $query .= " LIMIT ". ($page - 1)*$limit . "," . $limit;
    $result = $connection->getResult($query);
}
$rows = mysqli_num_rows($result);
if ($rows != 0){
    
    echo "<table class='div--full'>";
    echo "<th >Player name</th><th>Club</th><th>Position</th><th>Price</th>";
    while($row = mysqli_fetch_array($result)){
        
        $buttonValue = $row['id_player']."|".$row['club']."|".$row['name']."|".$row['last_name'].
                "|".$row['position']."|".$row['price'];
        $rowPlayer = "<tr><td class='align--center'><label for='".$row['id_player']."'>".$row['last_name']."</label></td><td class='align--center'>".$row['club']."</td><td class='align--center'>".$row['position']."</td>".
                "<td class='align--center'>".$row['price']."</td><td class='align--center'><button id=".$row['id_player']." type='button' value='".$buttonValue."'";
        if(in_array($row['id_player'], $players)){
            $rowPlayer .= " onclick='removePlayer(this.value)'>REMOVE </button></td></tr>";
        }
        else{
            $rowPlayer .= " onclick='purchasePlayer(this.value)'>PURCHASE </button></td></tr>";
        }
                
        echo $rowPlayer;
        
    }
    echo "<tr><td colspan='100' class='align--center'><b>Displayed ".$rows." of ".$total." results</b></td></tr>";
    echo "</table>";
    
    
    
    
    if($total > $limit){ ?>
        <div class="buttons__gw">
    <?php        
        
        //PREVIOUS
        if($page > 1){
        ?>
            <div class="item--previous"><button type='button' class="butt--full" onclick='loadDoc("displayPlayers.php", showPlayers, "<?php echo $page - 1; ?>", "<?php echo $limit; ?>")'>PREV</button></div>
        <?php
        }
        //NEXT
        if($page*$limit < $total){
        ?>
            <div class="item--next"><button type='button' class="butt--full" onclick='loadDoc("displayPlayers.php", showPlayers, "<?php echo $page + 1; ?>", "<?php echo $limit; ?>")'>NEXT</button></div>
        <?php
        }
    }
    ?>
        </div>
    <?php

}
else{
    echo "No players that match given parameters!";
}


?>