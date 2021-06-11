<?php

namespace App\Service;

use App\Entity\Submission;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Judge
{
    private $client;
    private $url;
    private $key;
    private $host;

    public function __construct(HttpClientInterface $client, $url, $key, $host)
    {
        $this->client = $client;
        $this->url = $url;
        $this->key = $key;
        $this->host = $host;
    }

    public function submit(Submission $submission)
    {
        $data = [
            'source_code' => $submission->getCode(),
            'language_id' => $submission->getLanguage(),
            'cpu_time_limit' => 1
        ];
        $response = $this->client->request('POST', $this->url . '/submissions', [
            'headers' => [
                'x-rapidapi-key' => $this->key,
                "x-rapidapi-host" => $this->host,
            ],
            'json' => $data
        ]);

        return json_decode($response->getContent())->token;
    }

    public function getSubmission($token)
    {
        $response = $this->client->request('GET', $this->url . '/submissions/' . $token, [
            'headers' => [
                'x-rapidapi-key' => $this->key,
                "x-rapidapi-host" => $this->host,
            ],
            'query' => [
                'base64_encoded' => 'true'
            ]
        ]);
//        return $response;
        return json_decode($response->getContent());
    }
}