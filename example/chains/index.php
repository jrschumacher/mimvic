<?php

require_once '../../uvic.php';

use MiMViC as mvc;

function htmlHead($params){
	echo '<html><head><title>Testing</title></head><body>';
}

mvc\createChain('normalheader',
	'htmlHead', 
	function($params){
		echo '<b>A header</b><br/>';
	}
);
	
mvc\createChain('specialHeader', 
	'htmlHead',
	function ($params){
		echo '<h1>Special header</h1>';
	}
);

mvc\createChain('footer', 
	function (){
		echo '</body></html>';
	}
);

mvc\get('/',
	mvc\chain('normalheader',
		function (){
			echo 'hmmm.....';
		},
		'footer'
	)
);

mvc\get('/do/*/',
	mvc\chain('specialHeader',
		function ($params){
			echo '<pre>';
			var_dump($params);
			echo '</pre>';
		},
		'footer'
	)
);


$stat = mvc\start();

if($stat === NULL){
	echo "<h1>Invalid action</h1><br/>Try Something else...";
}
?>