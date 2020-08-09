<?php
echo ("5" - "3") - "0";



/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>


<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script>
            
            loadDoc("url-1", myFunction1);
            loadDoc("url-2", myFunction2);

            function loadDoc(url, cFunction) {
                if (window.XMLHttpRequest)
                {
                    xmlhttp = new XMLHttpRequest();
                }
                else
                {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                      cFunction(this);
                    }
                };
                xhttp.open("GET", url, true);
                xhttp.send();
            }
            function myFunction1(xhttp) {
                // action goes here
                //for example:
                document.getElementById("div1").innerHTML = 
                        xhttp.responseText;
            } 
            
            function myFunction2(xhttp) {
              // action goes here
            }
            var players = new Array();
            var arrayOfPlayers = players.join("|");
            var i = 3;
            function removePlayer(int){
                int++;return true;
            }
        </script>
    <body>
          <?php echo '<li><a href="?limit=10&page=2">next</a></li>'; ?>

    </body>
</head>
</html>