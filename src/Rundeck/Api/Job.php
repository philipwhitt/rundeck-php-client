<?php

namespace Rundeck\Api;

class Job {

	private $id;
	private $name;
	private $project;
	private $description;

	public function getId() {
		return $this->id;
	}
	public function getName() {
		return $this->name;
	}
	public function getProject() {
		return $this->project;
	}
	public function getDescription() {
		return $this->description;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	public function setProject($project) {
		$this->project = $project;
		return $this;
	}
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

}