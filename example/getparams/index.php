<?php

require_once '../../uvic.php';

use MiMViC as mvc;

mvc\get('/do/nothing/',
	function ($params){
		var_dump($_GET);
	}
);


$stat = mvc\start();

if($stat === NULL){
	echo "<h1>Invalid action</h1><br/>Try Something else...";
}
?>