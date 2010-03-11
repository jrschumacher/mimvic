<?php

require_once '../../uvic.php';

use MiMViC as mvc;

define ('WEB_ROOT', '/mimvic/trunk/example/getparams');

mvc\get('/',
	function ($params){
		mvc\render('template.php');
	}
);

mvc\get('/a/sub/path/*/',
  function ($params){
    var_dump($params);
    echo '<br />';
    var_dump($_GET);    
  }
);


$stat = mvc\start();

if($stat === NULL){
	echo "<h1>Invalid action</h1><br/>Try Something else...";
}
?>