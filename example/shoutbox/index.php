<?php
error_reporting(E_STRICT);
require_once '../../uvic.php';

use MiMViC as mvc;
define('BASE_URL', '/mimvic/trunk/example/shoutbox/index.php/');

require_once 'db.php';
require_once 'shout_actions.php';

mvc\start();
$endTm = mvc\calcBenchmark('bootup');
?>