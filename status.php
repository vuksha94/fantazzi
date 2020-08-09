<?php

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
    header("Location: index.php");
    exit();
}

require_once 'dbconfig.php';
require_once 'Connection.inc.php'; 
require_once 'test_input.inc.php';

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

$disabledInsertTeams = "disabled";
if($insertedTeamsForActiveGw == 0){
    $disabledInsertTeams = "";
}

$disabledCloseChg = "";//for submit button for gw status
if($activeGw == 39 || $gameUpdating){
    $disabledCloseChg = "disabled";
}

?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <title></title>
        <script>
        
        function confirmForm(){
            var msg = "Are you shure that you want to close changes for GW" + <?php echo $activeGw; ?> + "?";
            var confirmation = confirm(msg);
            if(confirmation === true){
                return true;
            }
            else{
                return false;
            }
        }
        </script>
    </head>
    <body>
       <div class="navbar"> <?php include 'navbar.php'; ?> </div>
       <?php
            if(isset($_GET['insertPointsAndUpdateLeagues'])){
                if($_GET['insertPointsAndUpdateLeagues'] == "success"){
                    $msg = "Succesfully inserted user's points and updated leagues for GW".$pointsGw." !";
                    echo "<span style='color:white; background-color:green'>". $msg . "</span>";
                }
                elseif($_GET['insertPointsAndUpdateLeagues'] == "fail"){
                    $msg = "Unsuccesfully inserted user's points and updated leagues for GW".$pointsGw." !";
                    echo "<span style='color:white; background-color:red'>". $msg . "</span>";
                }
            }
       
            if(isset($_POST['submitDeadlineBtn'])){
                echo $_POST['activeGw']." is closed";
            }           
            ?>
       <fieldset>
           <legend>GW STATUS</legend>
           <p>Active GW: <?php if($activeGw >= 1 && $activeGw <= 38) echo $activeGw; else echo "POSTSEASON"; ?>  (myteam.php)</p>
           <p>Points GW: <?php if($activeGw != 1) echo $pointsGw; else echo "-"; ?>  (points.php)</p>
            <?php if($activeGw >= 1 && $activeGw <= 38){ ?>
                <p><?php if($gameUpdating) echo "Game is currently being updated! Changes in users teams are disabled!"; else echo "Game is open for changes"; ?></p>
                <?php if($gameUpdating){ ?>
                <p><?php if($insertedTeamsForActiveGw) echo "Teams for GW".$activeGw." are inserted! Set flag game_updating to \"false\" to allow changes for GW".$activeGw." !";
                         else echo "Teams for GW".$activeGw." are NOT inserted! Insert teams for GW".$activeGw." and then allow changes forGW".$activeGw.", so users can make changes to their teams for GW".$activeGw."!" ;
                   ?></p>
                <?php
                }
            }
            ?>
                <?php if($pointsGw != "") { 
                            if(!$gameUpdating){
                                echo "<p>";
                                if(!$leaguesUpdated){
                                    echo "Leagues for GW".$pointsGw." are not updated!";
                    ?>
                    Go to<a href="commit.php?type=usersPoints&action=insertPointsAndUpdateLeagues">[ INSERT USERS POINTS AND UPDATE LEAGUE POINTS ]</a>
                    when you insert points for all fixtures in GW<?php echo $pointsGw; ?>
                <?php       }
                            else{
                                echo "Leagues for GW".$pointsGw." are updated!";
                            }
                            echo "</p>";
                        }
                }
                ?>
            

       </fieldset>
       <form method="post" action="commit.php?type=gwStatus&action=closeChanges" onsubmit="return confirmForm()">
       <fieldset>
           <legend>GW DEADLINE</legend>
           <?php
            if($activeGw >= 1 && $activeGw <= 38){
                $query = "SELECT date,time FROM fixtures, gw_status WHERE gw_status.active_gw=fixtures.gw ORDER BY fixtures.date ASC, fixtures.time ASC LIMIT 1";
                $result = $connection->getResult($query);
                $row = mysqli_fetch_array($result);
                
                $time = $row['time'];
                $arrayTime = explode(":", $time);
                               
                $deadlineDate = date('D j M G:i', strtotime($row['date']) + 3600*((int)$arrayTime[0]) + 60*((int)$arrayTime[1]) - 3600);//-60*60 seconds for one hour before first match               
            }
            else{
                $deadlineDate = "NEXT SEASON";
            }
           ?>
           <p>Next deadline(GW<?php echo $activeGw; ?>): <?php echo $deadlineDate; ?> </p>
            <?php if($activeGw >= 1 && $activeGw <= 38){ ?>
                <p>CLOSE CHANGES FOR <?php echo "GW"; if($gameUpdating) echo $activeGw - 1; else echo $activeGw; ?><input type="submit" name="submitDeadlineBtn" value="CLOSE" <?php echo $disabledCloseChg; ?>>
                 <?php 
                     if($gameUpdating){
                     $msg = "You already have closed changes for GW".($activeGw - 1)." !";
                     echo "<span style='color:white; background-color:red'>".$msg. "</span>";
                     }
                 else{
                    $msg = "Press CLOSE button to close changes for GW".$activeGw."!";
                    echo "<span style='color:white; background-color:green'>".$msg. "</span>";
                 }
                 ?>
                </p>
            <?php } ?>
           <input type="hidden" name="activeGw" value="<?php echo $activeGw; ?>">
       </fieldset>
       </form>
       <?php
        if($activeGw >= 1 && $activeGw <= 38){ 
            if($gameUpdating){            
        ?>
       <form method="post" action="commit.php?type=gwStatus&action=insertTeams">
       <fieldset>
           <legend>INSERT TEAMS FOR NEXT GW</legend>
           <p>INSERT TEAMS IN DB FOR GW<?php echo $activeGw; ?> <input type="submit" name="submitInsertTeamsBtn" value="INSERT" <?php echo $disabledInsertTeams; ?>>
           <?php
           if($insertedTeamsForActiveGw){
               $msg = "You already have inserted teams for GW".$activeGw." !";
               echo "<span style='color:white; background-color:red'>".$msg. "</span>";
           }
           else{
               $msg = "Press INSERT button to insert teams in DB for GW".$activeGw." !";
               echo "<span style='color:white; background-color:green'>".$msg. "</span>";
           }
           ?>
           </p>
           <input type="hidden" name="activeGw" value="<?php echo $activeGw; ?>">
       </fieldset>
       </form>
       <?php } ?>
       <?php
            if($gameUpdating && $insertedTeamsForActiveGw){//open game for changes again, can't if teams aren't inserted yet   
        ?>
       <form method="post" action="commit.php?type=gwStatus&action=openGameForChanges">
       <fieldset>
           <legend>OPEN GAME FOR TEAM CHANGES </legend>
           <p>SET game_updating FLAG TO "FALSE"<input type="submit" name="submitOpenGameForChangesBtn" value="SET">
               <?php 
               $msg = "Press Set button to allow changes in user's teams!";
               echo "<span style='color:white; background-color:green'>".$msg. "</span>";
               ?>
           </p>
       </fieldset>
       </form>
       <?php }
        }
        ?>
    </body>
</html>