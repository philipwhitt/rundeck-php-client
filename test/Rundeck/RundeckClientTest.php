<?php

namespace Rundeck;

use Rundeck\Api as api;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class RundeckClientTest extends \PHPUnit_Framework_TestCase {

	public function setup() {
		$this->client = new RundeckClientStub('http://localhost:4440', 'admin', 'admin', '2.6.1');
	}

	public function testGetProjects() {
		// given
		$body = Stream::factory(file_get_contents(__DIR__.'/projects.xml'));

		$mock = new Mock([new Response(200, [], $body)]);
		$this->client->getHttpClient()->getEmitter()->attach($mock);

		// when
		$projects = $this->client->getProjects();

		// then
		$this->assertEquals(count($projects), 2);

		$this->assertEquals($projects[0], (new api\Project)
			->setName("Project 1")
			->setDescription("Lorem Ipsum")
		);

		$this->assertEquals($projects[1], (new api\Project)
			->setName("Project 2")
			->setDescription("Lorem Ipsum 2")
		);
	}

	public function testGetJobs() {
		// given
		$body = Stream::factory(file_get_contents(__DIR__.'/jobs.xml'));

		$mock = new Mock([new Response(200, [], $body)]);
		$this->client->getHttpClient()->getEmitter()->attach($mock);

		// when
		$jobs = $this->client->getJobs('test');

		// then
		$this->assertEquals(count($jobs), 2);

		$this->assertEquals($jobs[0], (new api\Job)
			->setId("2342345-ac1d-43b2-b00f-uflkJ234")
			->setName("some-job-name")
			->setProject("test")
			->setDescription("This is a job for some tests")
		);

		$this->assertEquals($jobs[1], (new api\Job)
			->setId("234lkjfou-6c4a-4eff-b31f-234kljsdf9Ijlk")
			->setName("another-job-name")
			->setProject("test")
			->setDescription("Lorem Ipsum")
		);
	}

}
