<?php
session_start();

if(!isset($_SESSION['id_user'])){ 
    header("Location: index.php");
    exit();   
}

require_once 'dbconfig.php';
require_once 'Connection.inc.php';

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT points_gw,game_updating FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$currentPointsGw = $array['points_gw'];
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: index.php?gameUpdating");
    exit();
}
$stats = explode("|", $_GET['q']);
$pointsGw = (int)$stats[0];
$user_id = $stats[1];

if($_SESSION['id_user'] == $user_id){
    $name = $_SESSION['user_name'];
    $lastName = $_SESSION['user_lastname'];
    $teamName = $_SESSION['user_teamname'];
    $registrationGw = (int)$_SESSION['registration_gw'];
}
else{
    $query = "SELECT name, last_name, team_name, registration_gw FROM login WHERE id_user='".$user_id."' LIMIT 1";
    $result = $connection->getResult($query);
    $array = mysqli_fetch_array($result);
    $name = $array['name'];
    $lastName = $array['last_name'];
    $teamName = $array['team_name'];
    $registrationGw = (int)$array['registration_gw'];
    
}

$query = "SELECT * FROM users_teams,formations WHERE id_user='".$user_id."' AND gw='".$pointsGw."' AND formation=id_formation";
$result = $connection->getResult($query);
if(mysqli_num_rows($result) == 0){
    $playerFound = false;
}
else{
    $playerFound = true;
    $array = mysqli_fetch_array($result);

    $transfersMade = $array['transfers'];
    $transferCost = $array['transfer_cost'];

    //make an array of id_players by fetching $array
    $players = array();
    for($i = 4; $i <= 18; $i++){
        array_push($players, $array[$i]);
    }

    $query = "SELECT id_player, club, last_name, position FROM players WHERE id_player='".$players[0]."'";
    for($i =1; $i < count($players); $i++){
        $query .= " OR id_player='".$players[$i]."'";
    }

    $result = $connection->getResult($query);

    $playersInfo = array();
    while($row = mysqli_fetch_array($result)){
        for($i = 0; $i < 4; $i++){//*** 7 is number of columns in table players ***not dinamically -> BAD***
            array_push($playersInfo, $row[$i]);
        }
    }

    $formation = explode("-", $array['description']);//make an array of formation ex.{3,4,3}

    $startingXI = array();
    array_push($startingXI, $players[0]);
    for($i = 2; $i < 2 + $formation[0]; $i++){
        array_push($startingXI, $players[$i]);                                    
    }

    for($i = 7; $i < 7 + $formation[1]; $i++){
        array_push($startingXI, $players[$i]);                
    }

    for($i = 12; $i < 12 + $formation[2]; $i++){
        array_push($startingXI, $players[$i]);                         
    }
    $subs = array();
    $subs[0] = $players[1];//gkp substitution
    for($i = 19; $i <= 21; $i++){
        array_push($subs, $array[$i]);
    }


    $captain = $array[22]; $vice_captain = $array[23];
    $query = "SELECT id_player, mins_played, points_scored FROM players_stats, fixtures WHERE fixtures.gw='".$pointsGw."' AND players_stats.id_fixture=fixtures.id_fixture AND (players_stats.id_player='".$players[0]."'";
    for($i =1; $i < count($players); $i++){
        $query .= " OR players_stats.id_player='".$players[$i]."'";
    }
    $query .= ")";

    $playersPoints = array();//$playersPoints = {id_player, mins_played, points_scored}

    $result = $connection->getResult($query);
    $playerStats = "";
    while($array = mysqli_fetch_array($result)){
        array_push($playersPoints, (int)$array['id_player']);
        $playerStats = $array['mins_played']."|".$array['points_scored'];
        array_push($playersPoints, $playerStats);

    }
    $points = 0;//calculate points for GW without transfer cost
    for($i = 0; $i < count($startingXI); $i++){
        if( ($indexPts = array_search($startingXI[$i], $playersPoints)) !== false ){
            $stat = explode("|", $playersPoints[$indexPts + 1]);
            $pts = (int)$stat[1];
            if($captain == $startingXI[$i]){
                $pts = 2*$pts;
            }
            $points += $pts;
        }
    }
    if($transfersMade != 0){
        //select transfers IN and transfers OUT
        $queryTransfers = "SELECT transfer_in,transfer_out,last_name,id_player FROM users_transfers,players WHERE users_transfers.id_user='".$user_id."' AND users_transfers.gw='".$pointsGw."' AND users_transfers.transfer_in=players.id_player OR users_transfers.transfer_out=players.id_player";
        $resultTransfers = $connection->getResult($queryTransfers);
        $transfersIn = array();
        $transfersOut = array();
        while($arrayTransfers = mysqli_fetch_array($resultTransfers)){
            if($arrayTransfers['transfer_in'] == $arrayTransfers['id_player']){
                array_push($transfersIn, $arrayTransfers['last_name']);           
            }
            else{
                array_push($transfersOut, $arrayTransfers['last_name']);
            }
        }
    }

}
?>

<h2>GW<?php echo $pointsGw; ?> POINTS: <?php echo $points; ?></h2>
Transfers Made: <?php echo $transfersMade; if($transferCost < 0) echo "(".$transferCost." pts)"; ?><br>
<?php 

echo $teamName."<br><br>";
$qPrevious = ($pointsGw - 1)."|".$user_id;
$qNext = ($pointsGw + 1)."|".$user_id;
?>
<div class="buttons__gw">
<?php
if($registrationGw < $pointsGw){//button for previous GW
?>

    <div class="item--previous"><button type='button' class="butt--full" onclick="loadDoc('displayPoints.php', showPoints, '<?php echo $qPrevious; ?>', 'points')">GW<?php echo ($pointsGw - 1)." "; ?>PTS</button></div>
<?php    
}
if($pointsGw < $currentPointsGw){//button for next GW
?>
    <div class="item--next"><button type='button' class="butt--full" onclick="loadDoc('displayPoints.php', showPoints, '<?php echo $qNext; ?>', 'points')">GW<?php echo ($pointsGw + 1)." "; ?>PTS</button></div>

<?php
}
?>
</div>
<?php
echo "<form name='mysquad'>";
echo "<h1 align='center'>First XI</h1>";
echo "<div class='startingeleven'>";
echo "<div class='gkp' align='center'>";
$index = array_search($players[0], $playersInfo);
echo "<span title='Goalkeeper' class='span__hidden'>".$playersInfo[$index + 3].": </span>";
if( ($indexPts = array_search($players[0], $playersPoints)) !== false ){
    $stat = explode("|", $playersPoints[$indexPts + 1]);
    $pts = $stat[1];
    if($captain == $players[0]){
        $pts = 2*(int)$pts;
    }
    $mins = $stat[0];
}
else{
    $pts = "";
}
echo "<input type='text' id='".$players[0]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")";
if($pts !== ""){
    echo ", M:".$mins." P:".$pts;
}
echo "' readonly='readonly'>";                
if($captain == $players[0]){
    echo "<button id='captain' class='cap-vcap__butt' type='button'>C</button>";
}
elseif($vice_captain == $players[0]){
    echo "<button id='viceCaptain' class='cap-vcap__butt' type='button'>V</button>";
}
echo "</div><br><br>";

echo "<div class='def' align='center'>"; 
//echo "DEF: ";
for($i = 2; $i < 2 + $formation[0]; $i++){
    $index = array_search($players[$i], $playersInfo);
    echo "<span title='Defender' class='span__hidden'>".$playersInfo[$index + 3].": </span>";
    if( ($indexPts = array_search($players[$i], $playersPoints)) !== false ){
        $stat = explode("|", $playersPoints[$indexPts + 1]);
        $pts = $stat[1];
        if($captain == $players[$i]){
            $pts = 2*(int)$pts;
        }
        $mins = $stat[0];
    }
    else{
        $pts = "";
    }
    echo "<input type='text' id='".$players[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")";
    if($pts !== ""){
        echo ", M:".$mins." P:".$pts;
    }
    echo "' readonly='readonly'>";
    if($captain == $players[$i]){
        echo "<button id='captain' class='cap-vcap__butt' type='button'>C</button>";
    }
    elseif($vice_captain == $players[$i]){
        echo "<button id='viceCaptain' class='cap-vcap__butt' type='button'>V</button>";
    }                    
}
echo "</div><br><br>";

echo "<div class='mid' align='center'>"; 
//echo "MID: ";
for($i = 7; $i < 7 + $formation[1]; $i++){
    $index = array_search($players[$i], $playersInfo);
    echo "<span title='Midfielder' class='span__hidden'>".$playersInfo[$index + 3].": </span>";
    if( ($indexPts = array_search($players[$i], $playersPoints)) !== false ){
        $stat = explode("|", $playersPoints[$indexPts + 1]);
        $pts = $stat[1];
        if($captain == $players[$i]){
            $pts = 2*(int)$pts;
        }
        $mins = $stat[0];
    }
    else{
        $pts = "";
    }
    echo "<input type='text' id='".$players[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")";
    if($pts !== ""){
        echo ", M:".$mins." P:".$pts;
    }
    echo "' readonly='readonly'>";
    if($captain == $players[$i]){
        echo "<button id='captain' class='cap-vcap__butt' type='button'>C</button>";
    }
    elseif($vice_captain == $players[$i]){
        echo "<button id='viceCaptain' class='cap-vcap__butt' type='button'>V</button>";
    }                    
}
echo "</div><br><br>";      
$pts = "";
echo "<div class='fwd' align='center'>"; 
//echo "FWD: ";
for($i = 12; $i < 12 + $formation[2]; $i++){
    $index = array_search($players[$i], $playersInfo);
    echo "<span title='Forward' class='span__hidden'>".$playersInfo[$index + 3].": </span>";
    if( ($indexPts = array_search($players[$i], $playersPoints)) !== false ){
        $stat = explode("|", $playersPoints[$indexPts + 1]);
        $pts = $stat[1];
        if($captain == $players[$i]){
            $pts = 2*(int)$pts;
        }
        $mins = $stat[0];
    }
    else{
        $pts = "";
    }
    echo "<input type='text' id='".$players[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")";
    if($pts !== ""){
        echo ", M:".$mins." P:".$pts;
    }
    echo "' readonly='readonly'>";
    if($captain == $players[$i]){
        echo "<button id='captain' class='cap-vcap__butt' type='button'>C</button>";
    }
    elseif($vice_captain == $players[$i]){
        echo "<button id='viceCaptain' class='cap-vcap__butt' type='button'>V</button>";
    }                    
}
echo "</div></div><br><br>"; 
$pts = "";
echo "<h1 align='center'>Subs</h1>";
echo "<div class='subs'>";
echo "<table>";
echo "<th>GK</th><th>1</th><th>2</th><th>3</th>";
echo "<tr><td>";
$index = array_search($players[1], $playersInfo);
echo $playersInfo[$index + 3].": ";
if( ($indexPts = array_search($players[1], $playersPoints)) !== false ){
    $stat = explode("|", $playersPoints[$indexPts + 1]);
    $pts = $stat[1];
    $mins = $stat[0];
}
else{
    $pts = "";
}
echo "<input type='text' id='".$players[1]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")";
if($pts !== ""){
    echo ", M:".$mins." P:".$pts;
}
echo "' readonly='readonly'></td>";

$pts = "";

for($i = 1; $i < 4; $i++){                   
    $index = array_search($subs[$i], $playersInfo);
    echo "<td>";
    echo "&nbsp; &nbsp";
    $pos = $playersInfo[$index + 3];
    if( ($indexPts = array_search($subs[$i], $playersPoints)) !== false ){
        $stat = explode("|", $playersPoints[$indexPts + 1]);
        $pts = $stat[1];
        $mins = $stat[0];
    }
    else{
        $pts = "";
    }
    echo $pos.": ";                   
    echo "<input type='text' id='".$subs[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")";
    if($pts !== ""){
        echo ", M:".$mins." P:".$pts;
    }
    echo "' readonly='readonly'></td>";
}
echo "</tr></table></div><br><br>"; 
echo "</form>";
//transfers made
if($transfersMade != 0){
?>
<div class="transfers" align="center">
<table>
    <th>TRANSFER IN</th><th>TRANSFER OUT</th>
    <?php
        for($i = 0; $i < count($transfersIn); $i++){
            echo "<tr><td>".$transfersIn[$i]."</td><td>".$transfersOut[$i]."</td></tr>";
        }
    ?>
</table>
</div>
<?php
}
?>
