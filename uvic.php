<?php

/*

Copyright (c) 2009 Zohaib Sibt-e-Hassan ( MaXPert )

MiMViC v0.9

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

namespace MiMViC;

$uvicConfig = array();

$uvicConfig['maps'] = array( );

//set associative value
function store($name, &$value){
	global $uvicConfig;
	$uvicConfig[$name] = $value;
}

//get associative value
function &retrieve($name){
	global $uvicConfig;
	return $uvicConfig[$name];
}

//Get URI segement after the script URI
function ugetURI(){
	//Seprate Segments
	$req=$_SERVER['REQUEST_URI'];
	$page=$_SERVER['SCRIPT_NAME'];

	// Try if its mod_rewrite
	if( stripos($req, $page) === FALSE && isset( $_SERVER['REDIRECT_URL'] ) ){
		$page = explode('/', $page);
		$page = array_slice($page, 0, -1);
		$page = join('/', $page)."/";
	}
	
	//Bug fix 2/20/2008 if no index.php was present at the end :D
	if(strlen($req)<strlen($page))
		$req=$page;
	
	
	//make sure the end part exists...
	$req=str_replace($page,'',$req);
	
	if(!strlen($req))
		return "/";
	
	return $req;
}

//Get request method
function ugetReqMethod(){
	return strtolower( $_SERVER['REQUEST_METHOD'] );
}

/*
* Parse URI segement returning true if $uri matches $pattern
* Pattern can have expression like following
*	# /foo/:varname/bar => param['varname'] will contain value
*	# /foo/* /bar/* => param['segments'] will contain all * parsed params
*/

function uparseURIParams($pattern, $ur){
	$psegs = explode('/', $pattern); //Pattern segments
	$usegs = explode('/', $ur); //URI segments
	
	$ret = array('segments' => array() );
	
	while( count($psegs) && count($usegs) ){
		$pseg=$psegs[0];
		array_shift($psegs);
		
		if(strlen($pseg) && $pseg[0] == ':'){ //Incase :foo
			//remove :
			$pseg = substr($pseg,1);
			// assign to ret
			$ret[$pseg] = urldecode(array_shift($usegs));
		}else if($pseg == $usegs[0]){ //Incase of simple match
			array_shift($usegs);
		}else if($pseg == '*'){
			//Repeat extraction untill first match found in next segments (/foo/*/bar/* bar in URL) 
			$segment = array();
			while( count($usegs) && $psegs[0] != $usegs[0] )
				array_push( $segment, array_shift($usegs) );
				
			array_push($ret['segments'], $segment);
		}else
			return false;
	}
	
	if( count($psegs) || count($usegs) )
		return false;
	
	return $ret;
}

/*
* Trigger the $uri matching function according to request $method
*/
function utriggerFunction($uri, $method){
	global $uvicConfig;
	$map = &$uvicConfig['maps'];
	
	foreach($map as $patrn => $info){
		//Try to match
		$cParams = uparseURIParams($patrn, $uri);
		//Catch validity and call
		if( is_array($cParams) )
		{
			// Select the first fit method and call it
			foreach($info as $inf){
				if($inf['method'] == $method && is_callable($inf['func']) && ( $inf['agent'] === false || preg_match($inf['agent'], $_SERVER['HTTP_USER_AGENT']) > 0 ) ){
					if( is_string($inf['func']) ){
						$func = $inf['func'];
						$ret = $func($cParams);
					}else{
						$ret = $inf['func']($cParams);
					}
					return $ret;
				}
			}
			break;
		}
	}
	
	//System failed to find any match
	return NULL;
}

// Trigger on request type
function dispatch($method, $uri, $func, $agent = false){
	global $uvicConfig;
	$map = &$uvicConfig['maps'];
	
	//--If method was array then for all methods register the function
	if( is_array($method) )
		foreach($method as $mthd)
			dispatch($mthd, $uri, $func, $agent = false);
	
	//--If URI was array then for all URIs register the function
	if( is_array($uri) )
		foreach($uri as $one_url)
			dispatch($methd, $one_url, $func, $agent = false);
	
	if( !isset($map[$uri]) )
		$map[$uri] = array();
	
	$map[$uri][] = array('method'=> $method, 'func'=> $func, 'agent' => $agent);
}

// get request register
function get($uri, $func, $agent = false){
	dispatch('get', $uri, $func, $agent);
}

// post request register
function post($uri, $func, $agent = false){
	dispatch('post', $uri, $func, $agent);
}

// put request register
function put($uri, $func, $agent = false){
	dispatch('put', $uri, $func, $agent);
}

// delete request register
function delete($uri, $func, $agent = false){
	dispatch('delete', $uri, $func, $agent);
}

//Render template with $template_name file path and $_tempateData containing associative data
function render($template_name,$_templateData=array()){
	if(stristr($template_name,'.php')===FALSE)
		$template_name=$template_name.'.php';
	
	//Create variables for each of sent data index
	extract($_templateData,EXTR_OVERWRITE);
	
	//Check existance and load file
	if(file_exists($template_name))
		require($template_name);
	else
		return NULL;
		
	return true;
}

// Start the engine
function start(){
	$url = ugetURI();
	$ret = utriggerFunction( $url , ugetReqMethod() );
}

?>
