<?php

class AssetsController extends BaseController {
	
	public function __construct () {
		$this->log = Log::getInstance();
		$this->log->write("AssetsController > created");
	}
	public function __destruct () {
		$this->loadView();
		$this->log->write("AssetsController > destroyed");
	}
	
	//TODO: Get this to read styles folder, concat the files & minify it. Should be cached.
	public function getStyles () {
		$args = func_get_args();
		if (count($args) > 0) {
			$func = create_function('$value','return $BASE_DIR."/assets/styles/".$value;');
			$args = array_map($func,$args);
			$content = $this->combineFiles($args);
			$this->log->write("AssetsController > getStyles:");
			$this->log->write($content);
		} else {
			$content = "* { padding:0; margin:0; }";
		}
		
		$this->req_key = "blank";
		$this->template = "blank";
		
		Model::setLocalValue("content",$content);
		header("Content-type: text/css; charset: UTF-8");
	}
	//TODO: Get this to read scripts folder, concat the files & minify it. Should be cached.
	public function getScripts () {
		$args = func_get_args();
		if (count($args) > 0) {
			$func = create_function('$value','return $BASE_DIR."/assets/scripts/".$value;');
			$args = array_map($func,$args);
			$content = $this->combineFiles($args);
			$this->log->write("AssetsController > getStyles:");
			$this->log->write($content);
		} else {
			$content = "function howdy () { alert('howdy'); }";
		}
		
		$this->req_key = "blank";
		$this->template = "blank";
		
		Model::setLocalValue("content",$content);
		header("Content-type: text/javascript; charset: UTF-8");
	}
	
	protected function generic (){
		$this->req_key = "blank";
		$this->template = "blank";
		Model::setLocalValue("content","");
	}
	
	private function combineFiles ($argArr) {
		$str = "";
		for($i=0; $i < sizeof($argArr); ++$i) {
			if (file_exists($argArr[i])) {
				$str .= @file_get_contents($argArr[i]);
			}
		}
		return $str;
	}
}