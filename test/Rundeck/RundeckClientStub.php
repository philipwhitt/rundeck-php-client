<?php

namespace Rundeck;

use GuzzleHttp\Client;

class RundeckClientStub extends RundeckClient {

	public function __construct($url, $user, $pass, $version) {
		$this->client = new Client([
			'base_url' => $url
		]);

		$this->majorMinorVersion = (int)($version[0] . $version[1]);
	}

}