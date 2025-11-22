<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EncryptionService
{
    public string $key;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->key = $parameterBag->get('encryption_key');
    }

    public function encrypt(string $data): string
    {
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);

        // Generate a random IV
        $iv = random_bytes($ivLength);

        // Encrypt the data
        $encrypted = openssl_encrypt($data, $cipher, $this->key, 0, $iv);

        if ($encrypted === false) {
            $encrypted = '';
        }

        // Combine IV and encrypted data, then base64 encode it
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $data): string
    {
        $cipher = 'aes-256-cbc';

        // Base64 decode the input data
        $decodedData = base64_decode($data);

        // Extract the IV and encrypted data
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($decodedData, 0, $ivLength);
        $ciphertext = substr($decodedData, $ivLength);

        // Decrypt the ciphertext
        $decrypted = openssl_decrypt($ciphertext, $cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            $decrypted = '';
        }

        return $decrypted;
    }
}
