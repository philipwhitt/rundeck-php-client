[![Build Status](https://drone.io/github.com/philipwhitt/rundeck-php-client/status.png)](https://drone.io/github.com/philipwhitt/rundeck-php-client/latest)

# Rundeck PHP Client
PHP Client for Rundeck HTTP Api version 10 and 11. Not all features are supported by API version 10.

Version 10 (Rundeck v2.0.+) Docs:
http://rundeck.org/2.0.1/api/index.html

Version 11 (Rundeck v2.1.+) Docs:
http://rundeck.org/2.1.1/api/index.html

###Install With Composer
```
{
	"require" : {
		"rundeck/rundeck-client" : "1.*"
	}
}
```

###Examples
```php
<?php
use Rundeck\RundeckClient;

// Init client, user/pass is optional
$rd = new RundeckClient('http://localhost:4440', 'admin', 'admin');

// returns array of Rundeck\Api\Project
$projects = $rd->getProjects();

// returns array of Rundeck\Api\Job
$jobs = $rd->getJobs($projects[0]->getName()); // By Project Name

// backup/export project jobs
foreach ($projects as $project) {
	@mkdir(__DIR__.'/xml/'.$project);

	$xml = $rd->exportJobs($project);

	file_put_contents('/path-to-jobs/'.$project.'/jobs.xml', $xml);
}

// create project and import jobs (requires rundeck v2.1.+)
$projectName = 'Hello World';
$rd->deleteProject($projectName);
$rd->createProject($projectName);
$rd->importJobs($projectName, file_get_contents('/path-to-jobs/jobs.xml'));

```

###Todo
Rundeck API is fairly robust - only a few features are available via this client. Feel free to put in a pull request to finish out the missing features.