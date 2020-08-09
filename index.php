<!DOCTYPE html>
<?php
session_start();

require_once 'dbconfig.php';
require_once 'Connection.inc.php'; 
require_once 'test_input.inc.php';

$connection = new Connection();
$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT active_gw,game_updating FROM gw_status";
$result = $connection->getResult($query);

$array = mysqli_fetch_array($result);

$gameUpdating = (int)$array['game_updating'];
$activeGw = $array['active_gw'];



?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script>
        
        </script>
        <link rel="stylesheet" type="text/css" href="fantazzi.css">
    </head>
    <body>
        <div class="navbar"> <?php include 'navbar.php'; ?> </div>
        <div class="container">
        <?php
        if($gameUpdating){
            echo "<h1>Game is currently being updated!</h1>";
        }
        if(isset($_SESSION['user_name'])){
            $userName = $_SESSION['user_name'];
            $userLastname = $_SESSION['user_lastname'];
            //ako se user uloguje
            echo "<p>You are logged in as ".$userName." ".$userLastname."</p>";
            if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1){
                echo "<p>ADMINISTRATOR</p>";
            }
            
            
            
        }    
        elseif(isset($_POST['submitBtn'])){
            if(!empty($_POST['email']) && !empty($_POST['password'])){
                               
                //provera da li se loguje admin
                if(test_input($_POST['email']) == "admin@admin" && test_input($_POST['password']) == "admin"){
                    header("Location: index.php?admin");
                    exit();                    
                }
                else{//loguje se  korisnik
                    //treba dopuniti izraze za SQL injection
                    $email = mysqli_real_escape_string($connection->getLink(),test_input($_POST['email']));
                    $password = mysqli_real_escape_string($connection->getLink(),test_input($_POST['password']));                    
                    $query = "SELECT * FROM login WHERE email='".$email."' AND password='".$password."' LIMIT 1";
                    $result = $connection->getResult($query);

                    if(mysqli_num_rows($result)==1){//ako je pronadjen korisnik sa datim email-om i pass
                        $row = mysqli_fetch_array($result);
                        $_SESSION['id_user'] = $row['id_user'];
                        $_SESSION['user_name'] = $row['name'];
                        $_SESSION['user_lastname'] = $row['last_name'];
                        $_SESSION['user_teamname'] = $row['team_name'];
                        $_SESSION['picked_team'] = $row['picked_team'];
                        $_SESSION['registration_gw'] = $row['registration_gw'];

                        $connection->close();
                        header("Location: myteam.php");
                        exit();
                    }
                    else{
                        $connection->close();
                        header("Location: index.php?fail"); 
                        exit();                         
                    }                     
                }
            }
        } 
        elseif(isset($_POST['submitBtnAdmin'])){//ako se loguje admin
            //treba dopuniti izraze za SQL injection
            $email = mysqli_real_escape_string($connection->getLink(),test_input($_POST['email']));
            $password = mysqli_real_escape_string($connection->getLink(),test_input($_POST['password']));            
            
            $query = "SELECT * FROM admin WHERE email='".$email."' AND password='".$password."' LIMIT 1";
            $result = $connection->getResult($query);
                    
            if(mysqli_num_rows($result)==1){//ako je pronadjen administrator sa datim email-om i pass
                $row = mysqli_fetch_array($result);
                $_SESSION['id_admin'] = $row['id_admin'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_lastname'] = $row['last_name'];
                $_SESSION['admin'] = 1;
                        
                $connection->close();
                header("Location: status.php");
                exit();
            }
            else{
                $connection->close();
                header("Location: index.php?fail"); 
                exit();                         
            }            
        }
        elseif(isset($_GET['admin'])){//ako se loguje admin
        ?>
            <div class="loginform"> 
                <form name="adminLogin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <table>
                        <th colspan="2"><span style="color:white; background-color:red">ADMIN</span></th>
                        <tr>
                            <td>E-mail:</td>
                            <td><input type="email" name="email" class="form__login--email"></td>
                        </tr>
                        <tr>
                            <td>Password:</td>
                            <td><input type="password" name="password"></td>                            
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><input type="submit" name="submitBtnAdmin" value="LOG IN"> </td>
                        </tr> 
                    </table>
            </div>
                        
        <?php
        }
        else{
            if($gameUpdating){
                $connection->close();
                exit();
            }
            else{
                if(isset($_GET['fail'])){
                    $error = "Incorrect email or password!";
                    echo "<span style='color:white; background-color:red'>". $error . "</span>";
                }
            ?>
                
                <form name="loginForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <div class="loginform">
                        <div class="div--email div--margin">
                            <div>
                                <label class="form--label" for="user-email">Email Address</label>
                                <input type="email" name="email" id="user-email" class="form__login" required>
                            </div>
                        </div>
                        <div class="div--password div--margin">
                            <div>
                                <label class="form--label" for="user--password">Password</label>
                                <input type="password" name="password" id="user--password" class="form__login" required>  
                            </div>
                        </div>
                        <div class="div--log__in">
                            <div class="div__bottom div--full">
                            <input class="butt--full butt--login" type="submit" name="submitBtn" value="LOG IN" >
                            
                            </div>
                            
                        </div>
                 </div>  
                 </form>
                <div class="signup">
                    <div>
                        <h2 class="signup--heading">Register to play Fantazzi</h2>
                    </div>
                     <div><a class="btn signup--btn" href="signup.php">Sign up</a></div>
                 </div>
                
            
        </div>
            <?php
            }
        }
        ?>
    </body>
</html>
