<?php

namespace Common\Libraries\Jira;

class Jira
{

    protected $username;

    protected $token;

    protected $timeout = 5;

    protected $endpoint;

    public function __construct()
    {
        $this->username = getenv('JIRA_USER');
        $this->token    = getenv('JIRA_TOKEN');
        $this->url      = getenv('JIRA_URL');
    }

    protected function login()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->url.$this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->token);

        return $ch;
    }

    protected function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function issues($options = [])
    {
        $endpoint = 'search?jql=dueDate='.$options['dueDate'].'+and+assignee='.$options['valueUser'].'+and+resolution=Unresolved&fields=key,status&maxResults=40';
        $this->setEndpoint($endpoint);

        $ch = $this->login();

        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        return $this->exec($ch);
    }

    protected function exec($ch)
    {
        $res     = curl_exec($ch);
        $chError = curl_error($ch);

        if ($chError) {
            $result['error'] = "cURL Error: $chError";
        } else {
            $result['res']  = $res;
            $result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);
        return $result;
    }

    public function updateIssueTransition($params)
    {
        $this->setEndpoint('issue/'.$params['issue_key'].'/transitions?expand=transitions.fields.update');
        $ch = $this->login();
        $data = $this->formatIssueTransition($params);

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        return $this->exec($ch, $data);
    }

    private function formatIssueTransition($params)
    {
        return [
            'update' => [
                'comment' => [[
                    'add' => [
                        "body" => $params['comment'],
                    ]
                ]],
            ],
            'transition' => [
                "id" => $params['transition_id'],
            ]
        ];
    }
}
