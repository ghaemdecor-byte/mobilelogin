<?php
class SMSIrClient
{
    private $apiKey;
    private $secretKey;
    private $lineNumber;
    private $baseUrl = 'https://api.sms.ir/';

    public function __construct($apiKey, $secretKey, $lineNumber)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->lineNumber = $lineNumber;
    }

    public function sendVerify($mobile, $templateId, $parameters)
    {
        $token = $this->getToken();
        
        if (!$token) {
            return false;
        }

        $url = $this->baseUrl . 'v1/send/verify';
        
        $data = [
            'mobile' => $mobile,
            'templateId' => $templateId,
            'parameters' => $parameters
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->apiKey,
            'Authorization: Bearer ' . $token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    private function getToken()
    {
        $url = $this->baseUrl . 'v1/auth/token';
        
        $data = [
            'apiKey' => $this->apiKey,
            'secretKey' => $this->secretKey
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        return isset($result['token']) ? $result['token'] : null;
    }
}
