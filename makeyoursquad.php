<?php

session_start();

if(!isset($_SESSION['id_user'])){ 
    header("Location: index.php");
    exit();   
}

if($_SESSION['picked_team'] != 0){ 
    header("Location: myteam.php");
    exit();
}

require_once 'dbconfig.php';
require_once 'Connection.inc.php'; 

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$user_id = $_SESSION['id_user'];

$query = "SELECT * FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$id_gw = $array['active_gw'];//active_gw -> {1,2,...38,39(POSTSEASON)}
$pointsGw = $array['points_gw'];
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: index.php");
    exit();
}

if(isset($_POST['submitBtn'])){
    echo "Upis podataka u bazu...!";    
    //***test $result if succesful every time you have the function call $connection->getResult($query) or mysqli_query($this->link, $query)***
    
    
    $gkp1 = $_POST['gkp1_id']; $gkp2 = $_POST['gkp2_id'];
    $def1 = $_POST['def1_id']; $def2 = $_POST['def2_id']; $def3 = $_POST['def3_id']; $def4 = $_POST['def4_id']; $def5 = $_POST['def5_id'];
    $mid1 = $_POST['mid1_id']; $mid2 = $_POST['mid2_id']; $mid3 = $_POST['mid3_id']; $mid4 = $_POST['mid4_id']; $mid5 = $_POST['mid5_id'];
    $fwd1 = $_POST['fwd1_id']; $fwd2 = $_POST['fwd2_id']; $fwd3 = $_POST['fwd3_id'];
    
    //default formation is 1. 4-4-2
    $formation = 1;
    //*** make formation to be changable with code that will determine dinamically $sub1, $sub2, @sub3 ***
    //$sub1, $sub2, @sub3 statically
    $sub1 = $fwd3;
    $sub2 = $mid5;
    $sub3 = $def5;
    //default captain and vice-captain
    $captain = $fwd1;
    $vice_captain = $mid1;
    
    //***have to make server side check of $_POST *** if javascript is disabled ***
    
    $query = "INSERT INTO `users_teams`(`id_user`, `gw`, `formation`, `gk1`, `gk2`, `def1`, `def2`, `def3`, ".
            "`def4`, `def5`, `mid1`, `mid2`, `mid3`, `mid4`, `mid5`, `fwd1`, `fwd2`, `fwd3`, `sub_1`, `sub_2`, `sub_3`, `captain`, `vice_captain`) ".
            "VALUES ('".$user_id."','".$id_gw."','".$formation."','".$gkp1."','".$gkp2."','".$def1."','".$def2."','".$def3."',".
            "'".$def4."','".$def5."','".$mid1."','".$mid2."','".$mid3."','".$mid4."','".$mid5."','".$fwd1."','".$fwd2."','".$fwd3."',".
            "'".$sub1."','".$sub2."','".$sub3."','".$captain."','".$vice_captain."')";
    $connection->getResult($query);
    //insert player in overall league
    $query = "INSERT INTO users_league_points (id_user, id_league) VALUES ('".$user_id."','1')";
    $connection->getResult($query);
    
    $bank = $_POST['bank'];

    $query = "UPDATE login SET picked_team='1', bank='".$bank."', registration_gw='".$id_gw."' WHERE id_user='".$user_id."'";
    $connection->getResult($query);
    
    $_SESSION['picked_team'] = 1;
    $_SESSION['registration_gw'] = $id_gw;

    header("Location: myteam.php");
    exit();
    
}
else{
    $query = "SELECT bank FROM login WHERE id_user='".$user_id."'";
    $result = $connection->getResult($query);
    
    $bank = mysqli_fetch_array($result)['bank'];
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script>
            
            var cnt = 0;//number of picked players
            var gkps = new Array();//array of indexes of gkps picked ex. {1} - one gkp picked
            var defs = new Array();//array of indexes of defs picked ex. {1,3,4,5} - 4 defs picked, 2nd removed
            var mids = new Array();//
            var fwds = new Array();//
            var players = new Array();//Array of idPlayer
            var clubs = new Array();//Array of clubs-check for 3 or less players from same club rule
                                    //ex. clubs = {"ARS", 2, "CHE", 3, ...}
            var bank = <?php echo $bank; ?>;
            
            function onLoadFunction(){
                loadDoc('displayPlayers.php', showPlayers, '1', '30');
                LoadDocFx('displayFixtures.php', showFixtures, '');
                //writeMoneyInBank();
            }/*
            function writeMoneyInBank(){
                document.getElementById("bank").value = bank;
            }
            */
            //both functions removePlayer and purchasePlayer are dependable of number of input fields in form***maybe bad***
            
            function removePlayer(playerInfo){
                var info = playerInfo.split("|");
                
                var idPlayer = info[0];
                var club = info[1];
                //var name = info[2];
                //var lastName = info[3];
                var position = info[4];
                var price = info[5];
                
                //reduce number of players from this club
                var indexOfClub = clubs.indexOf(club);//returns index of club in Array of purchased player club, -1 if doesnt contain 
                if(clubs[indexOfClub + 1] === 1){//delete this club from array clubs
                    clubs.splice(indexOfClub, 2);
                }   
                else{//just reduce number of players for the club that is already 2 or 3
                    clubs[indexOfClub + 1]--;
                }
                //find index in form of player to be removed with help of hidden form and id_player
                var index, id, idHidd;
                //var form = document.getElementById("mysquadform");
                switch(position){
                    case "GKP":
                        //find index and update gkps array
                        for(index = 1; index <= 2; index++){
                            id = "gkp_" + index;  
                            idHidd = "gkp" + index + "_id";
                            if(document.getElementById(idHidd).value === idPlayer){
                                break;
                            }
                        }
                        gkps.splice(gkps.indexOf(index), 1);
                        break;
                    case "DEF":
                        //find index and update defs array
                        for(index = 1; index <= 5; index++){
                            id = "def_" + index; 
                            idHidd = "def" + index + "_id";
                            if(document.getElementById(idHidd).value === idPlayer){
                                break;
                            }
                        }
                        defs.splice(defs.indexOf(index), 1);   
                        break;
                    case "MID":
                        //find index and update mids array
                        for(index = 1; index <= 5; index++){
                            id = "mid_" + index;     
                            idHidd = "mid" + index + "_id";
                            if(document.getElementById(idHidd).value === idPlayer){
                                break;
                            }
                        }
                        mids.splice(mids.indexOf(index), 1);   
                        break;
                    case "FWD":
                        //find index and update fwds array
                        for(index = 1; index <= 5; index++){
                            id = "fwd_" + index;        
                            idHidd = "fwd" + index + "_id";
                            if(document.getElementById(idHidd).value === idPlayer){
                                break;
                            }
                        }
                        fwds.splice(fwds.indexOf(index), 1);   
                        break;                     
                }                
                bank += price - "0";
                
                var inputElement = document.getElementById(id);
                var inputButton = document.getElementById("img_" + idPlayer);//REMOVE button has to be deleted
                
                inputElement.value = "";//delete player
                inputElement.parentNode.removeChild(inputButton);
                document.getElementById(idHidd).value = "";//delete hidden id_player
                var bankElement = document.getElementById("bank");
                bankElement.value = bank;//update bank
                if(bank < 0){
                    bankElement.style.backgroundColor = "red";
                }
                else{
                    bankElement.style.backgroundColor = "white";
                }
                
                if(document.getElementById(idPlayer)){//change content of buttons in xhttp list
                    document.getElementById(idPlayer).onclick = function(){ purchasePlayer(playerInfo);}//change REMOVE -> PURCHASE for removed player 
                    document.getElementById(idPlayer).innerHTML = "PURCHASE";                    
                }
  
                players.splice(players.indexOf(idPlayer), 1);//delete player from array of id_player
                cnt--;
                document.getElementById("confirmButton").disabled = true;//disable confirm button
                return false;
            }
            function purchasePlayer(playerInfo){
                var info = playerInfo.split("|");
                
                var idPlayer = info[0];
                var club = info[1];
                //var name = info[2];
                var lastName = info[3];
                var position = info[4];
                var price = info[5];
                
                var id, idHidd;//id of input text and hidden elements where players are put
                
                if(cnt === 15){
                    alert("Already have 15 players!");
                    return false;
                }
                //check if user already has 3 players from same club
                var indexOfClub = clubs.indexOf(club);//returns index of club in Array of purchased player club, -1 if doesnt contain 
                if(indexOfClub !== -1 && clubs[indexOfClub + 1] === 3){
                    alert("Too many players from " + club + "!");
                    return false;
                }
                
                switch(position){
                    case "GKP":
                        if(gkps.length === 2){
                            alert("Too many goalkeepers!");
                            return false;
                        }
                        else{                        
                            var i;
                            //prolazak kroz niz indeksa gkps zbog moguceg prethodnog brisanja nekog od igraca
                            for(i = 1; i <= 2; i++){
                                if(gkps.indexOf(i) === -1){
                                    id = "gkp_" + i;
                                    idHidd = "gkp" + i + "_id";
                                    gkps.push(i);
                                    break;
                                }
                            }
                        }
                        break;
                    case "DEF":
                        if(defs.length === 5){
                            alert("Too many defenders!");
                            return false;
                        }
                        else{
                            var i;
                            //prolazak kroz niz indeksa defs zbog moguceg prethodnog brisanja nekog od igraca
                            for(i = 1; i <= 5; i++){
                                if(defs.indexOf(i) === -1){
                                    id = "def_" + i;
                                    idHidd = "def" + i + "_id";
                                    defs.push(i);
                                    break;
                                }
                            }                                                                            
                        }                        
                        break;
                    case "MID":
                        if(mids.length === 5){
                            alert("Too many midfielders!");
                            return false;
                        }
                        else{
                            var i;
                            //prolazak kroz niz indeksa mids zbog moguceg prethodnog brisanja nekog od igraca
                            for(i = 1; i <= 5; i++){
                                if(mids.indexOf(i) === -1){
                                    id = "mid_" + i;
                                    idHidd = "mid" + i + "_id";
                                    mids.push(i);
                                    break;
                                }
                            }
                        }                        
                        break;
                    case "FWD":
                        if(fwds.length === 3){
                            alert("Too many forwards!");
                            return false;
                        }
                        else{
                            var i;
                            //prolazak kroz niz indeksa fwds zbog moguceg prethodnog brisanja nekog od igraca
                            for(i = 1; i <= 5; i++){
                                if(fwds.indexOf(i) === -1){
                                    id = "fwd_" + i;
                                    idHidd = "fwd" + i + "_id";
                                    fwds.push(i);                                   
                                    break;
                                }
                            }
                        }                        
                        break;                        
                }
                //save the club for check 3- players from same club
                if(indexOfClub === -1){
                    clubs.push(club);
                    clubs.push(1);
                }
                else{
                    clubs[indexOfClub + 1]++;
                }
                bank -= price - "0";
                
                var inputElement = document.getElementById(id);
                
                inputElement.value = lastName + "(" + club + "), " + price;//write name in textbox
                document.getElementById(idHidd).value = idPlayer;//id_player is written to hidden field               
                var bankElement = document.getElementById("bank"); 
                bankElement.value = bank;//update bank
                if(bank < 0){
                    bankElement.style.backgroundColor = "red";
                }
                else{
                    bankElement.style.backgroundColor = "white";
                }
                              
                var removeImg = document.createElement("IMG");
                removeImg.setAttribute("SRC", "images/download.png");
                removeImg.setAttribute("ID", "img_" + idPlayer);
                removeImg.setAttribute("height", "20");
                removeImg.setAttribute("width", "20");
                removeImg.onclick = function(){ removePlayer(playerInfo);}
                
                inputElement.parentNode.appendChild(removeImg);       
                document.getElementById(idPlayer).onclick = function(){ removePlayer(this.value);}//change PURCHASE -> REMOVE for selected player 
                document.getElementById(idPlayer).innerHTML = "REMOVE";
                players.push(idPlayer);
                cnt++;               
                if(cnt == 15){//if 15 players selected, enable confirm button
                    document.getElementById("confirmButton").disabled = false;
                }
                return false;
            }            
            function loadDoc(url, cFunction, page, limit)//display players
            {
                var club = document.searchPlayersForm.clubs.value;
                var position = document.searchPlayersForm.position.value;
                var sort = document.searchPlayersForm.sort.value;
                var playerName = document.searchPlayersForm.playername.value;
                var arrayOfPlayers = players.join("|");//make a delimiter array of id_players for ex. 1|13|55|
                
                                           
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
                xmlhttp.open("GET", url + "?club=" + club + "&position=" + position + "&sort=" + sort + "&playerName=" + playerName + "&players=" + arrayOfPlayers + "&page=" + page + "&limit=" + limit, true);             
                xmlhttp.send();
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
            function showPlayers(xhttp){
                document.getElementById("displayPlayers").innerHTML = 
                        xhttp.responseText;
            }
            function showFixtures(xhttp){
                document.getElementById("displayFixtures").innerHTML = 
                        xhttp.responseText;
            }
            function checkInput(){
                if(cnt < 15){
                    alert("Select 15 players!");
                    return false;
                }
                if(bank < 0){
                    alert("You dont have enough money!");
                    return false;
                }
                return true;
            }

        </script>
    </head>
    <body onload="onLoadFunction()">
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <div>            
            <div id="pickteam">
                <h3>PICK 15 PLAYERS</h3>
                <form id="mysquadform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"
                      onsubmit="return checkInput()">
                    <center>Bank: <input type="text" name="bank" id="bank" value="100" size="5" readonly="readonly"></center>
                    <div class="startingeleven">
                    <center>
                        
                    <table>
                        <tr><td></td><td>GKP_1<input type="text" id="gkp_1" value="" size="16" readonly="readonly"></td>
                            <td></td><td>GKP_2<input type="text" id="gkp_2" value="" size="16" readonly="readonly"></td>
                        <td></td></tr>
                        <tr><td>DEF_1<input type="text" id="def_1" value="" size="16" readonly="readonly"></td>
                            <td>DEF_2<input type="text" id="def_2" value="" size="16" readonly="readonly"></td>
                            <td>DEF_3<input type="text" id="def_3" value="" size="16" readonly="readonly"></td>
                            <td>DEF_4<input type="text" id="def_4" value="" size="16" readonly="readonly"></td>
                            <td>DEF_5<input type="text" id="def_5" value="" size="16" readonly="readonly"></td>
                        </tr>
                        <tr><td>MID_1<input type="text" id="mid_1" value="" size="16" readonly="readonly"></td>
                            <td>MID_2<input type="text" id="mid_2" value="" size="16" readonly="readonly"></td>
                            <td>MID_3<input type="text" id="mid_3" value="" size="16" readonly="readonly"></td>
                            <td>MID_4<input type="text" id="mid_4" value="" size="16" readonly="readonly"></td>
                            <td>MID_5<input type="text" id="mid_5" value="" size="16" readonly="readonly"></td>
                        </tr>   
                        <tr><td></td>
                            <td>FWD_1<input type="text" id="fwd_1" value="" size="16" readonly="readonly"></td>
                            <td>FWD_2<input type="text" id="fwd_2" value="" size="16" readonly="readonly"></td>
                            <td>FWD_3<input type="text" id="fwd_3" value="" size="16" readonly="readonly"></td>
                        <td></td></tr>  
                        
                        <tr><td><input type="hidden" name="gkp1_id" id="gkp1_id" value="" size="16"></td>
                            <td><input type="hidden" name="gkp2_id" id="gkp2_id" value="" size="16"></td>
                        </tr>
                        <tr><td><input type="hidden" name="def1_id" id="def1_id" value="" size="16"></td>
                            <td><input type="hidden" name="def2_id" id="def2_id" value="" size="16"></td>
                            <td><input type="hidden" name="def3_id" id="def3_id" value="" size="16"></td>
                            <td><input type="hidden" name="def4_id" id="def4_id" value="" size="16"></td>
                            <td><input type="hidden" name="def5_id" id="def5_id" value="" size="16"></td>
                        </tr>
                        <tr><td><input type="hidden" name="mid1_id" id="mid1_id" value="" size="16"></td>
                            <td><input type="hidden" name="mid2_id" id="mid2_id" value="" size="16"></td>
                            <td><input type="hidden" name="mid3_id" id="mid3_id" value="" size="16"></td>
                            <td><input type="hidden" name="mid4_id" id="mid4_id" value="" size="16"></td>
                            <td><input type="hidden" name="mid5_id" id="mid5_id" value="" size="16"></td>
                        </tr>   
                        <tr><td><input type="hidden" name="fwd1_id" id="fwd1_id" value="" size="16"></td>
                            <td><input type="hidden" name="fwd2_id" id="fwd2_id" value="" size="16"></td>
                            <td><input type="hidden" name="fwd3_id" id="fwd3_id" value="" size="16"></td>
                        </tr>                                         
                    </table>
                    </center> 
                    </div>
                    <center><input id="confirmButton" class='btn confirmation--button' type="submit" name="submitBtn" value="CONFIRM" disabled="disabled"></center>
                </form>                 
            </div>
            <form name="searchPlayersForm">
                <select name="clubs" onchange="loadDoc('displayPlayers.php', showPlayers, '1', '30')">
                    <option value="" selected>Select a club</option>
                    <?php
                        $query = "SELECT * FROM clubs";
                        $result = $connection->getResult($query);
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='".$row['club_shortname']."'>".$row['club_name']."</option>";
                        }                    
                    ?>
                </select>
                <select name="position" onchange="loadDoc('displayPlayers.php', showPlayers, '1', '30')">
                    <option value="" selected>Select a position</option>
                    <?php
                        $query = "SELECT * FROM positions";
                        $result = $connection->getResult($query);
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='".$row['position_shortname']."'>".$row['position_name']."</option>";
                        }                    
                    ?>
                </select>
                <select name="sort" onchange="loadDoc('displayPlayers.php', showPlayers, '1', '30')">
                    <option value="" selected>Sort by</option>
                    <option value="price">Price</option>
                </select> 
                <input type="text" name="playername" placeholder="Search..." onkeyup="loadDoc('displayPlayers.php', showPlayers, '1', '30')">
            </form>
            <div class="div--full"><div class="div--left elem--half" id="displayPlayers"></div><div id="displayFixtures" class="elem--half div--right"></div></div>
        </div>
        
    </body>
</html>
<?php } ?>