<?php

/*

Copyright (c) 2009 Zohaib Sibt-e-Hassan ( MaXPert )

MiMViC Shift v0.9.4

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

/**
* Class for throwing MiMViC specific exceptions
*/
class MiMViCException extends \Exception{
    
}

/**
* Global array to hold configuration of data for the framework
* 
* @var array
*/
$uvicConfig = array();

/**
* Route maps for the trigger actions
* 
* @var array
*/
$uvicConfig['maps'] = array( );

/**
* Associative array for holding the user stored data
* 
* @var array
*/
$uvicConfig['userData'] = array( );

/**
* Associative array for holding bechmark data
*
* @var array
*/
$uvicConfig['bechmark'] = array( );

/**
* Store the user associated data key value pairs
* 
* @param mixed $name name of parameter to be set
* @param mixed $value value of parameter to be set
*/
function store($name, &$value){
	global $uvicConfig;
	$uvicConfig['userData'][$name] = $value;
}


/**
* Retrieve the user stored value
* 
* @param mixed $name
* @return mixed stored data against given $name
*/
function retrieve($name){
	global $uvicConfig;
    if(!isset($uvicConfig['userData'][$name]))
        return null;
	return $uvicConfig['userData'][$name];
}

/**
* Tell if mod_rewrite is detected
* 
* @return boolean true if detected false otherwise
*/
function isModRewrite(){
	global $uvicConfig;
	if( isset($uvicConfig['mod_rewrite_detected']) )
		return $uvicConfig['mod_rewrite_detected'];
	
	$req=$_SERVER['REQUEST_URI'];
	$page=$_SERVER['SCRIPT_NAME'];
	
	if ( stripos($req, $page) === FALSE && isset( $_SERVER['REDIRECT_URL'] ) )
		$uvicConfig['mod_rewrite_detected'] = true;
	else
		$uvicConfig['mod_rewrite_detected'] = false;
	
	return $uvicConfig['mod_rewrite_detected'];
}

/**
* Remove the regular expression ?.+ from URL
*
* @param string $url url to strip get parameters from it
*/
function uRemoveGetParams($url){
	$relUrl = explode('?', $url);
	return $relUrl[0];
}

/**
* Get URI segement after the script URI
* 
* @return string relative uri containing the path after the index.php
*/
function ugetURI(){
	//Seprate Segments
	$req=uRemoveGetParams($_SERVER['REQUEST_URI']);
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
	
	// if the starting '/' is missing append it
	if(strlen($req)=== 0 || $req[0]!=='/')
		$req = '/'.$req;
	
	return $req;
}

/**
* Get request method
* 
* @return string lowered case request methond ( currently supporting GET, PUT, DELETE, POST)
*/
function ugetReqMethod(){
	return strtolower( $_SERVER['REQUEST_METHOD'] );
}

/**
* Parse URI segement. Tries to match URL on given pattern without using regexp for performance.
* Pattern can have expression like following
*    # /foo/:varname/bar => param['varname'] will contain value
*    # /foo/* /bar/* => param['segments'] will contain all * parsed params
* Note: astrix as first token is not supported yet
* 
* @param string $pattern custom pattern expression
* @param string $ur the url to match against the pattern
* @return mixed parsed array containing associative array of 'name' => 'value for param', 'segments' => array() containg '*' params; false if url doesn't match
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

/**
* Trigger the $uri matching function according to request $method
* 
* @param mixed $uri URI obtained from request
* @param mixed $method REQUEST method
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
					return $ret || true;
				}
			}
			break;
		}
	}
	
	//System failed to find any match
	return NULL;
}

/**
* Trigger on request type 
* 
* @param mixed $method request method(s); can be array or * for all method types
* @param mixed $uri request urls
* @param mixed $func callback function
* @param mixed $agent requesting agent regular expression
*/
function dispatch($method, $uri, $func, $agent = false){
	global $uvicConfig;
	$map = &$uvicConfig['maps'];
    
	// for all methods *
    if($method == '*')
        $method = array('get','post','put','delete');
	
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

/**
* get request register
* 
* @param mixed $uri request url
* @param mixed $func callback function name or function itself
* @param mixed $agent requesting agent regular expression
*/
function get($uri, $func, $agent = false){
	dispatch('get', $uri, $func, $agent);
}

/**
* post request register
* 
* @param mixed $uri request url
* @param mixed $func callback function name or function itself
* @param mixed $agent requesting agent regular expression
*/
function post($uri, $func, $agent = false){
	dispatch('post', $uri, $func, $agent);
}

/**
* put request register
* 
* @param mixed $uri request url
* @param mixed $func callback function name or function itself
* @param mixed $agent requesting agent regular expression
*/
function put($uri, $func, $agent = false){
	dispatch('put', $uri, $func, $agent);
}

/**
* delete request register
* 
* @param mixed $uri request url
* @param mixed $func callback function name or function itself
* @param mixed $agent requesting agent regular expression
*/
function delete($uri, $func, $agent = false){
	dispatch('delete', $uri, $func, $agent);
}

/**
* Render template with $template_name file path and $_tempateData containing associative data
*/
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

/**
* Start benchmark timer for given $name
* 
* @param string $name name of marker to save
*/
function startBenchmark($name){
	global $uvicConfig;
	
	$uvicConfig['benchmark'][$name] = microtime();
}

/**
* Calculate total time consumed for given benchmark $name
* 
* @param string $name name of the marker to calculate total time for
* @return mixed null incase of $name mark not being found; float otherwise containing the total time consumed
*/
function calcBenchmark($name){
	global $uvicConfig;
	
	if( !isset($uvicConfig['benchmark'][$name]) )
		return null;
	
	list($startMic, $startSec) = explode(' ', $uvicConfig['benchmark'][$name]);
	list($endMic, $endSec) = explode(' ', microtime());
	
	return (float)($endMic + $endSec) - (float)($startMic + $startSec);
}

/**
* start engine
*/
function start(){
	$url = ugetURI();
	$ret = utriggerFunction( $url , ugetReqMethod() );
	return $ret;
}

?>
