<?php

session_start();

if(!isset($_SESSION['id_user'])){ 
    header("Location: index.php");
    exit();   
}

if($_SESSION['picked_team'] == 0){ //show pick 15 players selection->only happens after registration
    header("Location: makeyoursquad.php");
    exit();
}
require_once 'dbconfig.php';
require_once 'Connection.inc.php'; 

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT * FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$activeGw = $array['active_gw'];//active_gw -> {1,2,...38,39(POSTSEASON)}
$pointsGw = $array['points_gw'];
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: index.php?gameUpdating");
    exit();
}

$user_id = $_SESSION['id_user'];

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script></script>
    </head>
    <body>
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <?php
        if(isset($_GET['leagueId'])){
            $query = "SELECT user_ranking, user_points, user_gw_points, team_name, login.id_user FROM users_league_points, login WHERE users_league_points.id_league='".$_GET['leagueId']."' AND users_league_points.id_user=login.id_user AND users_league_points.user_ranking IS NOT NULL ORDER BY users_league_points.user_ranking ASC";
            $result = $connection->getResult($query);
            ?>
            <table>
                <th>Ranking</th><th>Team name</th><th>GW Points</th><th>Overall Points</th>
        <?php
            while($array = mysqli_fetch_array($result)){
        ?>
                <tr><td><?php echo $array['user_ranking']; ?></td><td><a href="points.php?userId=<?php echo $array['id_user']; ?>"><?php echo $array['team_name']; ?></a></td>
        <td><?php echo $array['user_gw_points']; ?></td><td><?php echo $array['user_points']; ?></td></tr>
        <?php        
            }
            ?>
            </table><br>
            
        <?php
            $query = "SELECT login.team_name, login.id_user FROM users_league_points, login WHERE users_league_points.id_league='".$_GET['leagueId']."' AND users_league_points.id_user=login.id_user AND users_league_points.user_ranking IS NULL";
            $result = $connection->getResult($query);
            
            if(mysqli_num_rows($result)){
                echo "<h3>Teams to be added after next deadline</h3>";
                echo "<table>";
                echo "<th>Team name</th>";
                while($array = mysqli_fetch_array($result)){
        ?>
                    <tr><td><?php echo $array['team_name']; ?></td></tr>
        <?php        
                }
                echo "</table>";
            }           
        }
        else{
            //get all leagues that user participates in
            $query = "SELECT users_leagues.id_league, users_leagues.league_name, users_league_points.user_ranking FROM users_league_points, users_leagues WHERE id_user='".$user_id."' AND users_league_points.id_league=users_leagues.id_league";
            $result = $connection->getResult($query);
            ?>
            <table>
                <th>League</th><th>Ranking</th>
            <?php
            while($array = mysqli_fetch_array($result)){
            ?>
                <tr><td><a href="leagues.php?leagueId=<?php echo $array['id_league']; ?>"><?php echo $array['league_name']; ?></a></td>
                    <td><?php echo $array['user_ranking']?></td></tr>    
            <?php        
            }
            ?>
            </table>
        <?php        
            }
        ?>
        
       
    </body>
</html>