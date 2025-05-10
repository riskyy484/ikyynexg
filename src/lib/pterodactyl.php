<?php
class Pterodactyl {
    private $apiKey;
    private $clientKey;
    private $domain;

    public function __construct() {
        $this->domain = PTERODACTYL_DOMAIN;
        $this->apiKey = PTERODACTYL_API_KEY;
        $this->clientKey = PTERODACTYL_CLIENT_KEY;
    }

    private function makeRequest($endpoint, $method = 'GET', $data = null, $isClient = false) {
        $url = $this->domain . '/api/' . ($isClient ? 'client' : 'application') . $endpoint;
        $key = $isClient ? $this->clientKey : $this->apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $key
        ]);

        if($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response);
        } else {
            error_log("Pterodactyl API Error: " . $response);
            return false;
        }
    }

    public function getAllServers() {
        return $this->makeRequest('/servers?include=user');
    }

    public function getNodes() {
        return $this->makeRequest('/nodes');
    }

    public function getNodeUtilization($nodeId) {
        return $this->makeRequest('/nodes/' . $nodeId . '/utilization');
    }

    public function getUserServers($userId) {
        return $this->makeRequest('/servers?filter[user_id]=' . $userId);
    }

    public function getServerStatus($serverId) {
        return $this->makeRequest('/servers/' . $serverId . '/resources', 'GET', null, true);
    }

    public function sendPowerCommand($serverId, $command) {
        return $this->makeRequest('/servers/' . $serverId . '/power', 'POST', ['signal' => $command], true);
    }

    public function createUser($data) {
        return $this->makeRequest('/users', 'POST', [
            'username' => $data['username'],
            'email' => $data['email'],
            'first_name' => $data['first_name'] ?? 'User',
            'last_name' => $data['last_name'] ?? 'Panel',
            'password' => $data['password']
        ]);
    }
}