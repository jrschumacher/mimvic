<?php

require_once '../../uvic.php';

use MiMViC as mvc;
mvc\startBenchmark('boot');

class SampleHandler implements mvc\ActionHandler {
	public function exec($params){
		echo '<pre>';
		var_dump($params);
		echo '</pre>';
	}
}

class AnotherHandler implements mvc\ActionHandler {
	public function exec($params){
		echo 'done';
	}
}

mvc\get('/do/*', mvc\Action('SampleHandler'));
mvc\get('/*', mvc\Action('AnotherHandler'));
mvc\start();

echo '<br/>'.mvc\calcBenchmark('boot');
?>
