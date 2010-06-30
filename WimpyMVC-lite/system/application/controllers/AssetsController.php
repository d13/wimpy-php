<?php

class AssetsController extends BaseController {
	
	public function __construct () {
		$this->log = Log::getInstance();
		$this->log->write("HomeController > created");
	}
	public function __destruct () {
		$this->loadView();
		$this->log->write("HomeController > destroyed");
	}
	
	//TODO: Get this to read styles folder, concat the files & minify it. Should be cached.
	public function getCss () {
		$this->req_key = "blank";
		$this->template = "blank";
		$content = "* { padding:0; margin:0; }";
		Model::setLocalValue("content",$content);
		header("Content-type: text/css; charset: UTF-8");
	}
	//TODO: Get this to read scripts folder, concat the files & minify it. Should be cached.
	public function getJs () {
		$this->req_key = "blank";
		$this->template = "blank";
		$content = "function howdy () { alert('howdy'); }";
		Model::setLocalValue("content",$content);
		header("Content-type: text/javascript; charset: UTF-8");
	}
	
	protected function generic (){
		$this->req_key = "blank";
		$this->template = "blank";
		Model::setLocalValue("content","");
	}
}