<?php

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
    header("Location: index.php");
    exit();
}

require_once 'dbconfig.php';
require_once 'Connection.inc.php';

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$gameweek = $_GET['q'];
if ($gameweek == ""){
    $query = "SELECT * FROM fixtures, gw_status WHERE gw_status.points_gw=fixtures.gw ORDER BY fixtures.date ASC, fixtures.time ASC";
}
else{
    $query = "SELECT * FROM fixtures, gw_status WHERE fixtures.gw='".$gameweek."' ORDER BY fixtures.date ASC, fixtures.time ASC";
}
$result = $connection->getResult($query);
if($result && mysqli_num_rows($result) == 0){
    echo "No fixtures to show for GW".$gameweek."!<br>"; ?>
    <td><button type='button' onclick="loadDoc('displayFixturesPoints.php', showFixtures, '')">SHOW FIXTURES</button></td>
<?php 

}
else{

    echo "<table>";
    $allGwFixturesPointsUpdated = true;//flag that shows if all players points for current GW are updated
    $i = 0;
    while($row = mysqli_fetch_array($result)){
        if($allGwFixturesPointsUpdated){
            if($row['points_updated'] == 0){
                $allGwFixturesPointsUpdated = false;
            }
        }
        if($i === 0){
            $gameweek = (int)$row['gw'];
            if($gameweek == 39){//postseason
                $gameweek = 38;
            }
            echo "<th colspan='10'>GW".$gameweek."<th>";        
            $i++;
        }
        echo "<tr>";
        $date = date('D j M', strtotime($row['date']));
        $time = date('G:i',strtotime($row['time']));
        echo "<td>".$date."</td>"."<td>".$time."</td>";
        $homeScore = ""; $awayScore = "";
        if($row['result_updated'] == 1){
            $homeScore = $row['home_score'];
            $awayScore = $row['away_score'];
        }
        $queryClub = "SELECT club_name, id_club FROM clubs WHERE id_club='".$row['home']."' OR id_club='".$row['away']."'";
        $resultClub = $connection->getResult($queryClub);

        $away = "";//save away here if is in first row of $resultClub
        $j = 0;//0 - write home, 1 - write away
        while($rowClub = mysqli_fetch_array($resultClub)){
            if($j === 0){//write home
                $j++;
                if($rowClub['id_club'] == $row['home']){
                    echo "<td>".$rowClub['club_name']."</td>";
                    echo "<td>".$homeScore." - ".$awayScore."</td>";
                }
                else{
                    $away = $rowClub['club_name'];
                }           
            }
            else{//write away or both, home and away
                if($away !== ""){//write home and away
                    echo "<td>".$rowClub['club_name']."</td>";
                    echo "<td>".$homeScore." - ".$awayScore."</td><td>".$away."</td>";
                }
                else{//write away
                    echo "<td>".$rowClub['club_name']."</td>";
                }
            }
        }
        if($gameweek == (int)($row['points_gw'])){//Insert points only for current points gw
            if((int)($row['result_updated'])){
               echo "<td><a href='updatePoints.php?type=playerPoints&action=updatePoints&idFixture=".$row['id_fixture']."'>[ UPDATE POINTS ]</a></td><td></td>";
            }
            else{
                echo "<td><a href='updatePoints.php?type=playerPoints&action=insertPoints&idFixture=".$row['id_fixture']."'>[ INSERT POINTS ]</a></td><td></td>";               
            }
        }
        
        echo "</tr>";    
    }
    echo "<tr>";
    if($gameweek != 1){ ?>
        <td><button type='button' onclick="loadDoc('displayFixturesPoints.php', showFixtures, <?php echo $gameweek - 1; ?>)"> GW <?php echo $gameweek - 1; ?></button></td>
    <?php 
    } 
    else{
        echo "<td></td>";
    }
    echo "<td></td><td></td><td></td><td></td>";
    if($gameweek != 38){ ?>
        <td><button type='button' onclick="loadDoc('displayFixturesPoints.php', showFixtures, <?php echo $gameweek + 1; ?>)"> GW <?php echo $gameweek + 1; ?></button></td>
    <?php 
    } 
    echo "</tr>";
    if($allGwFixturesPointsUpdated){
    ?>
        <tr><td colspan="10"><a href="commit.php?type=usersPoints&action=insertPointsAndUpdateLeagues">[ INSERT USERS POINTS AND UPDATE LEAGUE POINTS ]</a></td></tr>
    <?php
    }

    echo "</table>";
}
?>


