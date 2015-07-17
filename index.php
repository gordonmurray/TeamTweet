<?php
require 'vendor/autoload.php';

use teamwork\teamwork;

$teamwork = new teamwork();

$since_timestamp = strtotime('last monday');

$referral_link = 'http://murr.ie/9dD';

$messages_array = array();

$existing_project_ids = $teamwork->projects_read_cache();

$projects = $teamwork->get('projects');

$latest_project_ids = $teamwork->projects_parse_ids($projects);

$teamwork->projects_save_cache($latest_project_ids);

$new_project_ids = array_diff($latest_project_ids, $existing_project_ids);

$completed_project_ids = array_diff($existing_project_ids, $latest_project_ids);

$completed_task_ids = $teamwork->tasks_compile($since_timestamp, 'completed', 'completed_on');

$new_task_ids = $teamwork->tasks_compile($since_timestamp, null, 'created-on');

$tweet = $teamwork->tweet_compile_text($new_project_ids, $completed_project_ids, $new_task_ids, $completed_task_ids, $referral_link);

echo $tweet;