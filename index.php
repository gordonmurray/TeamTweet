<?php

require 'vendor/autoload.php';

use Teamwork\Teamwork;

$teamwork = new Teamwork();

/*
 * projects
 */

$existing_project_ids = $teamwork->read_projects_cache();

$projects = $teamwork->get('projects');

$latest_project_ids = $teamwork->parse_project_ids($projects);

$teamwork->save_projects_cache($latest_project_ids);

$new_project_ids = $teamwork->determine_difference($latest_project_ids, $existing_project_ids);

$archived_project_ids = $teamwork->determine_difference($existing_project_ids, $latest_project_ids);

echo count($new_project_ids) . " new projects. ";

echo count($archived_project_ids) . " completed projects. ";


/*
 * tasks
 */

// get a list of all tasks?

// compare to a list of existing local tasks

// determine number of closed tasks?

// determine number of created tasks?


echo 'done';