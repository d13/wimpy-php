<?php

class BaseEntity {
	protected $log;
	private $id = 0;
	
	public function __construct() {
		$this->log = Log::getInstance();
	}	
	// GETTERS
	public function getId () {
		return $this->id;
	}
	
	// SETTERS
	public function setId ($id) {
		$this->id = $id;
	}
}
