<?php

class PageService extends BaseService {
	public function getHeader($pageName) {
		Model::setLocalValue("company","Wimpy MVC, Inc");
		Model::setLocalValue("name","Wimpy MVC Application");
		Model::setLocalValue("title","Welcome | Wimpy MVC Application");
		Model::setLocalValue("description","");
		Model::setLocalValue("keywords","");
	}
}