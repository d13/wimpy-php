<?php

class ErrorController extends BaseController {
	
	public function __construct () {
		$this->log = Log::getInstance();
		$this->log->write("ErrorController > created");
	}
	public function __destruct () {
		$this->loadView();
		$this->log->write("ErrorController > destroyed");
	}
	
	protected function generic (){
		$this->req_key = "error";
		Model::setLocalValue("view-class","error");
		//$pageService = ServiceManager::getService("page");
		//$pageService->getHeader($this->req_key);
	}
}