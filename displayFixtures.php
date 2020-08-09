<?php

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
    $admin = 0;
}
else{
    $admin = 1;
}

require_once 'dbconfig.php';
require_once 'Connection.inc.php';

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$gameweek = $_GET['q'];
if ($gameweek == ""){
    $query = "SELECT * FROM fixtures, gw_status WHERE gw_status.active_gw=fixtures.gw ORDER BY fixtures.date ASC, fixtures.time ASC";
}
else{
    $query = "SELECT * FROM fixtures WHERE gw='".$gameweek."'";
}
$result = $connection->getResult($query);

echo "<h2 align='center'>Fixtures</h2>";
if($result && mysqli_num_rows($result) == 0){
    echo "No fixtures to show for GW".$gameweek."!<br>"; ?>
    <td><button type='button' onclick="LoadDocFx('displayFixtures.php', showFixtures, '');">SHOW FIXTURES</button></td>
<?php 

}
else{
    echo "<table class='div--full'>";

    $i = 0;
    while($row = mysqli_fetch_array($result)){
        if($i === 0){
            $gameweek = (int)$row['gw'];
            if($gameweek == 39){//postseason
                $gameweek = 38;
            }
            echo "<th colspan='10'>GW".$gameweek."<th>";
            //echo "<tr><td>Date</td><td>Time</td><td>Home</td><td></td><td>Away</td></tr>";
            $i++;
        }
        echo "<tr>";
        $date = date('D j M', strtotime($row['date']));
        $time = date('G:i',strtotime($row['time']));
        echo "<td class='row__date align--center'>".$date."</td>"."<td class='row__stat align--center'>".$time."</td>";
        $homeScore = ""; $awayScore = "";
        if($row['result_updated'] == 1){
            $homeScore = $row['home_score'];
            $awayScore = $row['away_score'];
        }
        $queryClub = "SELECT club_name, id_club FROM clubs WHERE id_club='".$row['home']."' OR id_club='".$row['away']."'";
        $resultClub = $connection->getResult($queryClub);
        
        $home = "";
        $away = "";//save away here if is in first row of $resultClub
        $j = 0;//0 - write home, 1 - write away
        while($rowClub = mysqli_fetch_array($resultClub)){
            if($j === 0){//write home
                $j++;
                if($rowClub['id_club'] == $row['home']){
                    $home = $rowClub['club_name'];
                    echo "<td class='row__club align--center'>".$home."</td>";
                    echo "<td class='row__stat align--center'>".$homeScore." - ".$awayScore."</td>";
                }
                else{
                    $away = $rowClub['club_name'];
                }           
            }
            else{//write away or both, home and away
                if($away !== ""){//write home and away
                    $home = $rowClub['club_name'];
                    echo "<td class='row__club align--center'>".$home."</td>";
                    echo "<td class='row__stat align--center'>".$homeScore." - ".$awayScore."</td><td class='row__club align--center'>".$away."</td>";
                }
                else{//write away
                    $away = $rowClub['club_name'];
                    echo "<td class='row__club align--center'>".$away."</td>";
                }
            }
        }
        if($admin){//admin can update and delete
            if((int)$row['result_updated'] == 0){
                echo "<td><a title='".$home." vs ".$away."' href='fixtures.php?type=fixture&action=update&id=".$row['id_fixture']."'>UPDATE DETAILS</a></td>";
            }            
        }
        echo "</tr>";    
    }
    echo "<tr>";
    if($gameweek != 1){ ?>
        <td class="elem--quarter"><button type='button' class="butt--full" onclick="LoadDocFx('displayFixtures.php', showFixtures, <?php echo $gameweek - 1; ?>)">GW <?php echo $gameweek - 1; ?></button></td>
    <?php 
    } 
    else{
        echo "<td></td>";
    }
    echo "<td></td><td></td><td></td>";

    if($gameweek != 38){ ?>
        <td class="elem--quarter"><button type='button' class="butt--full" onclick="LoadDocFx('displayFixtures.php', showFixtures, <?php echo $gameweek + 1; ?>)">GW <?php echo $gameweek + 1; ?></button></td>
    <?php 
    } 

    echo "</table>";
}
?>
