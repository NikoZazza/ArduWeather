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
<h1 align="center">Stazione Metereologica</h1><br>
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
 if(isset($_GET["logout"])){
 	unset($_SESSION["nickname"]);
	unset($_SESSION["password"]);
 	unset($_SESSION["time"]);
    header("Location: login.php");
 }
 if(isset($_SESSION["nickname"]) && isset($_SESSION["time"]) && $_SESSION["time"] < 60*60+time()){//facciamo durare una sessione al massimo di 1 ora
 	if($db->query("SELECT * FROM utente WHERE nick = '".$db->escape_string($_SESSION["nickname"])."' AND pwd = '".$db->escape_string($_SESSION["password"])."' AND livello = 1")->num_rows){
        if(isset($_POST["localita"]) && isset($_POST["min"]) && isset($_POST["max"])){
            echo "<div style='background: rgba(255, 51, 51, 0.5);' align='center'>";
            if(is_null($_POST["localita"])){
                echo "Errore: Località errata!";
            }elseif(!(is_numeric($_POST["min"]) || is_float($_POST["min"]))){
                echo "Errore: Il valore minimo deve essere un numero!";
            }elseif(!(is_numeric($_POST["max"]) || is_float($_POST["max"]))){
                echo "Errore: Il valore minimo deve essere un numero!";
            }else{
            	if($_POST["min"] > $_POST["max"]){
                	$a = $_POST["min"];
                    $_POST["min"] = $_POST["max"];
                    $_POST["max"] = $a;
                }
                echo "</div><div style='background: rgba(153, 255, 51, 0.5);' align='center'>";
                $db->query("INSERT INTO sensore(posizione, valMin, valMax) VALUES ('".$db->escape_string($_POST["localita"])."', ".$_POST["min"]." , ".$_POST["max"].")");
                echo "Il sensore che si trova in <b>'".$_POST["localita"]."'</b> è stato aggiunto con successo!";
                echo "</div><div>";
            } 
        }
        echo "</div>";
        ?>
        <br>
        <div align="center">
            <form method="POST" style="background: rgba(204, 204, 204, 0.5);">
                <br><p>Località:
                <input type="text" name="localita" placeholder="Località del sensore"></p>
                <p>Valore minimo in cui l'allarme si deve attivare: 
                <input type="text" name="min" placeholder="Valore minimo"></p>
                <p>Valore massimo in cui l'allarme si deve attivare: 
                <input type="text" name="max" placeholder="Valore massimo"></p>
                <a href="index.php"><input type="button" value="Annulla"></a>
                <input type="submit" value="Aggiungi">
                <br>
            </form>
        </div>
        <?php
    }else{
    	echo "<div style='background: rgba(255, 51, 51, 0.5);' align='center'><h3><p style='color:orange'>Non sei autorizzato ad aggiungere i sensori!</p></h3>";
    	echo "<a href='index.php'><button><- Torna indietro</button></a><br></div>";
        exit();
    }
 }else{
 	header("Location: login.php");
 }
?>
</body>
</html>