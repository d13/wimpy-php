<?php

class AdminController extends BaseController {
	
	public function __construct () {
		$this->log = Log::getInstance();
		$this->log->write("AdminController > created");
	}
	public function __destruct () {
		// is req_key is not empty
		if(!empty($this->req_key)) {
			$this->loadView();
		}else {
			$this->log->write("AdminController > no view");
		}
		$this->log->write("AdminController > destroyed");
	}
	
	public function info () {
		$this->req_key = "admin.info";
		$this->template = "blank";
		Model::setLocalValue("view-class","admin");
	}	
	
	public function watchlog () {
		$this->req_key = "admin.watchlog";
		Model::setLocalValue("view-class","watchlog");
		Model::setLocalValue("title","Admin - Log Watcher");
		$targetDir = LOG_PATH;
		$fileList = array();
		$directory = opendir($targetDir);
		while($name = readdir($directory)) {
			if($name != '.' && $name != '..') {
				$filename = $name;
				$filename = str_replace(".txt", "", $filename);
				$fileList [] = $filename;
			}
		}
		closedir($directory);
		sort($fileList);
		Model::setLocalValue("targetDir",$targetDir);
		Model::setLocalValue("fileList",$fileList);
	}
	public function getlog ($filename) {
		$this->req_key = "admin.log";
		$this->template = "blank";
		$filepath = LOG_PATH.$filename.".txt";
		$content = @file_get_contents($filepath);
		Model::setLocalValue("content",$content);
	}
	public function clean () {
		$site_value = Model::getGlobalValue("site");
		Model::clearAllValues();
		Model::setGlobalValue("site",$site_value);
		Dispatcher::process("home");
	}
	
	
	protected function generic (){
		$isLoggedIn = Model::getGlobalValue("logged_in");
		if(!empty($isLoggedIn) && $isLoggedIn) {
			$this->req_key = "admin";
			Model::setLocalValue("view-class","admin");
		} else {
			Dispatcher::process("login");
		}
	}

}