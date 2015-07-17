<?php
require 'vendor/autoload.php';

use teamwork\teamwork;

$settings_array = parse_ini_file("settings.ini");

$teamwork = new teamwork();

$since_timestamp = strtotime('last monday');

$messages_array = array();

$existing_project_ids = $teamwork->projects_read_cache();

$projects = $teamwork->get('projects', null, $settings_array);

$latest_project_ids = $teamwork->projects_parse_ids($projects);

$teamwork->projects_save_cache($latest_project_ids);

$new_project_ids = array_diff($latest_project_ids, $existing_project_ids);

$complete_project_ids = array_diff($existing_project_ids, $latest_project_ids);

$complete_task_ids = $teamwork->tasks_compile($since_timestamp, 'completed', 'completed_on', $settings_array);

$new_task_ids = $teamwork->tasks_compile($since_timestamp, null, 'created-on', $settings_array);

$parameters = array(
    'new_project_ids' => $new_project_ids,
    'complete_project_ids' => $complete_project_ids,
    'new_task_ids' => $new_task_ids,
    'complete_task_ids' => $complete_task_ids
);

$tweet = $teamwork->tweet_compile_text($parameters, $settings_array);

echo $tweet;