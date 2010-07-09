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
			$func = create_function('$value','return "/assets/styles/".$value;');
			$args = array_map($func,$args);
			$content = $this->combineFiles($args);
		} else {
			$content = "/* CCS */";
		}
		
		$this->req_key = "blank";
		$this->template = "blank";
		
		Model::setLocalValue("content",$content);
		header("Content-type: text/css");
	}
	//TODO: Get this to read scripts folder, concat the files & minify it. Should be cached.
	public function getScripts () {
		$args = func_get_args();
		if (count($args) > 0) {
			$func = create_function('$value','return "/assets/scripts/".$value;');
			$args = array_map($func,$args);
			$content = $this->combineFiles($args);
		} else {
			$content = "/* JS */";
		}
		
		$this->req_key = "blank";
		$this->template = "blank";
		
		Model::setLocalValue("content",$content);
		header("Content-type: text/javascript");
	}
	
	protected function generic (){
		$this->req_key = "blank";
		$this->template = "blank";
		Model::setLocalValue("content","/* No Data */");
	}
	
	private function combineFiles ($argArr) {
		$str = "";
		for($i=0; $i < sizeof($argArr); ++$i) {
			$filename = $argArr[$i];
			if (!empty($filename)) {
				$file_w_path = BASE_DIR.$argArr[$i];
				if (file_exists($file_w_path)) {
					$str .= @file_get_contents($file_w_path)."\n";
					$this->log->write("AssetsController > combineFiles -- added: $file_w_path");
				} else {
					$this->log->write("AssetsController > combineFiles -- can't find: $file_w_path");
				}
			} else {
				$this->log->write("AssetsController > combineFiles -- filename missing");
			}
		}
		return $str;
	}
}