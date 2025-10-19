<?php
class IPPanelClient
{
    private $apiKey;
    private $baseUrl = 'https://api.ippanel.com/';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function sendPattern($patternCode, $recipient, $parameters)
    {
        $url = $this->baseUrl . 'v1/messages/patterns/send';
        
        $data = [
            'pattern_code' => $patternCode,
            'originator' => '+983000505',
            'recipient' => $recipient,
            'values' => $parameters
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: AccessKey ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    public function getCredit()
    {
        $url = $this->baseUrl . 'v1/credit';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: AccessKey ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        return isset($result['data']['credit']) ? $result['data']['credit'] : 0;
    }
}
