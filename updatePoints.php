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

$query = "SELECT game_updating,points_gw FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$pointsGw = $array['points_gw'];
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: status.php");
    exit();
}

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script>
        var subsHomeAdd = 0;//saves number of subs 
        var subsAwayAdd = 0;
        
        function addSubsHomeF(){
            var subsToAdd = parseInt(document.getElementById("addSubsHome").value, 10);
                if(subsToAdd < 1 || subsToAdd + subsHomeAdd > 3){
                    alert("Add can't be done!");
                    return false;
                }
                var i;
                for(i = 11 + subsHomeAdd + 1; i <= 11 + subsToAdd + subsHomeAdd; i++){
                    document.getElementById("playerHome" + i).disabled = false;
                    document.getElementById("minsHome" + i).disabled = false;
                    document.getElementById("ptsHome" + i).disabled = false;
                }
                subsHomeAdd += subsToAdd;
                document.getElementById("numHomePlayers").value = subsHomeAdd + 11;
        }
        function removeSubsHomeF(){
            var subsToRem = parseInt(document.getElementById("removeSubsHome").value, 10);
                if(subsToRem > subsHomeAdd){
                    alert("Remove can't be done!");
                    return false;
                }
                var i;
                for(i = 11 + subsHomeAdd; i > 11 + subsHomeAdd - subsToRem; i--){
                    document.getElementById("playerHome" + i).disabled = true;
                    document.getElementById("minsHome" + i).disabled = true;
                    document.getElementById("ptsHome" + i).disabled = true;
                }
                subsHomeAdd -= subsToRem;
                document.getElementById("numHomePlayers").value = 11 + subsHomeAdd - subsToRem;
        }
        function addSubsAwayF(){
            var subsToAdd = parseInt(document.getElementById("addSubsAway").value, 10);
                if(subsToAdd < 1 || subsToAdd + subsAwayAdd > 3){
                    alert("Add can't be done!");
                    return false;
                }
                var i;
                for(i = 11 + subsAwayAdd + 1; i <= 11 + subsToAdd + subsAwayAdd; i++){
                    document.getElementById("playerAway" + i).disabled = false;
                    document.getElementById("minsAway" + i).disabled = false;
                    document.getElementById("ptsAway" + i).disabled = false;
                }
                subsAwayAdd += subsToAdd;
                document.getElementById("numAwayPlayers").value = subsAwayAdd + 11;
        }
        function removeSubsAwayF(){
            var subsToRem = parseInt(document.getElementById("removeSubsAway").value, 10);
                if(subsToRem > subsAwayAdd){
                    alert("Remove can't be done!");
                    return false;
                }
                var i;
                for(i = 11 + subsAwayAdd; i > 11 + subsAwayAdd - subsToRem; i--){
                    document.getElementById("playerAway" + i).disabled = true;
                    document.getElementById("minsAway" + i).disabled = true;
                    document.getElementById("ptsAway" + i).disabled = true;
                }
                subsAwayAdd -= subsToRem;
                document.getElementById("numAwayPlayers").value = 11 + subsAwayAdd - subsToRem;
        }
        function checkPointsInput(){
            var homeResult = document.getElementById("homeResult").value;
            var awayResult = document.getElementById("awayResult").value;
            if(homeResult === "" || awayResult === ""){
                alert("Result is invalid!");
                return false;
            }
            var i, player, mins, pts;
            var playersHome = [];//for checking if same player is entered more than once
            var numHomePlayers = parseInt(document.getElementById("numHomePlayers").value, 10);
            for(i = 1; i <= numHomePlayers; i++){
                player = document.getElementById("playerHome" + i).value;
                mins = document.getElementById("minsHome" + i).value;
                pts = document.getElementById("ptsHome" + i).value;
                if(playersHome.indexOf(player) === -1){//if not in array
                    playersHome.push(player);
                }
                else{
                    alert("Home Player" + i + " is inserted for the second time!");
                    return false;
                }                
                if(player === "" || mins === "" || pts === ""){
                    alert("Home Player" + i + " input is invalid!");
                    return false;
                }
            }
            var playersAway = [];//for checking if same player is entered more than once
            var numAwayPlayers = parseInt(document.getElementById("numAwayPlayers").value, 10);
            for(i = 1; i <= numAwayPlayers; i++){
                player = document.getElementById("playerAway" + i).value;
                mins = document.getElementById("minsAway" + i).value;
                pts = document.getElementById("ptsAway" + i).value;
                if(playersAway.indexOf(player) === -1){//if not in array
                    playersAway.push(player);
                }
                else{
                    alert("Away Player" + i + " is inserted for the second time!");
                    return false;
                } 
                
                if(player === "" || mins === "" || pts === ""){
                    alert("Away Player" + i + " input is invalid!");
                    return false;
                }
            }
            return true;
        }
        function loadDoc(url, cFunction, q)
            {                
                if (window.XMLHttpRequest)
                {
                    xmlhttp = new XMLHttpRequest();
                }
                else
                {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function()
                {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
                    {
                        cFunction(this);
                    }
                }
                xmlhttp.open("GET", url + "?q=" + q, true);
                xmlhttp.send();
            }            
            function showFixtures(xhttp){
                document.getElementById("displayFixturesPoints").innerHTML = 
                        xhttp.responseText;
            }
        </script>
    </head>
    <body onload="loadDoc('displayFixturesPoints.php', showFixtures, '')">
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <?php
        if(isset($_GET['insertPoints']) && isset($_GET['idFixture'])){
            if($_GET['insertPoints'] == "success"){
                $msg = "Succesfully inserted points for fixture with ID: ".$_GET['idFixture']." !";
                echo "<span style='color:white; background-color:green'>". $msg . "</span>";
            }
            elseif($_GET['insertPoints'] == "fail"){
                $msg = "Unuccesfully inserted points for fixture with ID: ".$_GET['idFixture']." !";
                echo "<span style='color:white; background-color:red'>". $msg . "</span>";
            }
        }
        elseif(isset($_GET['allPlayersStatsUpdated']) && $_GET['allPlayersStatsUpdated'] == "fail"){
            $msg = "You haven't inserted player points for all fixtures in GW".$pointsGw." !";
            echo "<span style='color:white; background-color:red'>". $msg . "</span>";
        }
        elseif(isset($_GET['type']) && $_GET['type'] == "playerPoints" && isset($_GET['action']) && $_GET['action'] == "insertPoints" && isset($_GET['idFixture'])){
        ?>
        <div id="insertFixturePoints"><h2>INSERT POINTS AND RESULT</h2>
            <form id="insertPointsForm" method="post" action="commit.php?type=playerPoints&action=insertPoints" onsubmit="return checkPointsInput()">
            <?php
                $query = "SELECT id_fixture,home,away,date,time,club_name,club_shortname,id_club,fixtures.gw FROM fixtures, clubs, gameweek WHERE fixtures.gw=gameweek.id_gw AND id_fixture='".$_GET['idFixture']."' AND (fixtures.home=clubs.id_club OR fixtures.away=clubs.id_club)";
                $result = $connection->getResult($query);
                
                if(mysqli_num_rows($result) == 2){
                    $i = 0;
                    $homeName = ""; $awayName = "";                    
                    while($array = mysqli_fetch_array($result)){                        
                        if($i === 0){//first row -> To get all data with one query (because of club names)
                            $gw = $array['gw'];
                            $idFixture = $array['id_fixture'];
                            $home = $array['home'];
                            $away = $array['away'];
                            $date = date('D j M', strtotime($array['date']));
                            $time = date('G:i',strtotime($array['time']));
                            
                            if($array['id_club'] == $home){
                                $homeName = $array['club_name'];
                                $homeShortname = $array['club_shortname'];
                            }
                            else{
                                $awayName = $array['club_name'];
                                $awayShortname = $array['club_shortname'];
                            }
                        $i++;        
                        }
                        else{//second row -> To get all data with one query (because of club names)
                            if($array['id_club'] == $home){
                                $homeName = $array['club_name'];
                                $homeShortname = $array['club_shortname'];
                            }
                            else{
                                $awayName = $array['club_name'];
                                $awayShortname = $array['club_shortname']; 
                            }
                        }
                    }
                    ?>
                <div id="fixtureInfo" >
                    <?php
                    echo $gw." ".$date." ".$time;
                    ?><br>                   
                <input type="hidden" name="idFixture" value="<?php echo $idFixture; ?>">
                <input type="hidden" name="gw" value="<?php echo $gw; ?>">
                <input type="hidden" name="homeShort" value="<?php echo $homeShortname; ?>">
                <input type="hidden" name="awayShort" value="<?php echo $awayShortname; ?>">
                <input type="hidden" id="numHomePlayers" name="numHomePlayers" value="11">
                <input type="hidden" id="numAwayPlayers" name="numAwayPlayers" value="11">
                <?php
                echo $homeName; ?>&nbsp;
                <input type="number" id="homeResult" name="homeResult" value="0" min="0" max="10"> : 
                <input type="number" id="awayResult" name="awayResult" value="0" min="0" max="10">&nbsp;
                <?php
                echo $awayName; 
                $query = "SELECT id_player,name,last_name FROM clubs, players, positions WHERE positions.position_shortname=players.position AND clubs.club_shortname=players.club AND players.club='".$homeShortname."' ORDER BY positions.id_position";
                if( !( $result = $connection->getResult($query) ) ){
                    $msg = "Querry error!";
                    echo "<span style='color:white; background-color:red'>". $msg . "</span>";
                    exit();
                }
                $playersHomeInfo = array();//save players info into array, $playersInfo: {31, Shkodran, Mustafi, 33, Gabriel, Paulista, ... }
                while($array = mysqli_fetch_array($result)){
                    array_push($playersHomeInfo, $array['id_player']);
                    array_push($playersHomeInfo, $array['name']);
                    array_push($playersHomeInfo, $array['last_name']);
                }
                
                $query = "SELECT id_player,name,last_name FROM clubs, players, positions WHERE positions.position_shortname=players.position AND clubs.club_shortname=players.club AND players.club='".$awayShortname."' ORDER BY positions.id_position";
                if( !( $result = $connection->getResult($query) ) ){
                    $msg = "Querry error!";
                    echo "<span style='color:white; background-color:red'>". $msg . "</span>";
                    exit();
                }
                $playersAwayInfo = array();//save players info into array, $playersInfo: {31, Shkodran, Mustafi, 33, Gabriel, Paulista, ... }
                while($array = mysqli_fetch_array($result)){
                    array_push($playersAwayInfo, $array['id_player']);
                    array_push($playersAwayInfo, $array['name']);
                    array_push($playersAwayInfo, $array['last_name']);
                }
                ?>
                </div>
                <div id="players">
                    <div id="playersHome" style="float:left;padding-right: 70px">
                        <table>
                    <?php
                        for($i = 1; $i <= 14; $i++){
                            $disabled = "";
                            if($i >= 12){
                               $disabled = "disabled"; 
                            }
                        ?>
                        
                        <tr>
                        <td>Player <?php echo $i; ?>:</td>
                        <td><select id="playerHome<?php echo $i; ?>" name="playerHome<?php echo $i; ?>" <?php echo $disabled; ?>>
                            <option value="">Select Player...</option>
                            <?php
                            
                            for($j = 0; $j < count($playersHomeInfo) / 3; $j++){
                            ?>
                            <option value="<?php echo $playersHomeInfo[$j * 3]; ?>"><?php echo $playersHomeInfo[$j * 3 + 1]." ".$playersHomeInfo[$j * 3 + 2]; ?></option>
                            <?php
                            }
                            ?>                
                            </select></td>
                            <td>Minutes played:</td><td> <input style="width:40px;" type="number" id="minsHome<?php echo $i; ?>" name="minsHome<?php echo $i; ?>" value="90" min="1" max="90" <?php echo $disabled; ?>></td> 
                            <td>PTS:</td><td> <input style="width:40px;" type="number" id="ptsHome<?php echo $i; ?>" name="ptsHome<?php echo $i; ?>" value="2" <?php echo $disabled; ?>></td>
                        </tr>
                        <?php
                            if($i == 11){
                                echo "<tr><td colspan='10' align='center'><b>SUBS:<b></td></tr>";
                            }
                        }
                        ?>
                        <tr>
                        <td colspan="2">Add <input type="number" id="addSubsHome" value="1" min="1" max="3"> subs &nbsp;
                        <button type="button" name="addSubsHomeButt" onclick="addSubsHomeF()">ADD</button></td>
                        <td colspan="4">Remove <input type="number" id="removeSubsHome" value="1" min="1" max="3"> subs
                        <button type="button" name="removeSubsHomeButt" onclick="removeSubsHomeF()">REMOVE</button></td>
                        </tr>
                        </table>
                    </div>  
                    <div id="playersAway" style="padding-left: 70px"> 
                        <table>
                        <?php
                        for($i = 1; $i <= 14; $i++){
                            $disabled = "";
                            if($i >= 12){
                               $disabled = "disabled"; 
                            }
                        ?>
                            <tr><td>Player <?php echo $i; ?>:</td>
                                <td><select id="playerAway<?php echo $i; ?>" name="playerAway<?php echo $i; ?>" <?php echo $disabled; ?>>
                            <option value="">Select Player...</option>
                            <?php
                            for($j = 0; $j < count($playersAwayInfo) / 3; $j++){
                            ?>
                                  <option value="<?php echo $playersAwayInfo[$j * 3]; ?>"><?php echo $playersAwayInfo[$j * 3 + 1]." ".$playersAwayInfo[$j * 3 + 2]; ?></option>
                            <?php
                            }
                            ?>
                        
                        </select></td>
                        <td>Minutes played: </td><td><input type="number" style="width:40px;" id="minsAway<?php echo $i; ?>" name="minsAway<?php echo $i; ?>" value="90" min="1" max="90" <?php echo $disabled; ?>></td> 
                        <td>PTS: </td><td><input type="number" style="width:40px;" id="ptsAway<?php echo $i; ?>" name="ptsAway<?php echo $i; ?>" value="2" <?php echo $disabled; ?>></td>
                        </tr>
                        <?php
                            if($i == 11){
                                echo "<tr><td colspan='10' align='center'><b>SUBS:<b></td></tr>";
                            }
                        }
                        ?>
                        <tr>
                        <td colspan="2">Add <input type="number" id="addSubsAway" value="1" min="1" max="3"> subs &nbsp;
                        <button type="button" name="addSubsAwayButt" onclick="addSubsAwayF()">ADD</button></td>
                        <td colspan="4">Remove <input type="number" id="removeSubsAway" value="1" min="1" max="3"> subs
                        <button type="button" name="removeSubsAwayButt" onclick="removeSubsAwayF()">REMOVE</button></td>
                        </tr>
                        
                        </table>
                    </div>
                    <input type="submit" name="submitInsertPts" value="INSERT POINTS">
                </div>
                
                <?php
                }
                else{
                    $msg = "No fixture in DB with given id!";
                    echo "<span style='color:white; background-color:red'>". $msg . "</span>";
                }
                    
            ?>
            </form>
        </div>
        <?php 
        }
        ?>
        <div id="displayFixturesPoints"></div>
    </body>
</html>