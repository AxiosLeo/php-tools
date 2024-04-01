<?php

declare(strict_types=1);

namespace axios\tools;

class RSACrypt
{
    public array $config            = [
        'digest_alg'       => 'sha256',
        'private_key_bits' => 2048,
        'private_key_type' => \OPENSSL_KEYTYPE_RSA,
    ];
    private string $private_key     = '';
    private string $public_key      = '';
    private int $encrypt_block_size = 200;
    private int $decrypt_block_size = 256;

    public function __construct($config = [])
    {
        foreach ($config as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * get or set private key.
     *
     * @param null $private_key
     */
    public function privateKey($private_key = null): string
    {
        if (null !== $private_key) {
            $this->private_key = $private_key;
        }

        return $this->private_key;
    }

    /**
     * get or set public key.
     *
     * @param null $public_key
     *
     * @return mixed
     */
    public function publicKey($public_key = null): string
    {
        if (null !== $public_key) {
            $this->public_key = $public_key;
        }

        return $this->public_key;
    }

    /**
     * create a pair of private&public key.
     *
     * @param array $config config for openssl_pkey_new() method, see detail on
     *                      https://www.php.net/manual/en/function.openssl-pkey-new.php
     *
     * @return $this
     */
    public function create(array $config = []): self
    {
        $config = array_merge($this->config, $config);
        $res    = openssl_pkey_new($config);
        openssl_pkey_export($res, $pri_key);
        $this->privateKey($pri_key);
        $res    = openssl_pkey_get_details($res);
        $this->publicKey($res['key']);

        return $this;
    }

    public function encryptByPrivateKey(string $data): string
    {
        return $this->encrypt($data);
    }

    public function encryptByPublicKey(string $data): string
    {
        return $this->encrypt($data, 'public');
    }

    public function decryptByPrivateKey(string $data): string
    {
        return $this->decrypt($data, 'private');
    }

    public function decryptByPublicKey(string $data): string
    {
        return $this->decrypt($data, 'public');
    }

    private function encrypt(string $data, string $type = 'private'): string
    {
        $str  = '';
        $data = str_split($data, $this->encrypt_block_size);
        foreach ($data as $chunk) {
            $partial = '';
            'private' === $type ?
                @openssl_private_encrypt($chunk, $partial, $this->private_key) :
                @openssl_public_encrypt($chunk, $partial, $this->public_key);
            $str .= $partial;
        }

        return base64_encode($str);
    }

    private function decrypt(string $data, string $type): string
    {
        $str  = '';
        $data = str_split(base64_decode($data), $this->decrypt_block_size);
        foreach ($data as $chunk) {
            $partial = '';
            'private' === $type ?
                openssl_private_decrypt($chunk, $partial, $this->private_key, \OPENSSL_PKCS1_PADDING) :
                openssl_public_decrypt($chunk, $partial, $this->public_key, \OPENSSL_PKCS1_PADDING);
            $str .= $partial;
        }

        return $str;
    }
}
