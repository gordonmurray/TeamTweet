<?php

namespace teamwork;

class teamwork
{

    /**
     * Perform a GET to Teamwork's API
     *
     * @param string $endpoint
     * @param array $parameters
     * @param $settings_array
     * @return mixed
     */
    public function get($endpoint = 'projects', $parameters = array(), $settings_array)
    {
        $additional_parameters = '?';
        $additional_parameters_array = array();

        if (!empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $additional_parameters_array[] = "$key=$value";
            }
        }

        $additional_parameters .= implode("&", $additional_parameters_array);

        $url = $settings_array['teamwork_url'] . $endpoint . '.json' . $additional_parameters;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Basic " . $settings_array['teamwork_api_key']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    /**
     * Given the full details of several projects, pull out the IDs only
     *
     * @param $projects_array
     * @return array
     */
    public function projects_parse_ids($projects_array)
    {
        $project_ids = array();

        if (isset($projects_array['projects'])) {

            foreach ($projects_array['projects'] as $project) {
                $project_ids[] = isset($project['id']) ? $project['id'] : '';
            }
        }

        return $project_ids;
    }

    /**
     * Save a local cache of Project IDs
     *
     * @param $project_ids
     * @param string $file_name
     */
    public function projects_save_cache($project_ids, $file_name = 'project_ids')
    {
        if (is_array($project_ids) && !empty($project_ids)) {
            file_put_contents('cache/' . $file_name . '.json', json_encode($project_ids));
        }
    }

    /**
     * Return an array of cached project IDs, if any
     *
     * @param string $file_name
     * @return array|mixed
     */
    public function projects_read_cache($file_name = 'project_ids')
    {
        if (file_exists('cache/project_ids.json')) {
            return json_decode(file_get_contents('cache/' . $file_name . '.json'), true);
        } else {
            return array();
        }
    }

    /**
     * Compile some parameters, get the tasks and filter them
     *
     * @param string $since_timestamp
     * @param string $filter
     * @param string $filter_key
     * @param $settings_array
     * @return array
     */
    public function tasks_compile($since_timestamp = '', $filter = '', $filter_key = '', $settings_array)
    {
        $parameters = array(
            'pageSize' => 250,
            'sort' => 'dateadded',
            'filter' => $filter
        );

        $tasks_array = $this->get('tasks', $parameters, $settings_array);

        $filtered_tasks = $this->tasks_filter($tasks_array, $since_timestamp, $filter_key);

        return $filtered_tasks;
    }

    /**
     * Filter an array of tasks to those after a given date
     *
     * @param $tasks_array
     * @param $since_timestamp
     * @param $array_key
     * @return array
     */
    public function tasks_filter($tasks_array, $since_timestamp, $array_key)
    {
        $filtered_tasks = array();

        foreach ($tasks_array['todo-items'] as $task_details) {
            if (strtotime($task_details[$array_key]) >= $since_timestamp) {
                $filtered_tasks[] = $task_details['id'];
            }
        }

        return $filtered_tasks;
    }

    /**
     * Given some project and task arrays, compile some text to use in a Tweet
     *
     * @param $new_project_ids
     * @param $completed_project_ids
     * @param $new_task_ids
     * @param $completed_task_ids
     * @param $settings_array
     * @return string
     */
    public function tweet_compile_text($new_project_ids, $completed_project_ids, $new_task_ids, $completed_task_ids, $settings_array)
    {
        $messages_array[] = count($new_project_ids) > 0 ? count($new_project_ids) . " new projects" : '';

        $messages_array[] = count($completed_project_ids) > 0 ? count($completed_project_ids) . " completed projects" : '';

        $messages_array[] = count($new_task_ids) > 0 ? count($new_task_ids) . " new tasks" : '';

        $messages_array[] = count($completed_task_ids) > 0 ? count($completed_task_ids) . " completed tasks" : '';

        $messages_array = array_filter($messages_array);

        if (!empty($messages_array)) {
            $tweet = implode(", ", $messages_array);
        }

        $tweet .= ' this week at ' . $settings_array['business_name'] . '. Organised using @teamwork ' . $settings_array['teamwork_referral_link'];

        return $tweet;

    }
}