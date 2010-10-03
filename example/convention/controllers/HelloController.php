<?php

class HelloController {
	public function index(){
		echo 'Hello world';
	}
	
	public function to($name){
		echo 'Hello '.$name;
	}
}

?>