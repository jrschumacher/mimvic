<html>
	<head>
    <title>Get Parameters Demo</title>
  </head>
	<body>
	   <h1>Get Paramters demo!</h1>
	   <p>
	   	<a href="<?php echo WEB_ROOT?>/index.php/?do=some&perform=action">Passing parameters to this page</a><br />
	   	<a href="<?php echo WEB_ROOT?>/index.php/a/sub/path/and/?with=params">Sub action without mod_rewrite</a><br />
	   	<a href="<?php echo WEB_ROOT?>/a/sub/path/and/?with=params">Sub action with mod_rewrite</a><br />
	   </p>
	   
	   <div>
	   	<pre><?php var_dump($_GET) ?></pre>
	   </div>
  </body>
</html>