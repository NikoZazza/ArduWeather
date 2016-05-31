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
  <script>
      function updateSensor(id){
        var xhttp = new XMLHttpRequest();	
  		xhttp.onreadystatechange = function() {
		    if(xhttp.readyState == 4 && xhttp.status == 200){
    	 		 //alert(xhttp.responseText);
                 var g = xhttp.responseText.split("%");
                document.getElementById("sens_"+id).innerHTML = g[0];
               document.getElementById("data_"+id).innerHTML = g[1];
    		}
  		};
  		xhttp.open("GET", "lastTemp.php?id="+id, true);
  		xhttp.send();
        setTimeout(function() {
            updateSensor(id)
        }, 1000);
      }
  </script>
</head>
<body>
<h1 align="center">Stazione Metereologica</h1>
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
 if(!(isset($_SESSION["nickname"]) && isset($_SESSION["time"]) && $_SESSION["time"] < 60*60+time())){//facciamo durare una sessione al massimo di 1 ora
 	unset($_SESSION["nickname"]);
    unset($_SESSION["password"]);
    unset($_SESSION["time"]);    
 	header("Location: login.php");   
 }
?>
<div style="float:right">
	<a href="aggiungi.php"><button>Aggiungi sensore</button></a>
	<a href="?logout=true"><button>Logout</button></a>
</div>
<br><br>
<div style="background: rgba(204, 255, 255, 0.5);margin: 10px">
<?php
$res = $db->query("SELECT * FROM sensore ORDER BY ID") or die("Errore");
if(!$res->num_rows){
	echo "<div style='border: 1px solid;width:100%' align='center'>Non ci sono sensori presenti nel database!</div>";
}else{
    while($row = $res->fetch_array()){
        echo "<div style='border: 1px solid;width:100%'>
                  <div style='padding: 0px 10px'>
                      <div align='left' style='width:100%'>
                          <p><b>ID sensore:</b> ".$row["id"]."<span style='float:right'><b>Min:</b> ".$row["valMin"]." °C</span></p>
                          <p><b>Posizione:</b> ".$row["posizione"]."<span style='float:right'><b>Max:</b> ".$row["valMax"]." °C</span></p>
                          <a href='modifica.php?id=".$row["id"]."' style='float:right'>modifica</a>
                      </div>    
                      <div align='center' style='margin-left:33%;width:33%'>
                          <h1><span id='sens_".$row["id"]."'>0</span> °C</h1> Temperatura prelevata il <span id='data_".$row["id"]."'>0/0/0 00:00</span><br>
                          <script>updateSensor(".$row["id"].");</script>
                      </div>
                  </div>    
            </div>";   
    }
}
?>
</div>
</body>
</html>