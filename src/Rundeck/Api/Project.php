<?php

namespace Rundeck\Api;

class Project {

	private $name;
	private $description;

	public function getName() {
		return $this->name;
	}
	public function getDescription() {
		return $this->description;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

}