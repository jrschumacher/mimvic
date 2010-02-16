<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rest shoutbox</title>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <link href="http://www.css-reset.com/css/meyer.css" media="screen, projection" rel="stylesheet" type="text/css" />
  <style>
	body {
		margin: 0;
		padding: 0;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		background: #222;
		color: #787878;
	}
	
	.shout h1{
		font-size: 2em;
	}
	.shout h2{
		float: left;
		font-size: 1.3em;
		color: #ccc;
		width: 200px;
	}
  </style>
  
  
  <script type="text/javascript">
	
	
	function shoutHTML(name, shout, id){
		return '<div class="shout"> <h2>'+name+'</h2> <span class="remove">X</span> <input name="shout_id" type="hidden" value="'+id+'" /><h1>'+shout+'</h1><hr/></div>';
	}
	
	function loadShouts(st){
		var loadedShouts = function(data, status){
			if(!data)
				return;
			for(var k=0;k<data.length;k++){
				$('#shouts').prepend( shoutHTML(data[k].name, data[k].shout, data[k].id ));
			}
		};
		
		$.ajax({
			"type": "GET",
			"url": "http://localhost/mimvic/trunk/example/shoutbox/index.php/get/from/"+st+"/next/20",
			"dataType": "json",
			"success": loadedShouts
		});
	}
	
	function deleteShout(){
		var domNode = $(this).parent();
		var id = $("input[name=shout_id]", domNode).val();
		$.ajax({
			"type": "delete",
			"url": "http://localhost/mimvic/trunk/example/shoutbox/index.php/delete/"+id,
			"dataType": "json",
			"success": function(data){
				if(data === false)
					return;
				$(domNode).remove();
			}
		});
	}
	
	function postShout(){
		$('#formArea input').attr('disabled', 'disabled');
		var name = $('#name').val();
		var shout = $('#shout').val();
		$.ajax({
			"type": "post",
			"url": "http://localhost/mimvic/trunk/example/shoutbox/index.php/save",
			"dataType": "json",
			"data": {
				"name": name,
				"shout": shout
			},
			"success": function(data){
				if(data)
					$('#shouts').prepend( shoutHTML(name, shout, data ));
					
				$('#formArea input').removeAttr('disabled');
			}
		});
	}
	
	$(document).ready(function(){
		$("#status").slideUp();
		$("#formArea").fadeIn();
		loadShouts(0);
		$('#formArea input[type=submit]').click(postShout);
		$('.shout .remove').live('click', deleteShout);
	});
	
  </script>
</head>
<body>
	<div id="status">Please wait...</div>
	<div id="formArea" style="display: none;">
		<input id="name" type="text" value="" /><input id="shout" type="text" value="" style="width: 300px;" /><input type="submit" value="Add" />
	</div>
	<div id="shouts">
	</div>
</body>
</html>