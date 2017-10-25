<?php

namespace Rundeck;

use Rundeck\Api as api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class RundeckClient {

	protected $client;
	protected $jsession;
	protected $majorMinorVersion;

	public function __construct($url, $user, $pass) {
		$this->client = new Client([
			'defaults' => ['verify' => false],
			'base_url' => $url
		]);

		$response = $this->client->post('/j_security_check', [
			'allow_redirects' => false,
			'body' => [
				'j_username' => $user,
				'j_password' => $pass
			]
		]);

		$path = str_replace('JSESSIONID=', '', $response->getHeaders()['Set-Cookie'][0]);
		$this->jsession = str_replace(';Path=/', '', $path);

		$resp = $this->client->get("/api/1/system/info", [
			'cookies' => ['JSESSIONID' => $this->jsession]
		]);

		$version = explode('.', (string)$resp->xml()->system->rundeck->version);

		$this->majorMinorVersion = (int)($version[0] . $version[1]);
	}

	public function getHttpClient() {
		return $this->client;
	}

	public function getProjects() {
		$resp = $this->client->get('/api/1/projects', [
			'cookies' => ['JSESSIONID' => $this->jsession]
		]);

		$data = $this->decodeResponse($resp);

		$projects = $data['projects']['project'];

		return (new api\ProjectApiMapper)->getAllFromEncoded((int)$data['projects']['@attributes']['count'] > 1 ? $projects : [$projects]);
	}

	public function getJobs($projectName) {
		$resp = $this->client->get("/api/1/jobs?project=$projectName", [
			'cookies' => ['JSESSIONID' => $this->jsession]
		]);

		$data = $this->decodeResponse($resp);

		$jobs = $data['jobs']['job'];

		return (new api\JobApiMapper)->getAllFromEncoded((int)$data['jobs']['@attributes']['count'] > 1 ? $jobs : [$jobs]);
	}

	public function createProject($name) {
		if ($this->majorMinorVersion < 21) {
			throw new \Exception('Unsupported feature. Upgrade to Rundeck 2.1.+');
		}

		try {
			$this->client->post('/api/11/projects', [
				'cookies' => ['JSESSIONID' => $this->jsession],
				'json'    => ["name" => $name],
			]);
		} catch (ClientException $e) {
			// ignore, assume it's a 409 and the project already exists
		}
	}

	public function deleteProject($name) {
		if ($this->majorMinorVersion < 21) {
			throw new \Exception('Unsupported feature. Upgrade to Rundeck 2.1.+');
		}

		try {
			$this->client->delete("/api/11/project/$name", [
				'cookies' => ['JSESSIONID' => $this->jsession]
			]);
		} catch (ClientException $e) {
			// ignore, assume it's a 404 and the project already exists
		}
	}

	public function exportJobs($projectName) {
		return $this->client->get("/api/10/jobs/export?project=$projectName", [
			'cookies' => ['JSESSIONID' => $this->jsession]
		])->xml()->asXML();
	}

	public function importJobs($projectName, $xml) {
		try {
			$this->client->post("/api/10/jobs/import", [
				'cookies'      => ['JSESSIONID' => $this->jsession],
				'headers'      => ['Content-Type' => 'application/x-www-form-urlencoded'],
				'body'         => [
					'project'    => $projectName, 
					'uuidOption' => 'preserve',
					'xmlBatch'   => $xml, 
				],
			]);
		} catch (ClientException $e) {
			// ignore, assume it's a 400 and the project already exists
		}
	}

	private function decodeResponse($resp) {
		$xml   = simplexml_load_string($resp->xml()->asXml());
		$json  = json_encode($xml);

		return json_decode($json, true);
	}

}