<?php

session_start();

if(!isset($_GET['type']) || !isset($_GET['action'])){ 
    header("Location: index.php");
    exit();
}
if(!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
    //$admin = 0;
    header("Location: index.php");
    exit();
}

require_once 'dbconfig.php';
require_once 'Connection.inc.php'; 

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT * FROM gw_status";
$result = $connection->getResult($query);
$array = mysqli_fetch_array($result);

$activeGw = $array['active_gw'];
$pointsGw = $array['points_gw'];
$gameUpdating = (int)$array['game_updating'];
$leaguesUpdated = $array['leagues_updated'];
$playersStatsUpdated = $array['players_stats_updated'];
$insertedTeamsForActiveGw = (int)$array['inserted_teams_active_gw'];

switch($_GET['type']){
    case "usersPoints"://Insertion of user's points after all players stats in current gw are updated
        if($_GET['action'] == "insertPointsAndUpdateLeagues"){
            if(!$playersStatsUpdated){//if not all points for current points GW are inserted
                $connection->close();
                header("Location: updatePoints.php?allPlayersStatsUpdated=fail");
                exit();
            }
            if($leaguesUpdated){//cant update leagues twice
                $connection->close();
                header("Location: status.php?insertPointsAndUpdateLeagues=fail");
                exit();
            }
            //******* NO AUTOMATIC SUBS *******
            //update all users_teams db, column points_scored 
            $queryStats = "SELECT id_player, mins_played, points_scored FROM players_stats, fixtures WHERE fixtures.gw='".$pointsGw."' AND players_stats.id_fixture=fixtures.id_fixture";
            $playersPoints = array();//$playersPoints = {id_player_1, mins_played_1|points_scored_1,
                                    //                      id_player_2, mins_played_2|points_scored_2, ...}
            $resultStats = $connection->getResult($queryStats);
            $playerStats = "";
            while($arrayStats = mysqli_fetch_array($resultStats)){
                array_push($playersPoints, (int)$arrayStats['id_player']);
                $playerStats = $arrayStats['mins_played']."|".$arrayStats['points_scored'];
                array_push($playersPoints, $playerStats);

            }
            
            $query = "SELECT * FROM users_teams,formations WHERE gw='".$pointsGw."' AND formation=id_formation";
            $result = $connection->getResult($query);
            while($array = mysqli_fetch_array($result)){
                $players = array();
                for($i = 4; $i <= 18; $i++){
                    array_push($players, $array[$i]);
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
                $captain = $array[22]; //$vice_captain = $array[23];
                
                $points = 0;//calculate points for GW
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
                $transferCost = (int)$array['transfer_cost'];
                $points += $transferCost;
                $queryUpdatePoints = "UPDATE users_teams SET points='".$points."' WHERE id_user='".$array['id_user']."' AND gw='".$pointsGw."'";
                $resultUpdatePoints = $connection->getResult($queryUpdatePoints);
                //unset array of players
                for($i = 0; $i < count($players); $i++){
                    unset($players[$i]);
                }                
                //update leagues
                $queryUpdateLeagues = "UPDATE users_league_points SET user_points=user_points + '".$points."',user_gw_points='".$points."' WHERE id_user='".$array['id_user']."'";
                $resultUpdateLeagues = $connection->getResult($queryUpdateLeagues);               
            }
            //update rankings
            //get all leagues
            $query = "SELECT id_league FROM users_leagues";
            $result = $connection->getResult($query);
            while($array = mysqli_fetch_array($result)){
                $query1 = "SELECT id_user FROM users_league_points WHERE id_league='".
                        $array['id_league']."' ORDER BY user_points DESC";
                $result1 = $connection->getResult($query1);
                //go trough all users in league and update rankings
                $i = 1;
                while($array1 = mysqli_fetch_array($result1)){
                    $query2 = "UPDATE users_league_points SET user_ranking='".$i++."' WHERE id_user='".$array1['id_user']."'";
                    $result2 = $connection->getResult($query2);                    
                }
            }            
            //update flag leagues_updated
            $queryUpdateGwStatus = "UPDATE gw_status SET leagues_updated=1";
            $resultUpdateGwStatus = $connection->getResult($queryUpdateGwStatus);
            $connection->close();
            header("Location: status.php?insertPointsAndUpdateLeagues=success");
            exit();
        }
        
        break;
    case "playerPoints":    //Insertion of players points in current gw points
        if($_GET['action'] == "insertPoints" && isset($_POST['submitInsertPts'])){
            $gw = $_POST['gw'];
            if($pointsGw != $gw){
                $connection->close();
                header("Location: updatePoints.php?insertPoints=fail&idFixture=".$idFixture);
                exit();
            }
            $idFixture = $_POST['idFixture'];
            $homeShort = $_POST['homeShort'];
            $awayShort = $_POST['awayShort'];
            $homeScore = $_POST['homeResult'];
            $awayScore = $_POST['awayResult'];
            $numHomPlayers = $_POST['numHomePlayers'];
            $numAwayPlayers = $_POST['numAwayPlayers'];
            
            $playersHome = array();// $playersHome = {id_player1, mins_played1, pts1, id_player2, mins_played2, pts2, ... }
            for($i = 1; $i <= $numHomPlayers; $i++){
                array_push($playersHome, $_POST['playerHome'.$i]);
                array_push($playersHome, $_POST['minsHome'.$i]);
                array_push($playersHome, $_POST['ptsHome'.$i]);
            }
            $playersAway = array();// $playersHome = {id_player1, mins_played1, pts1, id_player2, mins_played2, pts2, ... }
            for($i = 1; $i <= $numAwayPlayers; $i++){
                array_push($playersAway, $_POST['playerAway'.$i]);
                array_push($playersAway, $_POST['minsAway'.$i]);
                array_push($playersAway, $_POST['ptsAway'.$i]);
            }
            
            $playersNotPlayed = array();//players from both clubs that havent featured in submited games -> 0 mins, 0 pts
            
            $queryNotPlayed = "SELECT id_player FROM players WHERE (club='".$homeShort."' OR club='".$awayShort."') AND";
            for($i = 0; $i < count($playersHome) / 3; $i++){
                $queryNotPlayed .= " id_player<>'".$playersHome[$i*3]."' AND";
            }
            for($i = 0; $i < count($playersAway) / 3; $i++){
                $queryNotPlayed .= " id_player<>'".$playersAway[$i*3]."'";  
                if($i != count($playersAway)/3 - 1){
                    $queryNotPlayed .= " AND";
                }                
            }
            if($result = $connection->getResult($queryNotPlayed)){//get all players that havent been submitted in form and set mins_played=0 , points_scored=0
                while($array = mysqli_fetch_array($result)){
                    array_push($playersNotPlayed, $array['id_player']);
                }
            }
            
            $queryResult = "UPDATE fixtures SET home_score='".$homeScore."',away_score='".$awayScore.
                            "',result_updated=1,points_updated=1 WHERE id_fixture='".$idFixture."'";
            $queryPoints = "INSERT INTO players_stats (id_player,id_fixture,mins_played,points_scored) VALUES ";
            
            for($i = 0; $i < count($playersHome) / 3; $i++){
                $queryPoints .= "('".$playersHome[$i*3]."','".$idFixture."','".$playersHome[$i*3 + 1]."','".$playersHome[$i*3 + 2]."'),";
            }
            for($i = 0; $i < count($playersAway) / 3; $i++){
                $queryPoints .= "('".$playersAway[$i*3]."','".$idFixture."','".$playersAway[$i*3 + 1]."','".$playersAway[$i*3 + 2]."'),";               
            }
            for($i = 0; $i < count($playersNotPlayed); $i++){
                $queryPoints .= "('".$playersNotPlayed[$i]."','".$idFixture."','0','0')"; 
                if($i != count($playersNotPlayed) - 1){
                    $queryPoints .= ",";
                }
            }
            echo "queryResult= ".$queryResult."<br>";
            echo "queryPoints= ".$queryPoints."<br>";
            
            if($connection->getResult($queryResult) && $connection->getResult($queryPoints)){
                //check if all fixtures in current gw are inserted
                $query = "SELECT * FROM fixtures, gw_status WHERE gw_status.points_gw=fixtures.gw";
                $result = $connection->getResult($query);
                $allGwFixturesPointsUpdated = true;//flag that shows if all players points for current GW are updated
                while($row = mysqli_fetch_array($result)){
                    if($allGwFixturesPointsUpdated){
                        if($row['points_updated'] == 0){
                            $allGwFixturesPointsUpdated = false;
                        }
                    }
                }
                if($allGwFixturesPointsUpdated){
                    $query = "UPDATE gw_status SET players_stats_updated='1'";
                    $result = $connection->getResult($query);
                }

                $connection->close();
                header("Location: updatePoints.php?insertPoints=success&idFixture=".$idFixture);
                exit();
            }
            else{
                $connection->close();
                header("Location: updatePoints.php?insertPoints=fail&idFixture=".$idFixture);
                exit();
            }
            
            
        }
        break;
    case "gwStatus":
        if($_GET['action'] == "closeChanges" && isset($_POST['submitDeadlineBtn'])){
            $activeGw = $_POST['activeGw'];
            if($activeGw >= 1 && $activeGw <= 38){
                $query = "UPDATE gw_status SET active_gw='".($activeGw + 1)."',points_gw='".$activeGw."',game_updating=1,leagues_updated=0,players_stats_updated=0,inserted_teams_active_gw=0";
                //$result = $connection->getResult($query);
                if($result = $connection->getResult($query)){
                    $connection->close();
                    header("Location: status.php?closeChanges=success");
                    exit();
                }
                else{
                    $connection->close();
                    header("Location: status.php?closeChanges=fail");
                    exit();
                }
            }
            elseif($activeGw == 39){//POSTSEASON
                $connection->close();
                header("Location: status.php?postseason");
                exit();
            }
            
        }
        elseif($_GET['action'] == "insertTeams" && isset($_POST['submitInsertTeamsBtn'])){//Copy all rows from users teams only with increasing of gw
            $activeGw = $_POST['activeGw'];
            $query = "SELECT * FROM users_teams WHERE gw='".($activeGw - 1)."'";
            $result = $connection->getResult($query);
            $numRows = mysqli_num_rows($result);
            
            $insertQuery = "INSERT INTO `users_teams`(`id_user`, `gw`, `formation`, `gk1`, `gk2`, `def1`, `def2`, `def3`, `def4`, `def5`,"
                    . " `mid1`, `mid2`, `mid3`, `mid4`, `mid5`, `fwd1`, `fwd2`, `fwd3`, `sub_1`, `sub_2`, `sub_3`, `captain`, `vice_captain`) VALUES ";
             
            $increaseTransfersQuery = "UPDATE login SET free_transfers=CASE free_transfers WHEN '0' THEN '1' WHEN '1' THEN '2' WHEN '2' THEN '2' WHEN '10' THEN '1' END WHERE picked_team='1'";
            
            if($numRows != 0){//if db users_teams isn't empty
                while($array = mysqli_fetch_array($result)){//go through result and copy every row of squad for next gw
                    $numRows--;
                    $insertQuery .= "('".$array['id_user']."','".$activeGw."','".$array['formation']."','".$array['gk1']."','".$array['gk2']."','".$array['def1']."','".$array['def2']."','".$array['def3']."','".$array['def4']."','".$array['def5']."','".$array['mid1']."',"
                                    ."'".$array['mid2']."','".$array['mid3']."','".$array['mid4']."','".$array['mid5']."','".$array['fwd1']."','".$array['fwd2']."','".$array['fwd3']."','".$array['sub_1']."','".$array['sub_2']."','".$array['sub_3']."',"
                                    ."'".$array['captain']."','".$array['vice_captain']."')";
                    if($numRows != 0){//checks if is the last row
                        $insertQuery .= ",";
                    }
                }
                if($connection->getResult($insertQuery) && $connection->getResult($increaseTransfersQuery)){//INSERT in users teams, and increase one free transfer
                    $query  = "UPDATE gw_status SET inserted_teams_active_gw=1";//*********What if one of if conditions fails???, one db is updated, other is not*******************
                    if($result = $connection->getResult($query)){
                        $connection->close();
                        header("Location: status.php?insertTeams=succes");
                        exit();
                    }
                }                
                $connection->close();
                header("Location: status.php?insertTeams=fail");
                exit();                
            }
            else{
                $connection->close();
                header("Location: status.php?insertTeams=fail&dbIsEmpty");
                exit();
            }          
        }
        elseif($_GET['action'] == "openGameForChanges" && isset($_POST['submitOpenGameForChangesBtn'])){
            $query = "UPDATE gw_status SET game_updating=0";
            if($result = $connection->getResult($query)){
                $connection->close();
                header("Location: status.php?openGameForChanges=succes");
                exit();
            }
            else{
                $connection->close();
                header("Location: status.php?openGameForChanges=fail");
                exit();
            }
        }
        else{
            $connection->close();
            header("Location: status.php");
            exit();
        }
        break;
    case "fixture":
        if($_GET['action'] == "insert"){
            if(isset($_POST['submitBtn'])){
                $numOfFixtures = (int)$_POST['numOfFixtures'];
                echo $numOfFixtures;
                $gw = array();
                $home = array();
                $away = array();
                $date = array();
                $time = array();

                for($i = 1; $i <= $numOfFixtures; $i++){
                    $gwName = "gw".$i;
                    $homeName = "home".$i;
                    $awayName = "away".$i;
                    $dateName = "date".$i;
                    $timeName = "time".$i;
                    array_push($gw, $_POST[$gwName]);
                    array_push($home, $_POST[$homeName]);
                    array_push($away, $_POST[$awayName]);
                    array_push($date, $_POST[$dateName]);
                    array_push($time, $_POST[$timeName]);
                }
                if(array_search("", $gw) !== false || array_search("", $home) !== false || array_search("", $away) !== false
                        || array_search("", $date) !== false || array_search("", $time) !== false) {
                    $connection->close();
                    header("Location: fixtures.php?error");
                    exit();
                }
                for($i = 0; $i < $numOfFixtures; $i++){
                    if($home[$i] == $away[$i]){
                        $connection->close();
                        header("Location: fixtures.php?errorSameClub");
                        exit();
                    }
                }

                $query = "INSERT INTO fixtures (gw, home, away, date, time) VALUES";
                for($i = 0; $i < $numOfFixtures; $i++){
                    $query .= " ('".$gw[$i]."', '".$home[$i]."', '".$away[$i]."', '".$date[$i]."', '".$time[$i]."')";
                    if($i != $numOfFixtures - 1){
                        $query .= ",";
                    }           
                }       
                if($result = $connection->getResult($query)){
                    $connection->close();
                    header("Location: fixtures.php?insert=success&fixtures=".$numOfFixtures);
                    exit();
                }
                else{
                    $connection->close();
                    header("Location: fixtures.php?insert=fail");
                    exit();
                }

            }
            else{
                $connection->close();
                header("Location: fixtures.php");
                exit();
            }
        }
        elseif($_GET['action'] == "update"){
            if(isset($_POST['updateBtn'])){
                $gwName = $_POST['gw'];
                $homeName = $_POST['home'];
                $awayName = $_POST['away'];
                $dateName = $_POST['date'];
                $timeName = $_POST['time'];

                if($gwName == "" || $homeName == "" || $awayName == "" || $dateName == "" || $timeName == ""){
                    $connection->close();
                    header("Location: fixtures.php?error");
                    exit();
                }
                $query = "UPDATE fixtures SET gw='".$gwName."', home='".$homeName.
                        "', away='".$awayName."', date='".$dateName."', time='".$timeName."' WHERE id_fixture='".$_GET['id']."'";
                if($result = $connection->getResult($query)){
                    $connection->close();
                    header("Location: fixtures.php?update=success");
                    exit();
                }
                else{
                    $connection->close();
                    header("Location: fixtures.php?update=fail");
                    exit();
                }

            }
            else{
                $connection->close();
                header("Location: fixtures.php");
                exit();
            }
        }
        elseif($_GET['action'] == "delete"){

        }
        else{
            $connection->close();
            header("Location: fixtures.php");
            exit();
        }
        break;
}




?>