<?php

require __DIR__ . '/../vendor/autoload.php';

use teamwork\teamwork;

class teamworkTest extends PHPUnit_Framework_TestCase
{

    public function testProjectsParseIds()
    {
        $teamwork = new teamwork();

        $mock_projects_array = array(
            'projects' => array(
                array(
                    'name' => 'sample project 1',
                    'id' => 5
                ),
                array(
                    'name' => 'sample project 2',
                    'id' => 6
                ),
            ),
        );

        $project_ids = $teamwork->projects_parse_ids($mock_projects_array);

        $this->assertContains(5, $project_ids);
        $this->assertContains(6, $project_ids);

    }

    public function testTweetCompileText()
    {
        $teamwork = new teamwork();

        $new_project_ids = [5, 6, 7];
        $completed_project_ids = [1, 2, 3];
        $new_task_ids = [45, 46, 47];
        $completed_task_ids = [23, 24, 25];
        $referral_link = 'http://murr.ie/9dD';

        $tweet = $teamwork->tweet_compile_text($new_project_ids, $completed_project_ids, $new_task_ids, $completed_task_ids, $referral_link);

        $this->assertEquals($tweet, '3 new projects, 3 completed projects, 3 new tasks, 3 completed tasks this week at Murrion. Organised using @teamwork http://murr.ie/9dD');
    }

    public function testProjectsSaveCache()
    {
        $teamwork = new teamwork();

        $project_ids = [1, 2, 3];
        $file_name = 'testfile';

        $teamwork->projects_save_cache($project_ids, $file_name);
    }

    public function testProjectsReadCache()
    {
        $teamwork = new teamwork();

        $project_ids = [1, 2, 3];
        $file_name = 'testfile';

        $teamwork->projects_save_cache($project_ids, $file_name);

        $existing_project_ids = $teamwork->projects_read_cache($file_name);

        $this->assertContains(1, $existing_project_ids);
        $this->assertContains(2, $existing_project_ids);
        $this->assertContains(3, $existing_project_ids);
    }

    public function testTasksFilter()
    {
        $teamwork = new teamwork();

        $mock_tasks_array = array(
            'todo-items' => array(
                array(
                    'name' => 'sample task 1',
                    'id' => 5,
                    'created-on' => date("Y-m-d h:i:s", strtotime('-90 minutes'))
                ),
                array(
                    'name' => 'sample task 2',
                    'id' => 6,
                    'created-on' => date("Y-m-d h:i:s", strtotime('-40 minutes'))
                ),
                array(
                    'name' => 'sample task 3',
                    'id' => 7,
                    'created-on' => date("Y-m-d h:i:s", strtotime('-30 minutes'))
                ),
            ),
        );

        $since_timestamp = strtotime('-1 hour');

        $new_task_ids = $teamwork->tasks_filter($mock_tasks_array, $since_timestamp, 'created-on');

        $this->assertNotContains(5, $new_task_ids);
        $this->assertContains(6, $new_task_ids);
        $this->assertContains(7, $new_task_ids);
    }
}
