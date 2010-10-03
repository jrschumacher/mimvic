<?php

/*

Copyright (c) 2010 Zohaib Sibt-e-Hassan ( MaXPert )

MiMViC Shift v0.9.9

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

class ConventionException extends MiMViCException{
	
}

class Convention implements ActionHandler{
	/**
	* 
	*/
	private static $defConfig = array('defaultAction'=> 'index', 'defaultClass'=> 'Home', 'classPrefix'=> '', 'classPostfix'=>'Controller');
	
	private var $baseDir = NULL;
	private var $conf = NULL;
	
	public static function hook($lookupDir, $baseURL='/', $conf = NULL){
		dispatch('*', $baseURL.'*', self::create($lookupDir, $baseURL, $conf));
	}
	
	public static function create($lookupDir, $baseURL='/', $conf = NULL){
		foreach(self::$defConfig as $k => $v)
			if( !isset($conf[$k]) )
				$conf[$k] = $v;
		$obj = new Convention($lookupDir, $conf);
		return Action('Convention', $obj);
	}
	
	public function __construct($baseDir, $conf){
		$this->baseDir = $baseDir;
		$this->conf = $conf;
	}
	
	public function exec($params){
		$segs = array_pop($params['segments']);
		$cls = $this->conf['classPrefix'].$this->conf['defaultClass'].$this->conf['classPostfix'];
		$act = $this->conf['defaultAction'];
		
		if(count($segs) && $segs[0] === "")
			array_shift($segs);
		
		if(count($segs)){
			$nme = array_shift($segs);
			$nme = str_replace('-', ' ', $nme);
			$nme = ucwords($nme);
			$nme = str_replace(' ', '', $nme);
			$cls = $this->conf['classPrefix'].$nme.$this->conf['classPostfix'];
		}
		
		if(count($segs) && $segs[0] === "")
			array_shift($segs);
		
		if(count($segs))
			$act = array_shift($segs);
		
		if(!file_exists($this->baseDir.'/'.strtolower($cls).'.php'))
			throw new ConventionException("Unable to find file ".$this->baseDir.'/'.strtolower($cls).'.php');
		require_once($this->baseDir.'/'.strtolower($cls).'.php');
		
		$obj = new $cls();
		$tbCalled = array(&$obj, $act);
		
		if( !is_callable($tbCalled) )
			throw new ConventionException("Unable to find specified action ".$cls.'::'.$act.'   ');
		call_user_func_array($tbCalled, $segs);
	}
}

?>