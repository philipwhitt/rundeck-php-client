<?php

namespace Rundeck\Api;

class JobApiMapper {

	public function getFromEncoded(array $params) {
		return (new Job())
			->setId((string)$params['@attributes']['id'])
			->setName((string)$params['name'])
			->setProject((string)$params['project'])
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

// Rundeck API response for job
// 
// {
//   "name" : "Some Name"
//   "project" : "test"
//   "description" : "Lorem Ipsum"
// }