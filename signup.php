<!DOCTYPE html>
<?php
require_once 'dbconfig.php';
require_once 'Connection.inc.php'; 
require_once 'test_input.inc.php';

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT active_gw,game_updating FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$activeGw = $array['active_gw'];
$gameUpdating = (int)$array['game_updating'];

if($gameUpdating){
    header("Location: index.php");
    exit();
}

if(isset($_POST['submitBtn'])){    
    $email = mysqli_real_escape_string($connection->getLink(),test_input($_POST['email']));
    $password = mysqli_real_escape_string($connection->getLink(),test_input($_POST['password']));
    $name = mysqli_real_escape_string($connection->getLink(),test_input($_POST['name']));
    $lastname = mysqli_real_escape_string($connection->getLink(),test_input($_POST['lastname']));
    $teamname = mysqli_real_escape_string($connection->getLink(),test_input($_POST['teamname']));
    
    $query = "SELECT email FROM login WHERE email='".$email."' LIMIT 1";
    $result = $connection->getResult($query);
    
    if (mysqli_num_rows($result)==0){ //email is not used by any other user
        $query = "INSERT INTO login (email, password, name, last_name, team_name, bank, picked_team) VALUES ('".$email."','".
                $password."','".$name."','".$lastname."','".$teamname."', '100', '0')"; 

        $connection->getResult($query);

        $query = "SELECT id_user FROM login WHERE email='".$email."' LIMIT 1";
        $result = $connection->getResult($query);
        
        session_start();
        $row = mysqli_fetch_array($result);
        
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['user_name'] = $name;
        $_SESSION['user_lastname'] = $lastname;
        $_SESSION['user_teamname'] = $teamname;
        $_SESSION['bank'] = 100;
        $_SESSION['picked_team'] = 0;
        $_SESSION['registration_gw'] = 0;
        
        header("Location: makeyoursquad.php");
        exit();       
    }
    else {// email is taken by another user
        header("Location: signup.php?emailTaken");
        exit();
    }
}

else{

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>SIGN UP</title>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
        <script>
            function checkInput(){
                
                var name = document.signUpForm.name.value;
                var lastName = document.signUpForm.lastname.value;
                var teamName = document.signUpForm.teamname.value;
                //var emailFree = document.signUpForm.emailFree.value;
                var password = document.signUpForm.password.value;
                var confirmPassword = document.signUpForm.confirmPassword.value;
                
                var passConfError = false;
                //var emailTaken = false;
                
                if (password !== confirmPassword){
                    passConfError = true;

                if (passConfError){
                    alert("Pogresna potvrda password-a!");
                    return false;
                }
                return true;
            }
        }
            

        </script>
    </head>
    <body>
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <div class="container">
        <form name="signUpForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"
              onsubmit="return checkInput()">
            
            <table class="elem--half"> 
                <th class="div--third"></th><th></th>
                <tr><td><label class="form--label" for="user--email">E-mail</label></td>
                    <td><input class="div--full form__login" type="email" name="email" id="user--email" placeholder="e-mail" required>
                <?php if(isset($_GET['emailTaken'])) echo "<span style='color:white; background-color:red'>E-mail taken</span>"; ?>
                    </td></tr>
                <tr><td><label class="form--label" for="user--name">Name</label></td>
                    <td><input class="div--full form__login" type="text" name="name" id="user--name" placeholder="name" required></td></tr>
                <tr><td><label class="form--label" for="user--lastname">Last name</label></td>
                    <td><input class="div--full form__login" type="text" name="lastname" id="user--lastname" placeholder="lastname" required></td></tr>
                <tr><td><label class="form--label" for="user--teamname">Team name</label></td>
                    <td><input class="div--full form__login" type="text" name="teamname" id="user--teamname" placeholder="teamname" required></td></tr>
                <tr><td><label class="form--label" for="user--password">Password</label></td>
                    <td><input class="div--full form__login" type="password" name="password" id="user--password" placeholder="password" required></td></tr>
                <tr><td><label class="form--label" for="user--confirmpassword">Confirm password</label></td>
                    <td><input class="div--full form__login" type="password" name="confirmPassword" id="user--confirmpassword" placeholder="password" required></td></tr>                
                <tr><td colspan="2" align="center"><input class="div--full butt--login" type="submit" name="submitBtn" value="CONFIRM" required></td></tr>                             
            </table>
        </form>    
        </div>
    </body>
</html>
<?php } ?>