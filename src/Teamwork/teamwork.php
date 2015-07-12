<?php

namespace Teamwork;

class Teamwork
{

    /**
     *
     * @param string $endpoint
     * @return mixed
     */
    public function get($endpoint = 'projects')
    {
        $timeout = 5;
        $url = 'projects.murrion.com/' . $endpoint . '.json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, 'APIKEY:x');
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
     * Return an array of cache project IDs, if any
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

    public function determine_difference($array1, $array2)
    {
        $difference = array_diff($array1, $array2);

        return $difference;
    }
}