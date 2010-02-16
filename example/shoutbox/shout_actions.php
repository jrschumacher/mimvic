<?php
use MiMViC as mvc;

mvc\dispatch('*','/',
	function (){
		mvc\render('views/shouts/main.php');
	}
);

mvc\get('/get/from/:page/next/:limit',
	function ($params){
		$query = 'select * from shouts limit '.((int)$params['page']).','.((int)$params['limit']);
		$stmt = mvc\retrieve('db')->prepare($query);
		if(!$stmt->execute())
			die('false');
		$rA = $stmt->fetchAll();
		echo json_encode($rA);
	}
);

mvc\get('/get/:id',
	function ($params){
		$stmt = mvc\retrieve('db')->prepare("select * from shouts where id = :id");
		$status = $stmt->execute( array('id' => $params['id']) );
		if(!$stmt->execute())
			die("false");
		$rA = $stmt->fetchAll();
		echo json_encode($rA);
	}
);

mvc\post('/save',
	function (){
		$stmt = mvc\retrieve('db')->prepare("insert into shouts(name, shout) values (:name, :shout)");
		$status = $stmt->execute( array('name' => $_POST['name'], 'shout' => $_POST['shout']) );
		if(!$status)
			die("false");
		else
			echo json_encode(mvc\retrieve('db')->lastInsertId());
	}
);

mvc\delete('/delete/:id',
	function ($params){
		$stmt = mvc\retrieve('db')->prepare("delete from shouts where id = :id");
		$status = $stmt->execute( array('id' => $params['id']) );
		if(!$stmt->execute())
			die("false");
		echo json_encode($stmt->rowCount());
	}
);

mvc\put('/update/:id',
	function ($params){
	}
);

?>