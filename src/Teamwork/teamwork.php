<?php

namespace Teamwork;

class Teamwork
{

    /**
     * Perform a GET to Teamwork's API
     *
     * @param string $endpoint
     * @param array $parameters
     * @return mixed
     */
    public function get($endpoint = 'projects', $parameters = array())
    {
        $timeout = 5;

        $additional_parameters = '?';
        $additional_parameters_array = array();

        foreach ($parameters as $key => $value) {
            $additional_parameters_array[] = "$key=$value";
        }

        $additional_parameters .= implode("&", $additional_parameters_array);

        $url = 'projects.murrion.com/' . $endpoint . '.json' . $additional_parameters;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Basic APIKEY"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
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
    public function parse_project_ids($projects_array)
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
     */
    public function save_projects_cache($project_ids)
    {
        if (is_array($project_ids) && !empty($project_ids)) {
            file_put_contents('cache/project_ids.json', json_encode($project_ids));
        }
    }

    /**
     * Return an array of cached project IDs, if any
     *
     * @return array|mixed
     */
    public function read_projects_cache()
    {
        if (file_exists('cache/project_ids.json')) {
            return json_decode(file_get_contents('cache/project_ids.json'), true);
        } else {
            return array();
        }
    }

    /**
     * Given 2 arrays, return the difference
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    public function determine_difference($array1, $array2)
    {
        $difference = array_diff($array1, $array2);

        return $difference;
    }

    /**
     * Compile some parameters, get the tasks and filter them
     *
     * @param string $since_timestamp
     * @param string $filter
     * @param string $filter_key
     * @return array
     */
    public function compile_tasks($since_timestamp = '', $filter = '', $filter_key = '')
    {
        $parameters = array(
            'pageSize' => 250,
            'sort' => 'dateadded',
            'filter' => $filter
        );

        $tasks_array = $this->get('tasks', $parameters);

        $filtered_tasks = $this->filter_tasks($tasks_array, $since_timestamp, $filter_key);

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
    public function filter_tasks($tasks_array, $since_timestamp, $array_key)
    {
        $filtered_tasks = array();

        foreach ($tasks_array['todo-items'] as $task_details) {
            if (strtotime($task_details[$array_key]) >= $since_timestamp) {
                $filtered_tasks[] = $task_details['id'];
            }
        }

        return $filtered_tasks;
    }
}