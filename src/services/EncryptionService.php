<?php

namespace App\Service;

class EncryptionService
{
    private $method;
    private $key;
    private $iv;

    public function __construct()
    {
        $this->method = 'aes-256-cbc';
        $this->key = 'your-secret-key'; // Make sure to replace this with your actual secret key
        $this->iv = substr(hash('sha256', 'your-secret-iv'), 0, 16); // Replace 'your-secret-iv' with your actual secret IV
    }

    public function encrypt($data)
    {
        return openssl_encrypt($data, $this->method, $this->key, 0, $this->iv);
    }

    public function decrypt($data)
    {
        return openssl_decrypt($data, $this->method, $this->key, 0, $this->iv);
    }
}