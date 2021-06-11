<?php

namespace App\Service;

use App\Entity\Submission;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Judge
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function submit(Submission $submission)
    {
        $data = [
            'source_code' => $submission->getCode(),
            'language_id' => $submission->getLanguage(),
            'cpu_time_limit' => 1
        ];
        $response = $this->client->request('POST', 'http://localhost/submissions', [
            'headers' => [
                'X-Auth-Token' => 'bonjour'
            ],
            'json' => $data
        ]);

        return json_decode($response->getContent())->token;
    }

    public function getSubmission($token)
    {
        $response = $this->client->request('GET', 'http://localhost/submissions/' . $token, [
            'headers' => [
                'X-Auth-Token' => 'bonjour'
            ],
            'query' => [
                'base64_encoded' => 'true'
            ]
        ]);
//        return $response;
        return json_decode($response->getContent());
    }
}