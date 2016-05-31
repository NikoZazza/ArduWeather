<html>
<head>
  <meta charset="utf-8">
  <title>Stazione Metereologica</title>
  <style>body {
	  	background-image: url("meteo.jpg");
  		background-color: white;
        background-repeat: no-repeat;
        background-position: right top;
    	background-attachment: fixed;
        background-size: 100%;
	}    
  </style>
</head>
<body>
<?php
 session_start();
 $db=new mysqli("localhost","xionbig","","my_xionbig")or die($db->errno);
 $db->query("CREATE TABLE IF NOT EXISTS utente (
		id INT(10) PRIMARY KEY AUTO_INCREMENT,
		nick VARCHAR(30),
		pwd VARCHAR(30),
		livello INT(1)		
 );")or die($db->errno);
 if(!$db->query("SELECT * FROM utente WHERE nick = 'guest'")->num_rows)
 	$db->query("INSERT INTO utente (nick, pwd, livello) VALUES ('guest', 'guest', 0)");
 $db->query("CREATE TABLE IF NOT EXISTS sensore (
		id INT(10) PRIMARY KEY AUTO_INCREMENT,
		posizione VARCHAR(30),
		valMin FLOAT(30),
		valMax FLOAT(30)	
 );")or die($db->errno);
  $db->query("CREATE TABLE IF NOT EXISTS temperatura (
		id INT(10) PRIMARY KEY AUTO_INCREMENT,
		id_sensore INT(10) REFERENCES sensore(id),
		temperatura FLOAT(30),
		time INT(20)	
 );")or die($db->errno);
  
 if(isset($_POST['nickname']) && isset($_POST['password']))
 {
 	echo "<div style='color: orange; font-size: 18px;background: rgba(255, 0, 0, 0.5);' align='center'>";
 	$nickname = strtolower(trim($_POST["nickname"]));
    $password = trim($_POST["password"]);
    $errore = false;
 	if($nickname == ""){
    	echo "Errore: il campo nickname è errato!<br>";
        $errore = true;
    }
    if($password == ""){
    	echo "Errore: il campo password è errato!<br>";
        $errore = true;
    }
    if(!$errore){
    	$res = $db->query("SELECT nick, pwd 
        					FROM utente 
                            WHERE nick = '".$db->escape_string($nickname)."' 
                            AND pwd = '".$db->escape_string($password)."';");
    	if($res->num_rows){
        	$_SESSION["nickname"] = $nickname;
            $_SESSION["password"] = $password;
            $_SESSION["time"] = time();
            header("Location: index.php");
        }else{
        	echo "Utente non registrato o password errata!<br>";
        }
        
    }      
    echo "</div>";    
 }
?>
  <div class="container" align="center">
    <div class="login">
      <h1>Stazione Metereologica - Login</h1>
       <form id="login" action="<?=$_SERVER['PHP_SELF'];?>" method='POST'>
          <p>
          Nome utente:
		  <input type='text' id="nickname" name='nickname' placeholder='Username'>
		  </p>
          <p>
          Password:
		  <input type='password' id="password" name='password' placeholder='Password'>
		  </p>
          <input type="submit" value="Entra">		  
      </form>
    </div>
    <div style="background: rgba(204, 204, 204, 0.5);">
	<p align="center">Se vuoi accedere come utente inserisci i dati necessari per effettuare il login <br>
	oppure<br> puoi accedere come utente visitatore <button onclick="guest()">cliccando qui</button></p> 	
    </div>
  </div>
</body>
<script>
function guest(){
	document.getElementById("nickname").value = "guest";
    document.getElementById("password").value = "guest";
    document.getElementById("login").submit();
}
</script>

</html>
