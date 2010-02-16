<?php
use MiMViC as mvc;

$dbh = new PDO("mysql:host=localhost;dbname=shoutbox","root","");

mvc\store('db', $dbh);

?>