<?php

$dir = dirname(dirname(dirname(dirname(__FILE__))));
require $dir.'/bootstrap/config.php';

use Carbon\Carbon;
use Common\Libraries\Jira\Jira;

$config = new \Common\Libraries\Config\Config;
$tasksConfig = $config->load('Tasks/Jira/UpdateTasks/config');

$date = Carbon::now();
$date->addDays($tasksConfig['issue_days']['days']);
$dueDate = $date->format('Y-m-d');

$jira = new Jira;

foreach ($tasksConfig['users'] as $keyUser => $valueUser) {
    $log->addInfo('Searching tasks for user '. $valueUser .' on '. $date->format('Y-M-d'));

    $options['dueDate']   = $dueDate;
    $options['valueUser'] = $valueUser;
    $result = $jira->issues($options);
    $tasks = json_decode($result['res']);

    $log->addInfo('Detected '.$tasks->total.' tasks for user '. $valueUser);

    foreach ($tasks->issues as $key => $value) {
        try {
            $params['issue_key']     = $value->key;
            $key = strstr($value->key, '-', true);
            $params['transition_id'] = $tasksConfig['project_transition_id'][$key];
            $params['comment']       = '';
            if ($value->fields->status->id != $tasksConfig['avoid_status']['default']) {
                $jira->updateIssueTransition($params);
                $log->addInfo('For task '. $value->key .' Updated to transition ID '. $tasksConfig['project_transition_id'][$key]);
            }
        } catch (\Exception $e) {
            $log->addError($e->getMessage());
        }
    }
}
