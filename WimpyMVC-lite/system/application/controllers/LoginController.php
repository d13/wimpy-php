<?php

class LoginController extends BaseController {
	
	public function __construct () {
		$this->log = Log::getInstance();
		$this->log->write("AdminController > created");
	}
	public function __destruct () {
		// is req_key is not empty
		if(!empty($this->req_key)) {
			$this->loadView();
		}else {
			$this->log->write("LoginController > no view");
		}
		$this->log->write("LoginController > destroyed");
	}
	
	public function authenticate () {
		$user = Request::$get["login_username"];
		$pass = Request::$get["login_password"]; 
		$actionKey = Request::$get["action_key"]; 
		if (!empty($user) && !empty($pass)){
			Model::setGlobalValue("logged_in",TRUE);
			session_regenerate_id();
			Dispatcher::process($actionKey);
		} else {
			Model::setGlobalValue("logged_in",FALSE);
			Dispatcher::process("login");
		}
	}
	
	protected function generic (){
		$this->req_key = "login";
		Model::setLocalValue("view-class","login");
		$pageService = ServiceManager::getService("page");
		$pageService->getHeader($this->req_key);
	}
}