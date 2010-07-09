<?php

class Dispatcher {
	private static $log;
	public static function initialize (){
		if (empty(self::$log)) {
			self::$log = Log::getInstance();
		}
		self::$log->write("REQUEST FROM URL ****************************************** ",1);
		
		// Capture request data
		Request::loadData(); // TODO: SANITIZE STRINGS
		
		// SET REQUEST KEY
		if(!empty(Request::$get["page"])) {
			$req_key = Request::$get["page"];
			self::$log->write("Dispatcher > initialize :: map: $req_key");
		} else {
			$req_key = DEFAULT_KEY; // Open domain
			self::$log->write("Dispatcher > initialize :: default map: $req_key");
		}
		
		// SET ACTION NAME
		if(!empty(Request::$get["action"])) {
			$req_action = Request::$get["action"];
			self::$log->write("Dispatcher > initialize :: action: $req_action");
		} else {
			$req_action = NULL;
			self::$log->write("Dispatcher > initialize :: default action: NONE SPECIFIED");
		}
		
		// SET PARAM
		if(!empty(Request::$get["param"])) {
			$req_param = Request::$get["param"];
			self::$log->write("Dispatcher > initialize :: param: $req_param");
		} else {
			$req_param = NULL;
			self::$log->write("Dispatcher > initialize :: param: NONE");
		}
		
		self::process($req_key,$req_action,$req_param);
		exit();
	}
	public static function process($req_key,$req_action=NULL,$req_param=NULL) {
		$result = self::load($req_key,$req_action,$req_param);
		if (!empty($result) && strlen($result) > 0) {
			echo $result;
		}
		Model::clearLocalValues();
	}
	public static function load($req_key,$req_action=NULL,$req_param=NULL) {
		self::$log->write("Dispatcher > load :: args: $req_key - $req_action - $req_param",1);
		
		self::$log->write("Dispatcher > load :: request method is ".$_SERVER['REQUEST_METHOD'],1);
		if(CACHE_ENABLED && ($_SERVER['REQUEST_METHOD'] == "GET")) {
			self::$log->write("Dispatcher > load :: caching is on",1);
			$result = self::loadFromCache($req_key,$req_action,$req_param);
		} else {
			self::$log->write("Dispatcher > load :: caching is off",1);
		}
		if (empty($result) ||  strlen($result) < 1) { 
			self::$log->write("Dispatcher > load :: loading from controller",1);
			$result = self::loadFromController($req_key,$req_action,$req_param);
		}
		CacheHelper::setBuffer("");
		return $result;
	}
	private static function loadFromCache ($req_key,$req_action,$req_param) {
		$cache_file = CacheHelper::makeFileNameFromUrl($req_key, $req_action, $req_param);
		$cache_file_path = CACHE_PATH.'/'.$cache_file;
		self::$log->write("Dispatcher > loadFromCache :: filename: $cache_file");
		self::$log->write("Dispatcher > loadFromCache :: filename w path: $cache_file_path");
		$time = time();
		$file_exists = file_exists($cache_file_path);
		if ($file_exists) {
			$time_diff = $time - filemtime($cache_file_path);
			$time_diff_ok = ($time_diff < CACHE_LIMIT) ? TRUE : FALSE;
			self::$log->write("Time diff of $cache_file is: $time_diff");
		} else {
			$time_diff_ok = FALSE;
		}
		if ($file_exists && $time_diff_ok) {
			self::$log->write("Retrieving contents of $cache_file");
			$content = @file_get_contents($cache_file_path);
			return $content;
		} else {
			if (!$file_exists) {
				self::$log->write("Dispatcher > loadFromCache :: Cache file not found");
			} else if ($file_exists && $time_diff_ok) {
				self::$log->write("Dispatcher > loadFromCache :: Cache is too old");
			}
			return NULL;
		}
	}
	private static function loadFromController ($req_key,$req_action,$req_param) {
		if (empty(self::$log)) {
			self::$log = Log::getInstance();
		}
		// GET CONTROLLER
		$objArr = Config::getController($req_key);
		$filename = CONTROLLER_PATH.'/'.$objArr[0].'.php';
		if (file_exists($filename)) {
			self::$log->write("Dispatcher > loadController: $objArr[0]");
			require_once($filename);
			$obj = new $objArr[0];
		} else { // No controller found
			self::$log->write("Dispatcher > loadController > Key: {$req_key} does not exist");
			$buffer = self::load("error");
		}
		$buffer = "";
		if (!empty($obj)) {
			$isCachable = $objArr[1];
			if (!empty($req_action)) {
			
				//TODO: Fix actions from underscores to camel-case
				if(strpos($req_action,'_') != FALSE){
					$action = Inflector::toCamelCase($req_action,'_');
				}
				else { $action = $req_action; }
				
				if (method_exists($obj,$action)) { 
					if(!empty($req_param)) { // If params are passed
						self::$log->write("Dispatcher > loadController :: calling {$req_key}->{$action}({$req_param})");
						
						$req_param_list = array();
						$req_param_list = explode("/",$req_param);
						call_user_func_array(array($obj, $action),$req_param_list);
					} else { // Call function with no params
						self::$log->write("Dispatcher > loadController :: calling {$req_key}->{$action}()");
						$obj->$action();
					}
				} else { // If method does not exist 
					self::$log->write("Dispatcher > loadController :: {$req_key}->{$action}() does not exist");
					$buffer = self::load("error");
				}
			}else { // Call default method
				self::$log->write("Dispatcher > loadController :: calling {$req_key}->execute()");
				$obj->execute();
			}
			$obj = null;
			$buffer = CacheHelper::getBuffer();
			if ($isCachable) {
				$params = !empty($req_param_list) ? $req_param_list : NULL;
				CacheHelper::saveView($req_key,$req_action,$params);
			}
		}
		
		return $buffer;
	}
}
