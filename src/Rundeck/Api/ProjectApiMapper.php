<?php

namespace Rundeck\Api;

class ProjectApiMapper {

	public function getFromEncoded(array $params) {
		return (new Project())
			->setName((string)$params['name'])
			->setDescription(is_string($params['description']) ? $params['description'] : '');
	}

	public function getAllFromEncoded(array $encs) {
		$data = [];
		foreach ($encs as $enc) {
			$data[] = $this->getFromEncoded($enc);
		}
		return $data;
	}

}

// Rundeck API response for projects
// 
// {
//   "name" : "Some Name"
//   "description" : "Lorem Ipsum"
// }