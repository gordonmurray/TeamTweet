<?php

require 'vendor/autoload.php';

use Teamwork\Teamwork;

$teamwork = new Teamwork();

$since_timestamp = strtotime('last monday');

$referral_link = 'http://murr.ie/9dD';

$messages_array = array();

$existing_project_ids = $teamwork->read_projects_cache();

$projects = $teamwork->get('projects');

$latest_project_ids = $teamwork->parse_project_ids($projects);

$teamwork->save_projects_cache($latest_project_ids);

$new_project_ids = $teamwork->determine_difference($latest_project_ids, $existing_project_ids);

$completed_project_ids = $teamwork->determine_difference($existing_project_ids, $latest_project_ids);

$completed_task_ids = $teamwork->compile_tasks($since_timestamp, 'completed', 'completed_on');

$new_task_ids = $teamwork->compile_tasks($since_timestamp, null, 'created-on');


$messages_array[] = count($new_project_ids) > 0 ? count($new_project_ids) . " new projects" : '';

$messages_array[] = count($completed_project_ids) > 0 ? count($completed_project_ids) . " completed projects" : '';

$messages_array[] = count($new_task_ids) > 0 ? count($new_task_ids) . " new tasks" : '';

$messages_array[] = count($completed_task_ids) > 0 ? count($completed_task_ids) . " completed tasks" : '';

$messages_array = array_filter($messages_array);

if (!empty($messages_array)) {
    $tweet = implode(", ", $messages_array);
}

echo $tweet . ' this week at Murrion. All organised using @teamwork ' . $referral_link . "<br />\n";


echo strlen($tweet);

