<?php
require_once '../uvic.php';

use MiMViC as mvc;

mvc\post('/add',
	function ($params){
		var_dump($_POST);
	}
);

mvc\get('/', 
	function (){
		mvc\render('views/header.php', array('title' => 'Hello !'));
		mvc\render('views/form.php');
		mvc\render('views/footer.php');
	}
);

mvc\start();

?>