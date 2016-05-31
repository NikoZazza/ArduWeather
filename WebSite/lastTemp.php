<?php
$db=new mysqli("localhost","xionbig","","my_xionbig")or die($db->errno);
if(isset($_GET["id"]) && is_numeric($_GET["id"])){
	$res = $db->query("SELECT * FROM temperatura WHERE id_sensore = ".$_GET["id"]. " ORDER BY time DESC;")or die();
    if($res->num_rows){    
        while($row = $res->fetch_array()){
            echo $row["temperatura"]."%".date("d/M/y G:i:s", $row["time"]);
            exit();
        }
    }else{
    	echo "0.0%0/0/0 00:00";
    }
}else{
	echo "0.0%0/0/0 00:00";
}
?>