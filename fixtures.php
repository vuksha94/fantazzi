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


if(isset($_GET['insert']) && $admin){
    if($_GET['insert'] == "success"){
        $msg = "Succesfully entered ".$_GET['fixtures']." fixture";
        if($_GET['fixtures'] != 1){
            $msg .= "s!";
        }
        else{
            $msg .= "!";
        }
        echo "<span style='color:white; background-color:green'>". $msg . "</span>";
    }
    elseif($_GET['insert'] == "fail"){
        $msg = "Unsuccesfully inserted fixtures!";
        echo "<span style='color:white; background-color:red'>". $msg . "</span>";
    }
}
elseif(isset($_GET['update']) && $admin){
    if($_GET['update'] == "success"){        
        $msg = "Succesfully updated fixture!";
        echo "<span style='color:white; background-color:green'>". $msg . "</span>";
    }
    elseif($_GET['update'] == "fail"){
        $msg = "Unsuccesfully updated fixture!";
        echo "<span style='color:white; background-color:red'>". $msg . "</span>";
    }
}


?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script>
            var fixToAdd = 1;
            
            function addFixtures(){
                var fixToAddPlus = parseInt(document.getElementById("quantityadd").value, 10);
                if(fixToAddPlus < 1 || fixToAddPlus + fixToAdd > 10){
                    alert("Add cant be done!");
                    return false;
                }
                var i;
                for(i = fixToAdd + 1; i <= fixToAdd + fixToAddPlus; i++){
                    document.getElementById("gw" + i).disabled = false;
                    document.getElementById("home" + i).disabled = false;
                    document.getElementById("away" + i).disabled = false;
                    document.getElementById("date" + i).disabled = false;
                    document.getElementById("time" + i).disabled = false;
                }
                fixToAdd += fixToAddPlus;
                document.getElementById("numOfFixtures").value = fixToAdd;
            }
            function removeFixtures(){
                var fixToRem = parseInt(document.getElementById("quantityrem").value, 10);
                if(fixToRem > fixToAdd){
                    alert("Remove cant be done!");
                    return false;
                }
                var i;
                for(i = fixToAdd; i > fixToAdd - fixToRem; i--){
                    document.getElementById("gw" + i).disabled = true;
                    document.getElementById("home" + i).disabled = true;
                    document.getElementById("away" + i).disabled = true;
                    document.getElementById("date" + i).disabled = true;
                    document.getElementById("time" + i).disabled = true;
                }
                fixToAdd -= fixToRem;
                document.getElementById("numOfFixtures").value = fixToAdd;
            }
            function checkInput(){
                var i;
                for(i = 1; i <= fixToAdd; i++){
                    var home = document.getElementById("home" + i).value;
                    var away = document.getElementById("away" + i).value;
                    if(document.getElementById("gw" + i).value === "" || home === "" || away === ""
                            || document.getElementById("date" + i).value === "" || document.getElementById("time" + i).value === ""){
                        alert("You have to fill all inputs!");
                        return false;
                    }
                    if(home === away){
                        alert("Home and away cant be same teams!");
                        return false;
                    }
                }
                return true;
            }
            function checkUpdateInput(){
                var home = document.getElementById("homeUpdate").value;
                var away = document.getElementById("awayUpdate").value;
                if(document.getElementById("gwUpdate").value === "" || home === "" || away === ""
                            || document.getElementById("dateUpdate").value === "" || document.getElementById("timeUpdate").value === ""){
                        alert("You have to fill all inputs!");
                        return false;
                }
                if(home === away){
                    alert("Home and away cant be same teams!");
                    return false;
                }
                return true;
            }
            function LoadDocFx(url, cFunction, q)
            {                        
                if (window.XMLHttpRequest)
                {
                    xmlhttpd = new XMLHttpRequest();
                }
                else
                {
                    xmlhttpd = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttpd.onreadystatechange = function()
                {
                    if (xmlhttpd.readyState == 4 && xmlhttpd.status == 200)
                    {
                        cFunction(this);
                    }
                }
                xmlhttpd.open("GET", url + "?q=" + q, true);            
                xmlhttpd.send();
            }            
            function showFixtures(xhttp){
                document.getElementById("displayFixtures").innerHTML = 
                        xhttp.responseText;
            }
            function addBlockFixtures(){
                document.getElementById("addfixtures").style.display = "block";
                var addButton = document.getElementById("addFixtureButt");
                addButton.innerHTML = "CLOSE";
                addButton.onclick = function(){ removeBlockFixtures(); }               
            }
            function removeBlockFixtures(){
                document.getElementById("addfixtures").style.display = "none";
                var addButton = document.getElementById("addFixtureButt");
                addButton.innerHTML = "ADD FIXTURES";
                addButton.onclick = function(){ addBlockFixtures(); }
            }
            function cancelUpdateFixture(){//removes div with update fixture
                var updateDiv = document.getElementById("updateFixture");
                updateDiv.parentNode.removeChild(updateDiv);
            }
        </script>
        <style>
            #addfixtures{
                display: none;
            }
        </style>
    </head>
    <body onload="LoadDocFx('displayFixtures.php', showFixtures, '');">
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <?php  
        if(isset($_GET['action']) && $_GET['action'] == "update" && isset($_GET['id'])){//Update fixture
            if($admin){ ?>
            <div id="updateFixture"><h2>UPDATE FIXTURE</h2>
                <form id="updateForm" method="post" action="commit.php?type=fixture&action=update&id=<?php echo $_GET['id']; ?>" onsubmit="return checkUpdateInput()">
                    <?php
                        $query = "SELECT * FROM fixtures WHERE id_fixture='".$_GET['id']."'";
                        $result = $connection->getResult($query);
                        $row = mysqli_fetch_array($result);
                        
                        if(mysqli_num_rows($result) == 1){
                            $gw = $row['gw'];
                            $home = $row['home'];
                            $away = $row['away'];
                            $date = $row['date'];
                            $time = $row['time'];
                            
                            $query = "SELECT * from gameweek WHERE gw<>'POSTSEASON' ORDER BY id_gw ASC ";
                            $result = $connection->getResult($query);?>
                    <table class="elem--half">
                        <tr><td>GW: </td><td><select name="gw" id="gwUpdate">
                        <?php
                            while($row = mysqli_fetch_array($result)){
                                if($row['id_gw'] == $gw){
                                       $selected = "selected"; 
                                    }
                                    else{
                                        $selected = "";
                                    } ?>                          
                                <option value="<?php echo $row['id_gw']; ?>" <?php echo $selected; ?>><?php echo $row['gw'] ?></option>
                      <?php } ?>
                            </select></td></tr>
                        <tr><td>Home: </td><td><select name="home" id="homeUpdate">
                        <?php
                            $query = "SELECT * from clubs";
                            $result = $connection->getResult($query);
                            while($row = mysqli_fetch_array($result)){
                                if($row['id_club'] == $home){
                                    $selected = "selected";
                                }
                                else{
                                    $selected = "";
                                }
                        ?>  
                            <option value="<?php echo $row['id_club']; ?>" <?php echo $selected; ?>><?php echo $row['club_name'] ?></option>

                            <?php } ?>                    
                    </select></td></tr>
                    <tr><td>Away: </td><td><select name="away" id="awayUpdate">
                        <?php
                            $query = "SELECT * from clubs";
                            $result = $connection->getResult($query);
                            while($row = mysqli_fetch_array($result)){
                                if($row['id_club'] == $away){
                                    $selected = "selected";
                                }
                                else{
                                    $selected = "";
                                }
                        ?>  
                            <option value="<?php echo $row['id_club']; ?>" <?php echo $selected; ?>><?php echo $row['club_name'] ?></option>

                            <?php } ?>                    
                    </select></td></tr>
                    <tr><td>Date: </td><td><input type="date" name="date" id="dateUpdate" value="<?php echo $date; ?>" min="2017-08-01" max="2018-08-01"></td></tr>
                    <tr><td>Time: </td><td><input type="time" name="time" id="timeUpdate" value="<?php echo $time; ?>"></td></tr>
                    <tr><td><input type="submit" name="updateBtn" value="UPDATE"></td><td><input type="button" name="cancelUpdateBtn" value="CANCEL" onclick="cancelUpdateFixture()"></td></tr>
                </table>
                    
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
            else{
                header("Location: fixtures.php");
                exit();
            }
        }
        ?>
        <?php if($admin){//admin can add (update or delete) fixtures ?>
        <button type="button" name="addFixtureButt" id="addFixtureButt" onclick="addBlockFixtures()">ADD FIXTURES</button><br>      
        <div id="addfixtures"><h2>ADD FIXTURES</h2>
            <form id="fixturesForm" method="post" action="commit.php?type=fixture&action=insert" onsubmit="return checkInput()">
                <table>
                    <tr>
                      <td><select name="gw1" id="gw1">
                              <option value="" selected="selected">GW</option>
                        <?php
                            $query = "SELECT gw from fixtures ORDER BY gw DESC LIMIT 1";
                            $result = $connection->getResult($query);
                            $selected_gw = "";
                            if(mysqli_num_rows($result) != 0){
                                $selected_gw = mysqli_fetch_array($result)['gw'];
                            }
                            $query = "SELECT * from gameweek WHERE gw<>'POSTSEASON' ORDER BY id_gw ASC ";
                            $result = $connection->getResult($query);
                            
                            while($row = mysqli_fetch_array($result)){
                                if($selected_gw != ""){
                                    if($row['id_gw'] == $selected_gw){
                                       $selected = "selected"; 
                                    }
                                    else{
                                        $selected = "";
                                    }
                                }
                                else{
                                    $selected = "";
                                }
                        ?>  
                        <option value="<?php echo $row['id_gw']; ?>" <?php echo $selected; ?>><?php echo $row['gw'] ?></option>

                        <?php } ?>
                          </select></td>
                    <td><select name="home1" id="home1">
                        <option value="" selected="selected">HOME</option>
                        <?php
                            $query = "SELECT * from clubs";
                            $result = $connection->getResult($query);
                            while($row = mysqli_fetch_array($result)){
                        ?>  
                        <option value="<?php echo $row['id_club']; ?>"><?php echo $row['club_name'] ?></option>

                        <?php } ?>
                        </select></td>
                    <td><select name="away1" id="away1">
                        <option value="" selected="selected">AWAY</option>
                        <?php
                            $query = "SELECT * from clubs";
                            $result = $connection->getResult($query);
                            while($row = mysqli_fetch_array($result)){
                        ?>  
                        <option value="<?php echo $row['id_club']; ?>"><?php echo $row['club_name'] ?></option>

                        <?php } ?>
                        </select></td>
                        <td>Date: <input type="date" name="date1" id="date1" value="2017-08-01" min="2017-08-01" max="2018-08-01"></td>
                        <td>Time: <input type="time" name="time1" id="time1" value="16:00"></td>
                    </tr>
                                    
                    <?php  
                        for($i = 2; $i <= 10; $i++){
                    ?>
                    <tr>
                    <td><select name="gw<?php echo $i; ?>" id="gw<?php echo $i; ?>" disabled="disabled">
                        <option value="" selected="selected">GW</option>
                        <?php
                            $query = "SELECT gw from fixtures ORDER BY gw DESC LIMIT 1";
                            $result = $connection->getResult($query);
                            $selected_gw = "";
                            if(mysqli_num_rows($result) != 0){
                                $selected_gw = mysqli_fetch_array($result)['gw'];
                            }
                        
                            $query = "SELECT * from gameweek WHERE gw<>'POSTSEASON' ORDER BY id_gw ASC ";
                            $result = $connection->getResult($query);
                                      
                            while($row = mysqli_fetch_array($result)){
                                if($selected_gw != ""){
                                    if($row['id_gw'] == $selected_gw){
                                       $selected = "selected"; 
                                    }
                                    else{
                                        $selected = "";
                                    }
                                }
                                else{
                                    $selected = "";
                                }
                        ?>  
                        <option value="<?php echo $row['id_gw']; ?>" <?php echo $selected; ?>><?php echo $row['gw'] ?></option>

                        <?php } ?>
                        </select></td>
                    <td><select name="home<?php echo $i; ?>" id="home<?php echo $i; ?>" disabled="disabled">
                        <option value="" selected="selected">HOME</option>
                        <?php
                            $query = "SELECT * from clubs";
                            $result = $connection->getResult($query);
                            while($row = mysqli_fetch_array($result)){
                        ?>  
                        <option value="<?php echo $row['id_club']; ?>"><?php echo $row['club_name'] ?></option>

                        <?php } ?>
                        </select></td>
                    <td><select name="away<?php echo $i; ?>" id="away<?php echo $i; ?>" disabled="disabled">
                        <option value="" selected="selected">AWAY</option>
                        <?php
                            $query = "SELECT * from clubs";
                            $result = $connection->getResult($query);
                            while($row = mysqli_fetch_array($result)){
                        ?>  
                        <option value="<?php echo $row['id_club']; ?>"><?php echo $row['club_name'] ?></option>

                        <?php } ?>
                        </select></td>
                        <td>Date: <input type="date" name="date<?php echo $i; ?>" id="date<?php echo $i; ?>" value="2017-08-01" min="2017-08-01" max="2018-08-01" disabled="disabled"></td>
                        <td>Time: <input type="time" name="time<?php echo $i; ?>" id="time<?php echo $i; ?>" value="16:00" disabled="disabled"></td>

                <?php } ?>
                    </tr>               
                    <tr>
                        <td>Add <input type="number" id="quantityadd" value="1" min="1" max="9" size="1">&nbsp; fixtures
                        <button type="button" name="addfixtures" onclick="addFixtures()">ADD</button></td><td></td><td></td><td></td>
                        <td>Remove <input type="number" id="quantityrem" value="1" min="1" max="9" size="1">&nbsp; fixtures
                        <button type="button" name="removefixtures" onclick="removeFixtures()">REMOVE</button></td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center"><input type="submit" name="submitBtn" value="SUBMIT"></td>
                    </tr>
                </table>   
                                    
                <input type="hidden" id="numOfFixtures" name="numOfFixtures" value="1">           
            </form>
        </div>
        <?php } ?>
        <div id="displayFixtures" class="elem--half div--center"></div>
    </body>
</html>