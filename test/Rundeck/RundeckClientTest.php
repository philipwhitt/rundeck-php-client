<?php

namespace Rundeck;

use Rundeck\Api as api;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class RundeckClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RundeckClientStub
     */
    protected $client;

    public function setup()
    {
        $this->client = new RundeckClientStub('http://localhost:4440', 'admin', 'admin', '2.6.1');
    }

    /**
     * @param $file
     * @param $expectedProjects
     * @dataProvider getDataForTestGetProjects
     */
    public function testGetProjects($file, $expectedProjects)
    {
        // given
        $body = Stream::factory(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

        $mock = new Mock([new Response(200, [], $body)]);
        $this->client->getHttpClient()->getEmitter()->attach($mock);

        // when
        /** @var api\Project[] $projects */
        $projects = $this->client->getProjects();

        // then
        $this->assertEquals(count($expectedProjects), count($projects));

        foreach ($projects as $project) {
            $this->assertInstanceOf('Rundeck\\Api\\Project', $project);
            list($key, $expectedProject) = each($expectedProjects);
            $this->assertEquals($expectedProject['name'], $project->getName());
            $this->assertEquals($expectedProject['description'], $project->getDescription());
        }
    }

    /**
     * @param string $file
     * @param string $project
     * @param array $expectedJobs
     * @dataProvider getDataForTestGetJobs
     */
    public function testGetJobs($file, $project, $expectedJobs)
    {
        // given
        $body = Stream::factory(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

        $mock = new Mock([new Response(200, [], $body)]);
        $this->client->getHttpClient()->getEmitter()->attach($mock);

        // when
        /** @var api\Job[] $jobs */
        $jobs = $this->client->getJobs($project);

        // then
        $this->assertEquals(count($expectedJobs), count($jobs));

        foreach ($jobs as $job) {
            $this->assertInstanceOf('Rundeck\\Api\\Job', $job);
            list($key, $expectedJob) = each($expectedJobs);
            $this->assertEquals($expectedJob['id'], $job->getId());
            $this->assertEquals($expectedJob['name'], $job->getName());
            $this->assertEquals($expectedJob['project'], $job->getProject());
            $this->assertEquals($expectedJob['description'], $job->getDescription());
        }
    }

    /**
     * @param string $project
     * @param string $file
     * @dataProvider getDataForTestExportProject
     */
    public function testExportJobs($project, $file)
    {
        // given
        $xmlA = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file);
        $body = Stream::factory($xmlA);

        $mock = new Mock([new Response(200, [], $body)]);
        $this->client->getHttpClient()->getEmitter()->attach($mock);

        // when
        $xmlB = $this->client->exportJobs($project);

        // then
        $this->assertEquals($xmlB, $xmlA);
    }

    /**
     * @return array
     */
    public function getDataForTestGetProjects()
    {
        return [
            [
                'projectsMultiple.xml',
                [
                    [
                        'name' => 'Project 1',
                        'description' => 'Lorem Ipsum',
                    ],
                    [
                        'name' => 'Project 2',
                        'description' => 'Lorem Ipsum 2',
                    ],
                ]
            ],
            [
                'projectsSingle.xml',
                [
                    [
                        'name' => 'Project 1',
                        'description' => 'Lorem Ipsum',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getDataForTestGetJobs()
    {
        return [
            [
                'jobsMultiple.xml',
                'test',
                [
                    [
                        'id' => '2342345-ac1d-43b2-b00f-uflkJ234',
                        'name' => 'some-job-name',
                        'project' => 'test',
                        'description' => 'This is a job for some tests',
                    ],
                    [
                        'id' => '234lkjfou-6c4a-4eff-b31f-234kljsdf9Ijlk',
                        'name' => 'another-job-name',
                        'project' => 'test',
                        'description' => 'Lorem Ipsum',
                    ],
                ]
            ],
            [
                'jobsSingle.xml',
                'test',
                [
                    [
                        'id' => '2342345-ac1d-43b2-b00f-uflkJ234',
                        'name' => 'some-job-name',
                        'project' => 'test',
                        'description' => 'This is a job for some tests',
                    ],
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function getDataForTestExportProject()
    {
        return [
            [
                'test',
                'exportJobs.xml',
            ],
        ];
    }
}
