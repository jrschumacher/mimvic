<?php

require_once '../../uvic.php';

use MiMViC as mvc;

mvc\startBenchmark('boot');

mvc\get('/:total',
	function ($params){
		for($i=0;$i<$params['total'];$i++) ;
	
		echo "Total time consumed ".mvc\calcBenchmark('boot');
	}
);

mvc\start();

?>