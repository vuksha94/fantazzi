<?php

session_start();

if(!isset($_SESSION['id_user'])){ 
    header("Location: index.php");
    exit();   
}

if($_SESSION['picked_team'] == 0){ 
    header("Location: makeyoursquad.php");
    exit();
}

function moveElement(&$array, $a, $b) {
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
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
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: index.php");
    exit();
}

$query = "SELECT * FROM users_teams WHERE id_user='".$user_id."' AND gw='".$id_gw."'";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$players = array();//15 players
for($i = 4; $i <= 18; $i++){
    array_push($players, $array[$i]);
}
$subs = array();//3 players
for($i = 19; $i <= 21; $i++){
    array_push($subs, $array[$i]);
}


$captain = $array['captain'];
$vice_captain = $array['vice_captain'];
$numOfTransfers = (int)$array['transfers'];
$transferCost = (int)$array['transfer_cost'];

if(isset($_POST['transferInfo'])){//transfers submitted
    $playersNew = array();//new submitted 15 players

     //gkps
    for($i = 1; $i <= 2; $i++){
        $pos = "gkp".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    }
    //defs
    for($i = 1; $i <= 5; $i++){
        $pos = "def".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    }  
    //mids
    for($i = 1; $i <= 5; $i++){
        $pos = "mid".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    } 
    //fwds
    for($i = 1; $i <= 3; $i++){
        $pos = "fwd".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    } 
    
    
    //echo $_POST['transferInfo']."<br>";
    $transferInfo = explode("|", $_POST['transferInfo']);
    $numOfTransfersNew = (int)$transferInfo[0];
    $bankNew = (double)$transferInfo[1];
    $transferCostNew = (int)(substr($transferInfo[2], 0, -4));//-4 for cutting " pts"
    
    
    $transfersMade = explode("*", $transferInfo[3]);//$transfersMade = {33->31, 135->142,...} $transferInfo[3] =  {33->31*135->142}
    $transfersArray = array();// $transfersArray = {33, 31, 135, 142} *******with this array you can save transfers IN and OUT in DB **************
    for($i = 0, $j = 0; $i < $numOfTransfersNew; ){
        array_push($transfersArray, explode("->", $transfersMade[$i])[$j]);
        if($j == 1){
            $i++;
            $j = 0;
        }
        else{
            $j++;
        }
    }
    //update subs,captain and vice captain if necessery   
    for($i = 0; $i < count($subs); $i++){
        $indexSub = array_search($subs[$i], $transfersArray);
        if($indexSub !== false){
            $transferPlayerInSub = $transfersArray[$indexSub - 1];
            $subs[$i] = $transferPlayerInSub;//change transferd out player with transfered in player
            //move $transferPlayerInSub player at the end of his position array in array $playersNew
            $indexInPlayers = array_search($transferPlayerInSub, $playersNew);
            if($indexInPlayers >= 0 && $indexInPlayers <= 1){//gkps
                moveElement($playersNew, $indexInPlayers, 1);
            }
            elseif($indexInPlayers >= 2 && $indexInPlayers <= 6){//defs
                moveElement($playersNew, $indexInPlayers, 6);
            }
            elseif($indexInPlayers >= 7 && $indexInPlayers <= 11){//mids
                moveElement($playersNew, $indexInPlayers, 11);
            }
            elseif($indexInPlayers >= 12 && $indexInPlayers <= 14){//fwds
                moveElement($playersNew, $indexInPlayers, 14);
            }
        }
    }
    $indexCaptain = array_search($captain, $transfersArray);
    if($indexCaptain !== false){
        $captain = $transfersArray[$indexCaptain - 1];
    }
    $indexViceCaptain = array_search($vice_captain, $transfersArray);
    if($indexViceCaptain !== false){
        $vice_captain = $transfersArray[$indexViceCaptain - 1];
    }
      
    $query = "SELECT free_transfers FROM login WHERE id_user='".$user_id."'";
    $result = $connection->getResult($query);
    
    $freeTransfers = (int)mysqli_fetch_array($result)['free_transfers'];
    if($freeTransfers != 10){//if not unlimited
        $freeTransfersNew = $freeTransfers - $numOfTransfersNew;
        if($freeTransfersNew < 0){
            $freeTransfersNew = 0;
        }    
    }
    $query = "UPDATE login SET bank='".$bankNew."'";
    
    if($freeTransfers != 10){//if not unlimited
        if($numOfTransfersNew + $numOfTransfers > 15){//Allowed up to 15 transfers per GW
            header("Location: transfers.php?maxTransfersPerWeekFail");
            exit();
        }
        $query .= ", free_transfers='".$freeTransfersNew."'";
    }
    $query .= " WHERE id_user='".$user_id."'";
    //echo $query."<br>";
    $result = $connection->getResult($query);
    
    $query = "UPDATE users_teams SET ";
    for($i = 1; $i <= 2; $i++){
        $column = "gk" . $i;
        $query .= $column."='".$playersNew[$i - 1]."',";
    }
    for($i = 1; $i <= 5; $i++){
        $column = "def" . $i;
        $query .= $column."='".$playersNew[$i + 1]."',";
    }
    for($i = 1; $i <= 5; $i++){
        $column = "mid" . $i;
        $query .= $column."='".$playersNew[$i + 6]."',";
    }
    for($i = 1; $i <= 3; $i++){
        $column = "fwd" . $i;
        $query .= $column."='".$playersNew[$i + 11]."',";
    }
    for($i = 1; $i <= 3; $i++){
        $column = "sub_" . $i;
        $query .= $column."='".$subs[$i - 1]."',";
    }
    
    if($freeTransfers == 10){//if unlimited
        $transferCostNew = $numOfTransfersNew = 0;
    }
    
    $query .= "captain='".$captain."',vice_captain='".$vice_captain."',transfers='".($numOfTransfers + $numOfTransfersNew).
            "',transfer_cost='".($transferCost + $transferCostNew)."' WHERE id_user='".$user_id."' AND gw='".$id_gw."'";  
    //echo $query ."<br>";
    $result = $connection->getResult($query);
    
    //insert transfers into users_transfers DB if not unlimited(first gameweek)
    if($freeTransfers != 10){
        $query = "INSERT INTO users_transfers (id_user, gw, transfer_in, transfer_out) VALUES";
        for($i = 0; $i < count($transfersArray); $i = $i + 2){
            $query .= " ('".$user_id."', '".
                        $id_gw."', '".$transfersArray[$i]."', '".$transfersArray[$i + 1]."')";
            if($i !== count($transfersArray) - 2){
                $query .= ",";
            }           
        }       
        $result = $connection->getResult($query);
    }
    
    header("Location: myteam.php");
    exit();

}
else{
    $query = "SELECT bank, free_transfers FROM login WHERE id_user='".$user_id."'";
    $result = $connection->getResult($query);

    $row = mysqli_fetch_array($result);
    $bank = $row['bank'];
    $free_transfers = $row['free_transfers'];

    $imgValArray = array();
    $query = "SELECT * FROM players,positions WHERE players.position=positions.position_shortname AND (id_player='".$players[0]."'";

    for($i =1; $i < count($players); $i++){
        $query .= " OR id_player='".$players[$i]."'";
        if($i == count($players) - 1){
            $query .=" OR id_player='".$players[$i]."')";
        }
    }
    $result = $connection->getResult($query);

    while($row = mysqli_fetch_array($result)){
        $imgValue = $row['id_player']."|".$row['club']."|".$row['name']."|".$row['last_name'].
                        "|".$row['position']."|".$row['price'];
        array_push($imgValArray, $row['id_player']);
        array_push($imgValArray, $imgValue);   
    }
    /*
        //test
        echo "Igraci: ";
        for ($i = 0; $i < count($players); $i++){
            echo $players[$i]." ";
        }
        echo " Subs: ";
        for ($i = 0; $i < count($subs); $i++){
            echo $subs[$i]." ";
        }
        echo " C: ".$captain . " V: " . $vice_captain. " TR:" . $numOfTransfers . " TC:".$transferCost;

        echo "<br>";
*/
    $clubs = array();//clubs = {"ARS", 2, "CHE", 3, ...}
    for($i = 0; $i < count($players); $i++){
        $indexImgInfo = $imgValArray[array_search($players[$i], $imgValArray) + 1];

        $playerClub = explode("|", $indexImgInfo)[1];
        //echo $playerClub." ";
        $index = array_search($playerClub, $clubs);//index of player`s club in clubs array

        if($index === false){//club is not in the list
            array_push($clubs, $playerClub);
            array_push($clubs, 1);
        }
        else{
            $clubs[array_search($playerClub, $clubs) + 1]++;
        }
    }

    if(isset($_GET['maxTransfersPerWeekFail'])){
        $error = "Maximum transfers per GW is 15!";
        echo "<span style='color:white; background-color:red'>". $error . "</span>";
    }

    $gkps = array(1, 2);
    $defs = array(1, 2, 3, 4, 5);
    $mids = array(1, 2, 3, 4, 5);
    $fwds = array(1, 2, 3);


    $js_clubs = json_encode($clubs);
    $js_players = json_encode($players);
    //$js_playersInfo = json_encode($playersInfo);
    $js_gkps = json_encode($gkps);
    $js_defs = json_encode($defs);
    $js_mids = json_encode($mids);
    $js_fwds = json_encode($fwds);

    $js_imgValArray = json_encode($imgValArray);

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script>
            var cnt = 15;//number of picked players
            var gkps = <?php echo $js_gkps; ?>;//array of indexes of gkps picked ex. {1} - one gkp picked
            var defs = <?php echo $js_defs; ?>;//array of indexes of defs picked ex. {1,3,4,5} - 4 defs picked, 2nd removed
            var mids = <?php echo $js_mids; ?>;//
            var fwds = <?php echo $js_fwds; ?>;//
            var players = <?php echo $js_players; ?>;
            var playersNew = <?php echo $js_players; ?>;
            var imageValArray = <?php echo $js_imgValArray; ?>;
            var transfersIn = [];
            var transfersOut = [];
            
            
            var clubs = <?php echo $js_clubs; ?>;
            var bank = <?php  echo $bank; ?>;
            var freeTransfers = <?php echo $free_transfers; ?>;
            
            var confirmWindow = null;
            
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
                //update bank
                var bankElement = document.getElementById("bank");
                bankElement.value = bank;
                if(bank < 0){
                    bankElement.style.backgroundColor = "red";
                }
                else{
                    bankElement.style.backgroundColor = "white";
                }
                
                bankElement.value = bank;
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
                
                    
                
                if(players.indexOf(idPlayer) !== -1){//if removing a player that was in team
                    transfersOut.push(id);
                    transfersOut.push(idPlayer);
                    //decrease text field FT if not 0 and if not unlimited
                    if(freeTransfers !== 10){
                        var ft = document.getElementById("freeTransfers").value - "0";
                        if(ft !== 0){
                            document.getElementById("freeTransfers").value = (ft - 1).toString();
                        }
                        if(transfersOut.length / 2 > freeTransfers){
                            var tc = (freeTransfers - transfersOut.length / 2)*4;
                            var tcInput = document.getElementById("transferCost");
                            tcInput.value = tc.toString() + " pts";
                            tcInput.style.backgroundColor = "red";                        
                        }
                    }
                    
                }
                else{
                    transfersIn.splice(transfersIn.indexOf(idPlayer) - 1, 2);
                }
                playersNew[playersNew.indexOf(idPlayer)] = "-1";
                //players.splice(players.indexOf(idPlayer), 1);//delete player from array of id_player
                cnt--;
                
                document.getElementById("confirmButton").disabled = true;
                
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
                var indexOfPlayersNew = -1;
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
                                    indexOfPlayersNew = i - 1;
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
                                    indexOfPlayersNew = 2 + (i - 1);
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
                                    indexOfPlayersNew = 7 + (i - 1);
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
                            for(i = 1; i <= 3; i++){
                                if(fwds.indexOf(i) === -1){
                                    id = "fwd_" + i;
                                    idHidd = "fwd" + i + "_id";
                                    fwds.push(i); 
                                    indexOfPlayersNew = 12 + (i - 1);
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
                //update bank
                var bankElement = document.getElementById("bank");
                bankElement.value = bank;
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
                
                if(players.indexOf(idPlayer) === -1){
                    transfersIn.push(id);
                    transfersIn.push(idPlayer);
                }
                else{
                    transfersOut.splice(transfersOut.indexOf(idPlayer) - 1, 2);
                    if(freeTransfers !== 10){
                        if(freeTransfers >= transfersOut.length / 2){
                            document.getElementById("freeTransfers").value = (freeTransfers - transfersOut.length / 2).toString();   
                        }
                        var tcInput = document.getElementById("transferCost");
                        var tc = 0;
                        if(transfersOut.length / 2 > freeTransfers){
                            tc = (freeTransfers - transfersOut.length / 2)*4;                            
                            tcInput.value = tc.toString() + " pts";
                            tcInput.style.backgroundColor = "red";                        
                        }
                        else{
                            tcInput.value = tc.toString() + " pts";
                            tcInput.style.backgroundColor = "white";
                        }
                    }                                                       
                }
                playersNew[indexOfPlayersNew] = idPlayer;

                cnt++;   
                checkForChanges();
                return false;
            }
            function checkForChanges(){//enable or disable CONFIRM BUTTON
                var makeTransfer = document.getElementById("confirmButton");
                if(cnt < 15){
                    makeTransfer.disabled = true;
                }
                else if(bank < 0){
                    makeTransfer.disabled = true;
                }
                else{
                    var i;
                    var transferMade = false;
                    for(i = 0; i < playersNew.length; i++){//check for transfers
                        if(players.indexOf(playersNew[i]) === -1){
                            transferMade = true;
                            break;
                        }
                    }
                    if(transferMade){
                        makeTransfer.disabled = false;
                    }
                    else{
                        makeTransfer.disabled = true;
                    }
                }
            }
            function confirm(){
                if(bank < 0){
                    alert("You dont have enough money!");
                    return false;
                }
                confirmWindow = window.open("", "Confirm", "left=450, top=200, width=300, height=200");
                //confirmWindow = PopupCenter("","Confirm transfers","");
                confirmWindow.document.write("<html>");
                confirmWindow.document.write("<title>CONFIRM TRANSFERS</title>");
                confirmWindow.document.write("<body>");
                confirmWindow.document.write("<center>");
                confirmWindow.document.write("<table>");
                confirmWindow.document.write("<th>Transfers IN</th><th>Transfers OUT</th><th>Transfer Cost</th>");
                var i, j;
                var transfersWritten = [];
                var transfersMade = [];//{33->31,...}
                for(i = 0; i < transfersIn.length; i = i + 2){
                    //get last name of player IN from input field from form
                    var playerIn = document.getElementById(transfersIn[i]).value;
                    var lastNameIn = playerIn.substr(0, playerIn.indexOf("("));
                    var lastNameOut;
                    for(j = 0; j < transfersOut.length; j = j + 2){
                        if(transfersOut[j].substring(0, 3) === transfersIn[i].substring(0, 3)){//comparing positions
                            if(transfersWritten.indexOf(transfersOut[j + 1]) === -1){
                                lastNameOut = imageValArray[imageValArray.indexOf(transfersOut[j + 1]) + 1].split("|")[3];
                                transfersWritten.push(transfersOut[j + 1]);
                                transfersMade.push(transfersIn[i + 1] + "->" + transfersOut[j + 1]);
                                break;
                            }                               
                        }                        
                    }
                    confirmWindow.document.write("<tr><td>" + lastNameIn + "</td><td>" + lastNameOut + "</td><td></td></tr>");   
                }
                             
                var tc = document.getElementById("transferCost").value;
                if(tc !== "0 pts"){
                    confirmWindow.document.write("<tr><td></td><td></td><td><span style='background-color:red'>" + tc + "</span></td></tr>");
                }
                else{
                    confirmWindow.document.write("<tr><td></td><td></td><td>" + tc + "</td></tr>");
                }
                //update transfersInfo
                var transferInfo = [];
                transferInfo.push(transfersIn.length / 2);//number of transfers
                transferInfo.push(bank);//bank
                transferInfo.push(tc);//transfer cost
                transferInfo.push(transfersMade.join("*"));//transfersMade {33->31*135->142)}
                var inf = document.getElementById("transferInfo");
                inf.value = transferInfo.join("|");
                               
                confirmWindow.document.write("<tr><td><button type='button' onclick='window.opener.submitForm()'>MAKE TRANSFERS</button></td><td></td>");
                confirmWindow.document.write("<td><button type='button' onclick='window.opener.cancelTransfer()'>CANCEL TRANSFERS</button></td></tr>");
                confirmWindow.document.write("</table>");
                confirmWindow.document.write("</center>");
                confirmWindow.document.write("</body></html>");
                
                document.getElementById("overlay").style.display = "block";
                
            }
            function cancelTransfer(){
                confirmWindow.close();
            }
            function submitForm(){
                confirmWindow.close();
                document.getElementById("transfersform").submit();
            }
            function onLoadFunction(){
                loadDoc('displayPlayers.php', showPlayers, '1', '30');
                LoadDocFx('displayFixtures.php', showFixtures, '');
            }
            function loadDoc(url, cFunction, page, limit)//display players
            {
                var club = document.searchPlayersForm.clubs.value;
                var position = document.searchPlayersForm.position.value;
                var sort = document.searchPlayersForm.sort.value;
                var playerName = document.searchPlayersForm.playername.value;
                var arrayOfPlayers = playersNew.join("|");//make a delimiter array of id_players for ex. 1|13|55|
                
                                           
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
            function parent_disable() {
                if(confirmWindow && !confirmWindow.closed){
                    confirmWindow.focus();
                }
                else{
                    document.getElementById("overlay").style.display = "none";
                }
            }
        
        </script>
        <style>
            
        </style>
    </head>
    <body onload="onLoadFunction()" onFocus="parent_disable()" onclick="parent_disable()">
        <div>
            <div id="overlay"></div>
            <div class="navbar"> <?php include 'navbar.php'; ?> </div>
            <div>
                <h3>TRANSFERS</h3>
                <form name="players">
                    <center>
                        <div class="transfersinfo">
                            <div class="div--inlineleft">
                                <span title="Bank">Bank: </span> <input type="text" name="bank" id="bank" value="<?php echo $bank; ?>" size="3" readonly="readonly"> 
                            </div>
                            <div class="div--inlineleft">
                                <div class="tooltip div--inlineleft">                               
                                    <span class="tooltiptext">Free Transfer</span><span>FT: </span>
                                </div>
                                <div class="div--inlineleft">
                                    <input type="text" name="freeTransfers" id="freeTransfers" value="<?php if($free_transfers != 10) echo $free_transfers; else echo "UNLIMITED"; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="div--inlineleft">
                                <div class="tooltip div--inlineleft">
                                    <span class="tooltiptext">Transfer Cost</span><span>TC: </span>
                                </div>
                                <div class="div--inlineleft">
                                    <input type="text" id="transferCost" value="<?php echo "0 pts"; ?>" size="3" readonly="readonly">
                                </div>
                            </div>
                        </div>
                        
                        <div class='startingeleven'>    
                        <table>
                        <tr><td></td><td><input type="text" id="gkp_1" value="<?php $indexImgInfo = $imgValArray[array_search($players[0], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[0]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[0], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>

                            <td></td><td><input type="text" id="gkp_2" value="<?php $indexImgInfo = $imgValArray[array_search($players[1], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[1]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[1], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                        <td></td></tr>
                        <tr><td><input type="text" id="def_1" value="<?php $indexImgInfo = $imgValArray[array_search($players[2], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[2]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[2], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="def_2" value="<?php $indexImgInfo = $imgValArray[array_search($players[3], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[3]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[3], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="def_3" value="<?php $indexImgInfo = $imgValArray[array_search($players[4], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[4]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[4], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="def_4" value="<?php $indexImgInfo = $imgValArray[array_search($players[5], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[5]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[5], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="def_5" value="<?php $indexImgInfo = $imgValArray[array_search($players[6], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[6]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[6], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                        </tr>
                        <tr><td><input type="text" id="mid_1" value="<?php $indexImgInfo = $imgValArray[array_search($players[7], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[7]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[7], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="mid_2" value="<?php $indexImgInfo = $imgValArray[array_search($players[8], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[8]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[8], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="mid_3" value="<?php $indexImgInfo = $imgValArray[array_search($players[9], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[9]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[9], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="mid_4" value="<?php $indexImgInfo = $imgValArray[array_search($players[10], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[10]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[10], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="mid_5" value="<?php $indexImgInfo = $imgValArray[array_search($players[11], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[11]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[11], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                        </tr>   
                        <tr><td></td>
                            <td><input type="text" id="fwd_1" value="<?php $indexImgInfo = $imgValArray[array_search($players[12], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[12]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[12], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="fwd_2" value="<?php $indexImgInfo = $imgValArray[array_search($players[13], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[13]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[13], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                            <td><input type="text" id="fwd_3" value="<?php $indexImgInfo = $imgValArray[array_search($players[14], $imgValArray) + 1]; $info = explode("|", $indexImgInfo); echo $info[3]."(".$info[1]."),".$info[5]; ?>" size="16" readonly="readonly">
                                <img id="img_<?php echo $players[14]; ?>" src="images/download.png" height="20" width="20" onclick="removePlayer('<?php $indexImg = array_search($players[14], $imgValArray); echo $imgValArray[$indexImg + 1]; ?>')"></td>
                        <td></td></tr>
                        </table>                           
                        </div>
                        <div style="padding-top: 10px;">
                            <input type="button" class="btn confirmation--button butt--full" id="confirmButton" name="confirmTransfers" value="CONFIRM TRANSFERS" onclick="confirm()" disabled="disabled">
                        </div>
                        </form>
                        <form id="transfersform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="gkp1_id" id="gkp1_id" value="<?php echo $players[0]; ?>" size="16">
                            <input type="hidden" name="gkp2_id" id="gkp2_id" value="<?php echo $players[1]; ?>" size="16">
                        
                            <input type="hidden" name="def1_id" id="def1_id" value="<?php echo $players[2]; ?>" size="16">
                            <input type="hidden" name="def2_id" id="def2_id" value="<?php echo $players[3]; ?>" size="16">
                            <input type="hidden" name="def3_id" id="def3_id" value="<?php echo $players[4]; ?>" size="16">
                            <input type="hidden" name="def4_id" id="def4_id" value="<?php echo $players[5]; ?>" size="16">
                            <input type="hidden" name="def5_id" id="def5_id" value="<?php echo $players[6]; ?>" size="16">
                        
                            <input type="hidden" name="mid1_id" id="mid1_id" value="<?php echo $players[7]; ?>" size="16">
                            <input type="hidden" name="mid2_id" id="mid2_id" value="<?php echo $players[8]; ?>" size="16">
                            <input type="hidden" name="mid3_id" id="mid3_id" value="<?php echo $players[9]; ?>" size="16">
                            <input type="hidden" name="mid4_id" id="mid4_id" value="<?php echo $players[10]; ?>" size="16">
                            <input type="hidden" name="mid5_id" id="mid5_id" value="<?php echo $players[11]; ?>" size="16">
                          
                            <input type="hidden" name="fwd1_id" id="fwd1_id" value="<?php echo $players[12]; ?>" size="16">
                            <input type="hidden" name="fwd2_id" id="fwd2_id" value="<?php echo $players[13]; ?>" size="16">
                            <input type="hidden" name="fwd3_id" id="fwd3_id" value="<?php echo $players[14]; ?>" size="16">

                            <input type="hidden" name="transferInfo" id="transferInfo" value="" size="16">                   
                        </form> 
                    </center>                    
                                
            </div>
            <form name="searchPlayersForm">
                <select name="clubs" onchange="loadDoc('displayPlayers.php', showPlayers, '1', '30');">
                    <option value="" selected>Select a club</option>
                    <?php
                        $query = "SELECT * FROM clubs";
                        $result = $connection->getResult($query);
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='".$row['club_shortname']."'>".$row['club_name']."</option>";
                        }                    
                    ?>
                </select>
                <select name="position" onchange="loadDoc('displayPlayers.php', showPlayers, '1', '30');">
                    <option value="" selected>Select a position</option>
                    <?php
                        $query = "SELECT * FROM positions";
                        $result = $connection->getResult($query);
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='".$row['position_shortname']."'>".$row['position_name']."</option>";
                        }                    
                    ?>
                </select>
                <select name="sort" onchange="loadDoc('displayPlayers.php', showPlayers, '1', '30');">
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