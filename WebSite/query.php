<?php
$db = new mysqli("localhost", "xionbig", "", "my_xionbig") or die("Errore connessone al database.");
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
if(isset($_GET["id"]) && isset($_GET["temperatura"])){
	$res = $db->query("SELECT * FROM sensore WHERE id = ".$_GET["id"]);
	if($res->num_rows){
 		$db->query("INSERT INTO temperatura(id_sensore, temperatura, time) VALUES
        	(".$_GET["id"].", ".$_GET["temperatura"].", ".time().")") or die($mysql->errno);   
    	$res = $db->query("SELECT * FROM sensore WHERE id = ".$_GET["id"]);
        while($row = $res->fetch_array()){
        	echo $row["valMin"]."%".$row["valMax"];
            exit();
        }
    }else{
    	echo "Sensore non esiste!";
    }
}
?>