<?php

class HomeController extends BaseController {
	
	public function __construct () {
		$this->log = Log::getInstance();
		$this->log->write("HomeController > created");
	}
	public function __destruct () {
		$this->loadView();
		$this->log->write("HomeController > destroyed");
	}
	
	protected function generic (){
		$this->req_key = "home";
		Model::setLocalValue("view-class","home");
		$pageService = ServiceManager::getService("page");
		$pageService->getHeader($this->req_key);
	}
}