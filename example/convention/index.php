<?php

require_once '../../uvic.php';
require_once '../../plugins/uvic.convention.php';

use MiMViC as mvc;
mvc\startBenchmark('boot');

for($i=0;$i<100;$i++){
	mvc\get("/do{$i}/:action", function($p){
		var_dump($_GET, $p);
	});
}

mvc\Convention::hook(realpath('./controllers'));

mvc\start();
echo '<br/>'.mvc\calcBenchmark('boot');

?>
