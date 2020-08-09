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

$user_id = $_SESSION['id_user'];

$query = "SELECT * FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);
$activeGw = $array['active_gw'];
$id_gw = $array['active_gw'];//active_gw -> {1,2,...38,39(POSTSEASON)}
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    $connection->close();
    header("Location: index.php?gameUpdating");
    exit();
}

$query = "SELECT * FROM users_teams,formations WHERE id_user='".$user_id."' AND gw='".$id_gw."' AND formation=id_formation";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);
//make an array of id_players by fetching $array
$players = array();
for($i = 4; $i <= 18; $i++){
    array_push($players, $array[$i]);
}
$playersInfo = array();
$query = "SELECT id_player, club, last_name, position FROM players WHERE id_player='".$players[0]."'";
for($i =1; $i < count($players); $i++){
    $query .= " OR id_player='".$players[$i]."'";
}
$result = $connection->getResult($query);

while($row = mysqli_fetch_array($result)){
    for($i = 0; $i < 4; $i++){//*** 7 is number of columns in table players ***not dinamically -> BAD***
        array_push($playersInfo, $row[$i]);
    }
}

if(isset($_POST['submitBtn'])){
    //read subs
    $subsNew = array();
    for($i = 1; $i <= 3; $i++){
        $pos = "sub".$i."_id";
        array_push($subsNew, $_POST[$pos]);
    } 
    
    $playersNew = array();
    
    //get id_formation of new formation
    $query = "SELECT id_formation FROM formations WHERE description='".$_POST['formation']."'";
    $result = $connection->getResult($query);
    
    //add formation, 15 players, 3 subs, captain and vice captain to players new array and update the database
    $row = mysqli_fetch_array($result);    
    array_push($playersNew, $row['id_formation']);
    
    $formationNew = explode("-", $_POST['formation']);
      
    //gkps
    for($i = 1; $i <= 2; $i++){
        $pos = "gkp".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    }
    //def-starters
    for($i = 1; $i <= $formationNew[0]; $i++){
        $pos = "def".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    }  
    //def-subs
    for($j = 0 ; $j <= 2; $j++){
        $index = array_search($subsNew[$j], $playersInfo);
        if($playersInfo[$index + 3] == "DEF"){
            array_push($playersNew, $subsNew[$j]);        
        }
    }
    //mid-starters
    for($i = 1; $i <= $formationNew[1]; $i++){
        $pos = "mid".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    } 
    //mid-subs
    for($j = 0; $j <= 2; $j++){
        $index = array_search($subsNew[$j], $playersInfo);
        if($playersInfo[$index + 3] == "MID"){
            array_push($playersNew, $subsNew[$j]);        
        }
    }
    //fwd-starters
    for($i = 1; $i <= $formationNew[2]; $i++){
        $pos = "fwd".$i."_id";
        array_push($playersNew, $_POST[$pos]);
    } 
    //fwd-subs
    for($j = 0; $j <= 2; $j++){
        $index = array_search($subsNew[$j], $playersInfo);
        if($playersInfo[$index + 3] == "FWD"){
            array_push($playersNew, $subsNew[$j]);        
        }
    }
    //subs
    for($i = 0; $i <= 2; $i++){
        array_push($playersNew, $subsNew[$i]);
    }   
    //captain and vice-captain
    array_push($playersNew, $_POST['captain']);
    array_push($playersNew, $_POST['vice_captain']);
    
    //update database
    $query = "UPDATE users_teams SET formation='".$playersNew[0]."', gk1='".$playersNew[1]."', gk2='".$playersNew[2]."', ";
    for($i = 3, $j = 1; $i <= 7; $i++, $j++){
        $column = "def".$j;
        $query .= $column."='".$playersNew[$i]."', ";
    }
    for($i = 8, $j = 1; $i <= 12; $i++, $j++){
        $column = "mid".$j;
        $query .= $column."='".$playersNew[$i]."', ";
    }    
    for($i = 13, $j = 1; $i <= 15; $i++, $j++){
        $column = "fwd".$j;
        $query .= $column."='".$playersNew[$i]."', ";
    }  
    for($i = 16, $j = 1; $i <= 18; $i++, $j++){
        $column = "sub_".$j;
        $query .= $column."='".$playersNew[$i]."', ";
    } 
    for ($i = 0; $i < count($playersNew); $i++){
        echo " ".$playersNew[$i];
    }
    echo "<br>";
    $query .= "captain='".$playersNew[19]."', vice_captain='".$playersNew[20]."' WHERE id_user='".$user_id."' AND gw='".$id_gw."'";
    $result = $connection->getResult($query);
    
    header("Location: myteam.php?saved");
    exit();
}
else{
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
    //test
    /*
    echo "Igraci: ";
    for ($i = 0; $i < count($players); $i++){
        echo $players[$i]." ";
    }*/

    $captain = $array[22]; $vice_captain = $array[23];

    

    $js_players = json_encode($players);
    $js_subs = json_encode($subs);
    $js_startingXI = json_encode($startingXI);
    $js_playersInfo = json_encode($playersInfo);
    $js_formation = json_encode($formation);

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script>
                var players = <?php echo $js_players; ?>;
                var startingXI = <?php echo $js_startingXI; ?>;
                var startingXINew = <?php echo $js_startingXI; ?>;
                var subs = <?php echo $js_subs; ?>;
                var subsNew = <?php echo $js_subs; ?>;
                var playersInfo = <?php echo $js_playersInfo; ?>;
                var formation = <?php echo $js_formation; ?>;
                var formationNew = <?php echo $js_formation; ?>;
                var captain = <?php echo $captain; ?>;
                captain = captain.toString();
                var captainNew = captain;
                var viceCaptain = <?php echo $vice_captain; ?>;
                viceCaptain = viceCaptain.toString();
                var viceCaptainNew = viceCaptain;
                
                
                
            function sub(playerId){
                var defNumber = formationNew[0] - "0";
                var midNumber = formationNew[1] - "0";
                var fwdNumber = formationNew[2] - "0";
                var buttons = document.getElementsByTagName("button");
                var i; 
                var subPlayer = "0";//player to be subbed with playerID, 0 indicates that SUB is pressed for the first time and no player is subbed
                for(i = 0; i < 17; i++){//17 instead 15 because there are 2 more buttons for cap and vicecap
                    if (buttons[i].innerHTML === "CANCEL"){
                        if(buttons[i].value !== playerId){
                            subPlayer = buttons[i].value;  
                            break;
                        }
                        else{
                            changeButtonToSubAndUndimmAll(playerId);
                            return false;
                        }
                    }
                }
                var buttonId = "butt_" + playerId;//button of player which SUB button is pressed
                var button = document.getElementById(buttonId);
                
                var index = playersInfo.indexOf(playerId)
                var position = playersInfo[index + 3];//find position of playerId {"GKP", "DEF", ...}
                var starter = isStarter(playerId);     //flag that indicates if player is in starting XI(true) or on the bench(false)
               
                if(subPlayer === "0"){
                    //dimm buttons if substitution is imposible
                    if(starter){//if player is in starting XI dimm all other players in starting XI that are on different position
                        dimmStartersOnDiffPos(position);        
                    } 
                    switch(position){
                        case "GKP":     //can sub only with gkp_sub
                            var i, id;                              
                            for(i = 1; i < subsNew.length; i++){//dimm subs except gkp_sub
                                id = "butt_" + subsNew[i];
                                document.getElementById(id).disabled = true;                                
                            }                           
                            if(!starter){//dimm all players except gkp_1
                                for(i = 1; i < startingXI.length; i++){
                                    id = "butt_" + startingXI[i];
                                    document.getElementById(id).disabled = true;                                
                                }                                
                            }
                            break;
                        case "DEF": //can sub with other defenders in starting XI, all defenders on bench,mids or fwds on the bench if formation allows 
                            if(starter){//if defender is starter
                                if(defNumber === 3){//defNumber -> number of players in defence in current formation                                
                                    if(midNumber === 5){// formation: 3-5-2, on bench: "DEF","DEF","FWD"
                                        dimmSubOnPos("FWD");//function that dimms buttons of substitutions on position "FWD"
                                    }
                                    else{//midNumber = 4 -> formation: 3-4-3 bench: "DEF","DEF","MID"
                                        dimmSubOnPos("MID");
                                    }     
                                }
                            } 
                            else{//if def is benched
                                dimmStartersOnPos("GKP");
                                if(fwdNumber === 1){//formation: 4-5-1 bench: "DEF","FWD","FWD"
                                    dimmStartersOnPos("FWD");
                                }
                            }
                            break;
                        case "MID": 
                            if(!starter){//if mid is benched
                                dimmStartersOnPos("GKP");
                                if(defNumber === 3){//formation: 3-4-3 bench: "DEF","MID","MID"
                                    dimmStartersOnPos("DEF");
                                }
                                if(fwdNumber === 1){//formation: 5-4-1 bench: "FWD","FWD","MID"
                                    dimmStartersOnPos("FWD");
                                }
                            }                            
                            break;
                        case "FWD":
                            if(starter){
                                if(fwdNumber === 1){//can sub only with other forwards
                                    if(midNumber === 5){// formation: 4-5-1 bench: "DEF","FWD","FWD"
                                        dimmSubOnPos("DEF");
                                    }
                                    else{//midNumber = 4 -> formation: 5-4-1 bench: "FWD","FWD","MID"
                                        dimmSubOnPos("MID");
                                    }                                
                                } 
                            }
                            else{//if fwd is benched
                                dimmStartersOnPos("GKP");
                                if(defNumber === 3){//formation: 3-5-2 bench: "DEF","DEF","FWD"
                                    dimmStartersOnPos("DEF");
                                }                                
                            }
                            break;
                    }
                    
                    button.innerHTML = "CANCEL"; 
                }    
                else{//make substitution
                    var positionSubPlayer = playersInfo[playersInfo.indexOf(subPlayer) + 3];//position of player to be substituted
                    if(position === positionSubPlayer){
                        subPlayersOnSamePosition(playerId, subPlayer);
                    }
                    else{
                        subPlayersOnDiffPositions(playerId, position, subPlayer, positionSubPlayer);
                    }
                }
            }
            function subPlayersOnDiffPositions(player1Id, posistion1, player2Id, posistion2){
                var defNumber = formationNew[0] - "0";
                var midNumber = formationNew[1] - "0";
                var fwdNumber = formationNew[2] - "0";
                var bothSubs = false;//flag that indicates if both players are on bench or only one
                var subId = "";//id of player that is benched
                var subPosition = "";
                var starterId = "";//id of starting player
                var starterPosition = "";
                
                if(!isStarter(player1Id) && !isStarter(player2Id)){
                    bothSubs = true;
                }
                else if(!isStarter(player1Id)){
                    subId = player1Id;
                    subPosition = posistion1;
                    starterId = player2Id;
                    starterPosition = posistion2;
                }
                else{
                    subId = player2Id;
                    subPosition = posistion2;
                    starterId = player1Id;
                    starterPosition = posistion1;
                }
                
                if(bothSubs){
                    subPlayersOnSamePosition(player1Id, player2Id);//works just fine!
                    return;
                }
                else{//sub players: one is starter, other is benched
                    //change hidden inputs
                    //get number of starters name position ex. <input type='hidden' name='defn_id' ...> gets n, delete hidden input and decrease position
                    // number of remaining formation[0] - x hidden inputs, then change id and value of hidden inputs in subs and append childnode to position of subId
                    var hiddenStarter = document.getElementById("hidd_" + starterId);
                    var hiddenStarterName = hiddenStarter.name;
                    var nStarter = hiddenStarterName.substring(3, 4) - "0";//get n
                    var i, numOfPlayersOnStarterPos;
                    switch(starterPosition){
                        case "DEF":
                            numOfPlayersOnStarterPos = defNumber;
                            break;
                        case "MID":
                            numOfPlayersOnStarterPos = midNumber;
                            break;
                        case "FWD":
                            numOfPlayersOnStarterPos = fwdNumber;
                            break;
                    }
                    for(i = nStarter; i < numOfPlayersOnStarterPos; i++){
                        hiddenStarter.nextSibling.name = starterPosition.toLowerCase() + i.toString() + "_id";
                        hiddenStarter = hiddenStarter.nextSibling;
                    }
                    hiddenStarter = document.getElementById("hidd_" + starterId);//re-read hiddenStarter and delete it
                    hiddenStarter.parentNode.removeChild(hiddenStarter);//delete hidden input  
                                   
                    //change id and value of hidden inputs in subs
                    var hiddenSub = document.getElementById("hidd_" + subId);
                    var hiddenSubName = hiddenSub.name;
                    hiddenSub.id = "hidd_" + starterId;
                    hiddenSub.value = starterId;
                    //append childnode after last player in starting XI on position of subId
                    var numOfPlayersOnSubPos;
                    switch(subPosition){
                        case "DEF":
                            numOfPlayersOnSubPos = defNumber;
                            break;
                        case "MID":
                            numOfPlayersOnSubPos = midNumber;
                            break;
                        case "FWD":
                            numOfPlayersOnSubPos = fwdNumber;
                            break;
                    }     
                    var newNode = document.createElement("INPUT");
                    newNode.setAttribute("type", "hidden");
                    var newName = subPosition.toLowerCase() + (numOfPlayersOnSubPos + 1).toString() + "_id";
                    var newId = "hidd_" + subId;
                    newNode.setAttribute("name", newName);
                    newNode.setAttribute("id", newId);
                    newNode.setAttribute("value", subId);
                    var nameToInsertAfter = subPosition.toLowerCase() + numOfPlayersOnSubPos.toString() + "_id";
                    var lastPlayerInFormationOnSubPos = document.getElementsByName(nameToInsertAfter)[0];
                    lastPlayerInFormationOnSubPos.parentNode.insertBefore(newNode, lastPlayerInFormationOnSubPos.nextSibling);//append newNode after last Player in starting XI on subPos
                
                    //swap text, input and button elements of players to be sub on different position 
                    var starterFields = []; var subFields = new Array(); 
                    starterFields[0] = starterId; starterFields[1] = "butt_" + starterId;
                    subFields[0] = subId; subFields[1] = "butt_" + subId;

                    var sub1, sub2, parentDiv1, parentDiv2, indexChildNodePos1, indexChildNodePos2;
                    for(i = 0; i < starterFields.length + 1; i++){
                        
                        if(i === 0){//in first iteration i=0, span tags are changed{GKP: ,DEF: ,...}
                            sub1 = document.getElementById(starterFields[i]);// i = 0
                            sub2 = document.getElementById(subFields[i]);
                            parentDiv1 = sub1.parentNode;
                            parentDiv2 = sub2.parentNode;
                            indexChildNodePos1 = getChildNodeIndex(sub1) - 1;//get index of text element {GKP:, DEF:,...}
                            indexChildNodePos2 = getChildNodeIndex(sub2) - 1;
                            sub1 = parentDiv1.childNodes[indexChildNodePos1];
                            sub2 = parentDiv2.childNodes[indexChildNodePos2];
                            //swap visibility property, set starter to see position because it is going to bench, set sub position to hidden
                            sub1.style.visibility = "visible";
                            sub2.style.visibility = "hidden";
                        }
                        else{
                            sub1 = document.getElementById(starterFields[i - 1]);
                            sub2 = document.getElementById(subFields[i - 1]);
                            parentDiv1 = sub1.parentNode;
                            parentDiv2 = sub2.parentNode;                         
                        }                       
                        parentDiv2.insertBefore(sub1, sub2);//insert starter element before sub element in <div>: parentDiv2

                        //append childnode after last player in starting XI on position of subId
                        var divToAppend = document.getElementsByClassName(subPosition)[0];
                        divToAppend.appendChild(sub2);                       
                    }   
                    //change <b>C</b> or <b>V</b> if captain or vicecaptain is subbed
                    if(starterId == captainNew){
                        var captainText = document.getElementById("captain");
                        var buttonBeforeNewCaptain = document.getElementById("butt_" + subId);
                        buttonBeforeNewCaptain.parentNode.insertBefore(captainText, buttonBeforeNewCaptain.nextSibling);
                    }     
                    else if(starterId == viceCaptainNew){
                        var vCaptainText = document.getElementById("viceCaptain");
                        var buttonBeforeNewCaptain = document.getElementById("butt_" + subId);
                        buttonBeforeNewCaptain.parentNode.insertBefore(vCaptainText, buttonBeforeNewCaptain.nextSibling);
                    }   
                    
                    changeButtonToSubAndUndimmAll(player2Id);
                    
                    //update startingXINew, subsNew, captainNew, viceCaptainNew, formationNew
                    //startingXINew
                    startingXINew.splice(startingXINew.indexOf(starterId), 1);//remove player that is subbed from startingXI
                    var indexStartingXI;//index in startingXINew where to add subId
                    switch(subPosition){
                        case "DEF":
                            indexStartingXI = defNumber + 1;
                            break;
                        case "MID":
                            indexStartingXI = defNumber + midNumber + 1;
                            break;
                        case "FWD":
                            indexStartingXI = defNumber + midNumber + fwdNumber + 1;
                            break;
                    }   
                    
                    startingXINew.splice(indexStartingXI, 0, subId);
                    //subsNew
                    subsNew[subsNew.indexOf(subId)] = starterId;
                    //captainNew and viceCaptainNew
                    changeCaptainsListAfterSubOnDiffPos("cap", subId, subPosition, starterId);
                    changeCaptainsListAfterSubOnDiffPos("vcap", subId, subPosition, starterId);
                    // formationNew
                    switch(subPosition){
                        case "DEF":
                            formationNew[0] = (parseInt(formationNew[0]) + 1).toString();
                            break;
                        case "MID":
                            formationNew[1] = (parseInt(formationNew[1]) + 1).toString();
                            break;
                        case "FWD":
                            formationNew[2] = (parseInt(formationNew[2]) + 1).toString();
                            break;
                    }
                    switch(starterPosition){
                        case "DEF":
                            formationNew[0] = (parseInt(formationNew[0]) - 1).toString();
                            break;
                        case "MID":
                            formationNew[1] = (parseInt(formationNew[1]) - 1).toString();
                            break;
                        case "FWD":
                            formationNew[2] = (parseInt(formationNew[2]) - 1).toString();
                            break;
                    }
                    //enable or disable SAVE CHANGE BUTTON
                    checkForChanges();
                }
            }
            function subPlayersOnSamePosition(player1Id, player2Id){
                var id1_butt, id2_butt, id1_hidd, id2_hidd;
                id1_hidd = "hidd_" + player1Id;
                id2_hidd = "hidd_" + player2Id;                
                id1_butt = "butt_" + player1Id;
                id2_butt = "butt_" + player2Id;
               
                var player1Fields = []; var player2Fields = new Array(); 
                player1Fields[0] = player1Id; player1Fields[1] = id1_butt;
                player2Fields[0] = player2Id; player2Fields[1] = id2_butt;
                
                //change id and value between hidden inputs of players
                var hiddPlayer1 = document.getElementById(id1_hidd);
                var hiddPlayer2 = document.getElementById(id2_hidd);
                var tempHiddIdPlayer1 = hiddPlayer1.id;
                var tempHiddValuePlayer1 = hiddPlayer1.value;
                hiddPlayer1.id = hiddPlayer2.id;
                hiddPlayer1.value = hiddPlayer2.value;
                hiddPlayer2.id = tempHiddIdPlayer1;
                hiddPlayer2.value = tempHiddValuePlayer1;
                
                //swap text, input and button elements of players to be sub on same position                                
                var i;
                var positionTextSwapDone = false;   
                
                var sub1, sub2, parentDiv1, parentDiv2, indexChildNode, indexChildNodePos1, indexChildNodePos2;
                for(i = 0; i < player1Fields.length + 1; i++){
                    
                    if(!positionTextSwapDone){//in first iteration i=0, text fields are swapped{GKP: ,DEF: ,...} because this function is used also when two players on bench with different positions are subbed
                        sub1 = document.getElementById(player1Fields[i]);// i = 0
                        sub2 = document.getElementById(player2Fields[i]);
                        parentDiv1 = sub1.parentNode;
                        parentDiv2 = sub2.parentNode;
                        indexChildNodePos1 = getChildNodeIndex(sub1) - 1;//get index of text element {GKP:, DEF:,...}
                        indexChildNodePos2 = getChildNodeIndex(sub2) - 1;
                        sub1 = parentDiv1.childNodes[indexChildNodePos1];
                        sub2 = parentDiv2.childNodes[indexChildNodePos2];
                        positionTextSwapDone = true;
                        //swap visibility property for span fields if one player is starter and other is benched
                        if( isStarter(player1Id) && !isStarter(player2Id) ){                            
                            sub1.style.visibility = "visible";
                            sub2.style.visibility = "hidden";
                        }
                        else if( !isStarter(player1Id) && isStarter(player2Id) ){
                            sub2.style.visibility = "visible";
                            sub1.style.visibility = "hidden";
                        }
                    }
                    else{
                        sub1 = document.getElementById(player1Fields[i - 1]);
                        sub2 = document.getElementById(player2Fields[i - 1]);
                        parentDiv1 = sub1.parentNode;
                        parentDiv2 = sub2.parentNode;                         
                    }
                    indexChildNode = getChildNodeIndex(sub1);
                    //returns index of sub1 element in its <div> ( indexChildNode = getChildNodeIndex(sub2) )                          
                    parentDiv2.insertBefore(sub1, sub2);//insert sub1 element before sub2 element in <div>: parentDiv2
                    if(isStarter(player1Id) && isStarter(player2Id)){//if both are starters
                        if(startingXINew.indexOf(player1Id) > startingXINew.indexOf(player2Id)){//if second time sub button is pressed on player that is after player which is pressed first
                            indexChildNode++;
                        }                    
                    }
                    else if(!isStarter(player1Id) && !isStarter(player2Id)){//if both are benched
                        if(subsNew.indexOf(player1Id) > subsNew.indexOf(player2Id)){//if second time sub button is pressed on player that is after player which is pressed first
                            indexChildNode++;
                        }                         
                    } 
                    parentDiv1.insertBefore(sub2, parentDiv1.childNodes[indexChildNode]);    
                }
                if(isStarter(player1Id) && isStarter(player2Id)){//if both are starters
                    //change <b>C</b> or <b>V</b> if captain or vicecaptain is subbed
                    if(player1Id == captainNew || player2Id == captainNew){
                        var captainText = document.getElementById("captain");
                        var buttonBeforeNewCaptain = document.getElementById("butt_" + captainNew);
                        buttonBeforeNewCaptain.parentNode.insertBefore(captainText, buttonBeforeNewCaptain.nextSibling);
                    }     
                    else if(player1Id == viceCaptainNew || player2Id == viceCaptainNew){
                        var vCaptainText = document.getElementById("viceCaptain");
                        var buttonBeforeNewCaptain = document.getElementById("butt_" + viceCaptainNew);
                        buttonBeforeNewCaptain.parentNode.insertBefore(vCaptainText, buttonBeforeNewCaptain.nextSibling);
                    }                   
                }
                changeButtonToSubAndUndimmAll(player2Id);
                
                //update startingXINew, subsNew, captainNew, viceCaptainNew ... formation remains
                //startingXINew, subsNew, captainNew and viceCaptainNew
                if(isStarter(player1Id) && isStarter(player2Id)){//change captains order in select options when both players are in starting XI ** unnecesery, but nice **
                   swapElementsInArray(startingXINew, startingXINew.indexOf(player1Id), startingXINew.indexOf(player2Id));
                   changeCaptainsListAfterSubInXI("cap", player1Id, player2Id);
                   changeCaptainsListAfterSubInXI("vcap", player1Id, player2Id);
                }                
                else if(!isStarter(player1Id) && !isStarter(player2Id)){
                    swapElementsInArray(subsNew, subsNew.indexOf(player1Id), subsNew.indexOf(player2Id));
                }
                else if(!isStarter(player1Id) && isStarter(player2Id)){
                    startingXINew[startingXINew.indexOf(player2Id)] = player1Id;
                    subsNew[subsNew.indexOf(player1Id)] = player2Id;
                    changeCaptainsListAfterSubOnSamePos("cap", player1Id, player2Id);
                    changeCaptainsListAfterSubOnSamePos("vcap", player1Id, player2Id);
                }
                else if(isStarter(player1Id) && !isStarter(player2Id)){
                    startingXINew[startingXINew.indexOf(player1Id)] = player2Id;
                    subsNew[subsNew.indexOf(player2Id)] = player1Id;
                    changeCaptainsListAfterSubOnSamePos("cap", player2Id, player1Id);
                    changeCaptainsListAfterSubOnSamePos("vcap", player2Id, player1Id);
                }                
                //enable or disable SAVE CHANGE BUTTON
                checkForChanges();
            }
            function checkForChanges(){//enable or disable SAVE CHANGE BUTTON
                var saveChangeButton = document.getElementById("submitBtn");
                document.getElementById("formation").value = formationNew[0] + "-" + formationNew[1] + "-" + formationNew[2];
                if(!compareArrays(startingXI, startingXINew) || !compareArrays(subs, subsNew) || (captainNew !== captain)
                        || (viceCaptainNew !== viceCaptain) || !compareArrays(formation, formationNew) ){
                    saveChangeButton.disabled = false;
                }
                else{
                    saveChangeButton.disabled = true;
                }                
            }
            function isStarter(playerId){//function that checks if playerId is in starting XI
                var starter = true;     //flag that indicates if player is in starting XI(true) or on the bench(false)
                var i;
                for(i = 0; i < subsNew.length; i++){
                    if(subsNew.indexOf(playerId) >= 0){
                        starter = false;
                        break;
                    }
                }
                return starter;                
            }
            function changeButtonToSubAndUndimmAll(playerId){//function is called if sub is made or cancel button is pressed...
                var buttonId = "butt_" + playerId;
                var button = document.getElementById(buttonId);
                var buttons = document.getElementsByTagName("button");
                button.innerHTML = "SUB";                            
                var j;
                for(j = 0; j < 17; j++){
                    buttons[j].disabled = false;
                }                
            }            
            function changeCaptainsListAfterSubOnSamePos(type, player1, player2){//player1 subs player2, type = {cap, vcap}
                var optionId = type + "_" + player2;
                var captainOption = document.getElementById(optionId);//captain(vice captain) <option> to be changed
                captainOption.id = type + "_" + player1;
                captainOption.value = player1;
                captainOption.innerHTML = playersInfo[playersInfo.indexOf(player1) + 2];//write last name in option
                //update captainNew and viceCaptainNew
                if(type === "cap"){
                    if(captainNew === player2){//update only if captain is subbed
                        captainNew = player1;
                    }
                }
                else if(type === "vcap"){
                    if(viceCaptainNew === player2){//update only if vice captain is subbed
                        viceCaptainNew = player1;
                    }
                }               
            }
            function changeCaptainsListAfterSubOnDiffPos(type, player1, position1, player2){//player1 subs player2, type = {cap, vcap}
                var defNumber = formationNew[0] - "0";
                var midNumber = formationNew[1] - "0";
                var fwdNumber = formationNew[2] - "0";
                
                var captain1Option = document.createElement("OPTION");
                captain1Option.setAttribute("id", type + "_" + player1);
                captain1Option.setAttribute("value", player1);
                var text = document.createTextNode(playersInfo[playersInfo.indexOf(player1) + 2]);
                captain1Option.appendChild(text);
                var selectCaptain;//parent of options
                if(type === "cap"){
                    selectCaptain = document.getElementsByName("captain")[0];
                    if(captainNew === player2){//update only if captain is subbed and make selected player1
                        captain1Option.setAttribute("selected", true);
                        captainNew = player1;
                    }
                }
                else if(type === "vcap"){
                    selectCaptain = document.getElementsByName("vice_captain")[0];
                    if(viceCaptainNew === player2){//update only if vice captain is subbed
                        captain1Option.setAttribute("selected", true);
                        viceCaptainNew = player1;
                    }
                }  
                var indexToAppendCaptain1Option;
                switch(position1){
                    case "DEF":
                        indexToAppendCaptain1Option = defNumber + 1;
                        break;
                    case "MID":
                        indexToAppendCaptain1Option = defNumber + midNumber + 1;
                        break;
                    case "FWD":
                        indexToAppendCaptain1Option = defNumber + midNumber + fwdNumber + 1;
                        break;
                }
                if(position1 === "FWD"){
                    selectCaptain.appendChild(captain1Option);
                } 
                else{
                    selectCaptain.insertBefore(captain1Option, selectCaptain.childNodes[indexToAppendCaptain1Option]);
                }
                var captain2Option = document.getElementById(type + "_" + player2);//remove this option because player is subbed 
                captain2Option.parentNode.removeChild(captain2Option);
            }
            function changeCaptainsListAfterSubInXI(type, player1, player2){//change captains order in select options when both players are in starting XI ** unnecesery, but nice **
                    var option2 = document.getElementById(type + "_" + player2);
                    var option1 = document.getElementById(type + "_" + player1);
                    var option2IdTemp = option2.id;
                    var option2ValTemp = option2.value;
                    var option2InnerTemp = option2.innerHTML;
                    var option2SelTemp = option2.selected;
                    
                    option2.id = option1.id;
                    option2.value = option1.val;
                    option2.innerHTML = option1.innerHTML;
                    option2.selected = option1.selected;
                    option1.id = option2IdTemp;
                    option1.value = option2ValTemp;
                    option1.innerHTML = option2InnerTemp; 
                    option1.selected = option2SelTemp;
            }
            function compareArrays(array1, array2){
                if (array1.length !== array2.length){
                    return false;
                }
                var i;
                for(i = 0; i < array1.length; i++){
                    if(array1[i] !== array2[i]){
                        return false;
                    }
                }
                return true;
            }
            function swapElementsInArray(array, index1, index2){
                var temp = array[index1];
                array[index1] = array[index2];
                array[index2] = temp;    
            }
            function getChildNodeIndex(element){//returns index of "element"(parameter of function) element in its <div>
                //Note that function makes element = null at the end of while loop
                var i = 0;
                while( (element = element.previousSibling) !== null ){ 
                    i++;
                }
                return i;
            }
            //dimm button functions -> when substitution is imposible
            function dimmStartersOnDiffPos(position){
                var defNumber = formationNew[0] - "0";
                var midNumber = formationNew[1] - "0";
                var fwdNumber = formationNew[2] - "0";
                var i, id;
                if(position !== "GKP"){//dimm gkps
                    for(i = 0; i < 2; i++){
                        id = "butt_" + players[i];
                        document.getElementById(id).disabled = true;                        
                    }
                }                
                if(position !== "DEF"){//dimm strating defenders
                    for(i = 1; i < defNumber + 1; i++){
                        id = "butt_" + startingXINew[i];
                        document.getElementById(id).disabled = true;                        
                    }
                }
                if(position !== "MID"){//dimm strating midfielders
                    for(i = defNumber + 1; i < defNumber + midNumber + 1; i++){
                        id = "butt_" + startingXINew[i];
                        document.getElementById(id).disabled = true;                        
                    }
                }                
                if(position !== "FWD"){//dimm strating forwards
                    for(i = defNumber + midNumber + 1; i < defNumber + midNumber + fwdNumber + 1; i++){
                        id = "butt_" + startingXINew[i];
                        document.getElementById(id).disabled = true;                        
                    }
                }                
            }
            function dimmStartersOnPos(position){//dims all starters on position 
                var defNumber = formationNew[0] - "0";
                var midNumber = formationNew[1] - "0";
                var fwdNumber = formationNew[2] - "0";
                var i, id;
                switch(position){
                    case "GKP"://plus dimm gkp_sub
                        for(i = 0; i < 2; i++){
                            id = "butt_" + players[i];
                            document.getElementById(id).disabled = true;                        
                        }         
                        break;
                    case "DEF":
                        for(i = 1; i < defNumber + 1; i++){
                            id = "butt_" + startingXINew[i];
                            document.getElementById(id).disabled = true;                        
                        }        
                        break;  
                    case "MID":
                        for(i = defNumber + 1; i < defNumber + midNumber + 1; i++){
                            id = "butt_" + startingXINew[i];
                            document.getElementById(id).disabled = true;                        
                        }        
                        break; 
                    case "FWD":
                        for(i = defNumber + midNumber + 1; i < defNumber + midNumber + fwdNumber + 1; i++){
                            id = "butt_" + startingXINew[i];
                            document.getElementById(id).disabled = true;                        
                        }        
                        break;                         
                }
                
            }
            function dimmSubOnPos(position){//dimms only sub that plays on position {"GKP", "DEF", ...}
                var i, id;                                   
                for(i = 0; i < subsNew.length; i++){//dimm subs except gkp_sub
                    id = "butt_" + subsNew[i];
                    var indexSub = playersInfo.indexOf(subsNew[i]);
                    var positionSub = playersInfo[indexSub + 3];//find position of players on bench {"GKP", "DEF", ...} 
                    if(positionSub === position){
                        document.getElementById(id).disabled = true;
                        break;
                    }
                }                 
            }
            function changeCaptain(captainId){//function that changes previous captain with captainId
                if(viceCaptainNew === captainId){//if vice captain is equal to captainId, change vice captain to previous captain                   
                    document.getElementById("vcap_" + viceCaptainNew).selected = false;
                    document.getElementById("vcap_" + captainNew).selected = true;
                    //change <b>V</b> text
                    var vCaptainText = document.getElementById("butt_" + viceCaptainNew).nextSibling;
                    var buttonBeforeNewVCaptain = document.getElementById("butt_" + captainNew);
                    buttonBeforeNewVCaptain.parentNode.insertBefore(vCaptainText, (buttonBeforeNewVCaptain.nextSibling).nextSibling);
                    viceCaptainNew = captainNew;
                }
                //change <b>C</b> text
                var captainText = document.getElementById("butt_" + captainNew).nextSibling;
                var buttonBeforeNewCaptain = document.getElementById("butt_" + captainId);
                buttonBeforeNewCaptain.parentNode.insertBefore(captainText, buttonBeforeNewCaptain.nextSibling);
                captainNew = captainId;
                //enable or disable SAVE CHANGE BUTTON
                checkForChanges();
            }
            function changeViceCaptain(viceCaptainId){                
                if(captainNew === viceCaptainId){//if captain is equal to viceCaptainId, change captain to previous vice captain                   
                    document.getElementById("cap_" + captainNew).selected = false;
                    document.getElementById("cap_" + viceCaptainNew).selected = true;     
                    //change <b>C</b> text
                    var captainText = document.getElementById("butt_" + captainNew).nextSibling;
                    var buttonBeforeNewCaptain = document.getElementById("butt_" + viceCaptainNew);
                    buttonBeforeNewCaptain.parentNode.insertBefore(captainText, (buttonBeforeNewCaptain.nextSibling).nextSibling);                    
                    captainNew = viceCaptainNew;
                }
                //change <b>V</b> text
                var vCaptainText = document.getElementById("butt_" + viceCaptainNew).nextSibling;
                var buttonBeforeNewVCaptain = document.getElementById("butt_" + viceCaptainId);
                buttonBeforeNewVCaptain.parentNode.insertBefore(vCaptainText, buttonBeforeNewVCaptain.nextSibling);
                viceCaptainNew = viceCaptainId;
                //enable or disable SAVE CHANGE BUTTON
                checkForChanges();
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
            function onLoadFunction(){
                LoadDocFx('displayFixtures.php', showFixtures, '');                
            }
            function showFixtures(xhttp){
                document.getElementById("displayFixtures").innerHTML = 
                        xhttp.responseText;
            }
        </script>
    </head>
    <body onload="onLoadFunction()">
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <div class="container">
            <div class="points">
        <h1>Make your squad for GW<?php echo $activeGw; ?></h1>
            <?php
                echo "<form name='mysquad'>";
                echo "<h1 align='center'>First XI</h1>";
                if(isset($_GET['saved'])){
                    $msg = "Your team has been saved!";
                    echo "<span style='color:white; background-color:green'>". $msg . "</span>";
                }
                echo "<div class='startingeleven'>";
                echo "<div class='gkp' align='center'>";
                $index = array_search($players[0], $playersInfo);
                echo "<span title='Goalkeeper' class='span__hidden'>".$playersInfo[$index + 3].": </span>";
                echo "<input type='text' id='".$players[0]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")' readonly='readonly'>";                
                echo "<button id='butt_".$players[0]."' type='button' value='".$players[0]."' onclick='sub(this.value)'>SUB</button>";
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
                    echo "<input type='text' id='".$players[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")' readonly='readonly'>";
                    echo "<button id='butt_".$players[$i]."' type='button' value='".$players[$i]."' onclick='sub(this.value)'>SUB</button>";    
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
                    echo "<input type='text' id='".$players[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")' readonly='readonly'>";
                    echo "<button id='butt_".$players[$i]."' type='button' value='".$players[$i]."' onclick='sub(this.value)'>SUB</button>";      
                    if($captain == $players[$i]){
                        echo "<button id='captain' class='cap-vcap__butt' type='button'>C</button>";
                    }
                    elseif($vice_captain == $players[$i]){
                        echo "<button id='viceCaptain' class='cap-vcap__butt' type='button'>V</button>";
                    } 
                }
                echo "</div><br><br>";      
                
                echo "<div class='fwd' align='center'>"; 
                //echo "FWD: ";
                for($i = 12; $i < 12 + $formation[2]; $i++){
                    $index = array_search($players[$i], $playersInfo);
                    echo "<span title='Forward' class='span__hidden'>".$playersInfo[$index + 3].": </span>";
                    echo "<input type='text' id='".$players[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")' readonly='readonly'>";
                    echo "<button id='butt_".$players[$i]."' type='button' value='".$players[$i]."' onclick='sub(this.value)'>SUB</button>";   
                    if($captain == $players[$i]){
                        echo "<button id='captain' class='cap-vcap__butt' type='button'>C</button>";
                    }
                    elseif($vice_captain == $players[$i]){
                        echo "<button id='viceCaptain' class='cap-vcap__butt' type='button'>V</button>";
                    } 
                }
                echo "</div></div><br><br>"; 
                
                echo "<h1 align='center'>Subs</h1>";
                echo "<div class='subs'>";
                echo "<table>";
                echo "<th>GK</th><th>1</th><th>2</th><th>3</th>";
                $index = array_search($players[1], $playersInfo);
                echo "<tr><td>";
                echo "<span title='Goalkeeper'>".$playersInfo[$index + 3].": </span>";
                echo "<input type='text' id='".$players[1]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")' readonly='readonly'>";
                echo "<button id='butt_".$players[1]."' type='button' value='".$players[1]."' onclick='sub(this.value)'>SUB</button></td>";
                
                for($i = 1; $i < 4; $i++){                   
                    $index = array_search($subs[$i], $playersInfo);
                    echo "<td>";
                    $pos = $playersInfo[$index + 3];
                    $spanTitle = "";
                    switch($pos){
                        case "DEF":
                            $spanTitle = "Defender";
                            break;
                        case "MID":
                            $spanTitle = "Midfielder";
                            break;
                        case "FWD":
                            $spanTitle = "Forward";
                            break;
                    }
                    echo "<span title='".$spanTitle."'>".$pos.": </span>";                  
                    echo "<input type='text' id='".$subs[$i]."' value='".$playersInfo[$index + 2]."(".$playersInfo[$index + 1].")' readonly='readonly'>";
                    echo "<button id='butt_".$subs[$i]."' type='button' value='".$subs[$i]."' onclick='sub(this.value)'>SUB</button></td>";
                    
                }
                echo "</tr></table></div><br><br>"; 
                echo "</form>";
                ?>
        
        <form name="myteam" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
                <?php
                echo "<div class='hidden_id'>";
                echo "<input type='hidden' name='gkp1_id' id='hidd_".$players[0]."' value='".$players[0]."'>";
                for($i = 2; $i < 2 + $formation[0]; $i++){
                    echo "<input type='hidden' name='def".($i - 1)."_id' id='hidd_".$players[$i]."' value='".$players[$i]."'>";    
                }
                for($i = 7; $i < 7 + $formation[1]; $i++){
                    echo "<input type='hidden' name='mid".($i - 6)."_id' id='hidd_".$players[$i]."' value='".$players[$i]."'>";                    
                }
                for($i = 12; $i < 12 + $formation[2]; $i++){
                    echo "<input type='hidden' name='fwd".($i - 11)."_id' id='hidd_".$players[$i]."' value='".$players[$i]."'>";                    
                }
                echo "<input type='hidden' name='gkp2_id' id='hidd_".$players[1]."' value='".$players[1]."'>";                    
                for($i = 1; $i < 4; $i++){
                    echo "<input type='hidden' name='sub".$i."_id' id='hidd_".$subs[$i]."' value='".$subs[$i]."'>";                    
                }
                echo "</div>";
                
                //captain and vicecatain
                echo "<div class='captains' align='center'>";
                echo "Captain: <select name='captain' onchange='changeCaptain(this.value)'>";
                $selected = "";
                if($players[0] == $captain){
                    $selected = "selected";
                }                
                $index = array_search($players[0], $playersInfo);
                echo "<option id='cap_".$players[0]."' value='".$players[0]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                for($i = 2; $i < 2 + $formation[0]; $i++){
                    $selected = "";
                    if($players[$i] == $captain){
                        $selected = "selected";
                    } 
                    $index = array_search($players[$i], $playersInfo);
                    echo "<option id='cap_".$players[$i]."' value='".$players[$i]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                }
                for($i = 7; $i < 7 + $formation[1]; $i++){
                    $selected = "";
                    if($players[$i] == $captain){
                        $selected = "selected";
                    } 
                    $index = array_search($players[$i], $playersInfo);
                    echo "<option id='cap_".$players[$i]."' value='".$players[$i]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                }  
                for($i = 12; $i < 12 + $formation[2]; $i++){
                    $selected = "";
                    if($players[$i] == $captain){
                        $selected = "selected";
                    }
                    $index = array_search($players[$i], $playersInfo);
                    echo "<option id='cap_".$players[$i]."' value='".$players[$i]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                }                
                echo "</select>";
                echo "&nbsp; &nbsp; &nbsp; &nbsp;";
                echo "Vice-captain: <select name='vice_captain' onchange='changeViceCaptain(this.value)'>";
                $selected = "";
                if($players[0] == $vice_captain){
                    $selected = "selected";
                }
                $index = array_search($players[0], $playersInfo);
                echo "<option id='vcap_".$players[0]."' value='".$players[0]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                for($i = 2; $i < 2 + $formation[0]; $i++){
                    $selected = "";
                    if($players[$i] == $vice_captain){
                        $selected = "selected";
                    } 
                    $index = array_search($players[$i], $playersInfo);
                    echo "<option id='vcap_".$players[$i]."' value='".$players[$i]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                }
                for($i = 7; $i < 7 + $formation[1]; $i++){
                    $selected = "";
                    if($players[$i] == $vice_captain){
                        $selected = "selected";
                    } 
                    $index = array_search($players[$i], $playersInfo);
                    echo "<option id='vcap_".$players[$i]."' value='".$players[$i]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                }  
                for($i = 12; $i < 12 + $formation[2]; $i++){
                    $selected = "";
                    if($players[$i] == $vice_captain){
                        $selected = "selected";
                    }
                    $index = array_search($players[$i], $playersInfo);
                    echo "<option id='vcap_".$players[$i]."' value='".$players[$i]."' ".$selected.">".$playersInfo[$index + 2]."</option>";
                }                
                echo "</select>";
                echo "<input type='hidden' name='formation' id='formation' value='".$formation[0]."-".$formation[1]."-".$formation[2]."'>";

                echo "</div><br><br>";  
                echo "<center><button class='btn confirmation--button' name='submitBtn' id='submitBtn' disabled='disabled'>SAVE CHANGES</button></center>";                
            ?>
            </form>
            </div>
        </div>
        <div id="displayFixtures" class="elem--half div--center"></div>
    </body>
</html>
<?php } ?>