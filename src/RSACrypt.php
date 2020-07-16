<?php

declare(strict_types=1);

namespace axios\tools;

class RSACrypt
{
    private $private_key;
    private $public_key;
    private $max_length;

    public function __construct($max_length = 117)
    {
        $this->max_length = $max_length;
    }

    /**
     * get or set private key.
     *
     * @param null $private_key
     *
     * @return mixed
     */
    public function privateKey($private_key = null)
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
    public function publicKey($public_key = null)
    {
        if (null !== $public_key) {
            $this->public_key = $public_key;
        }

        return $this->public_key;
    }

    /**
     * create a pair of private&public key.
     *
     * @return $this
     */
    public function create(): self
    {
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $pri_key);
        $this->privateKey($pri_key);
        $res = openssl_pkey_get_details($res);
        $this->publicKey($res['key']);

        return $this;
    }

    /**
     * @param $data
     *
     * @throws \ErrorException
     */
    public function encryptByPrivateKey(string $data): string
    {
        return $this->encrypt($data, 'private');
    }

    /**
     * @throws \ErrorException
     */
    public function encryptByPublicKey(string $data): string
    {
        return $this->encrypt($data, 'public');
    }

    /**
     * @throws \ErrorException
     */
    public function decryptByPrivateKey(string $data): string
    {
        return $this->decrypt($data, 'private');
    }

    /**
     * @throws \ErrorException
     */
    public function decryptByPublicKey(string $data): string
    {
        return $this->decrypt($data, 'public');
    }

    /**
     * @param        $data
     * @param string $type
     *
     * @throws \ErrorException
     *
     * @return string
     */
    private function encrypt($data, $type = 'private')
    {
        $str   = '';
        $count = 0;
        for ($i = 0; $i < \strlen($data); $i += $this->max_length) {
            $src    = substr($data, $i, 117);
            $result = 'private' === $type ?
                @openssl_private_encrypt($src, $out, $this->private_key) :
                @openssl_public_encrypt($src, $out, $this->public_key);
            if (false === $result) {
                throw new \ErrorException('Failed encrypt by ' . $type . ' key. string : ' . $src);
            }
            $str .= 0 == $count ? base64_encode($result) : ',' . base64_encode($result);
            ++$count;
        }

        return $str;
    }

    /**
     * @param        $data
     * @param string $type
     *
     * @throws \ErrorException
     *
     * @return string
     */
    private function decrypt($data, $type = 'private')
    {
        $str = '';
        if (strpos($data, ',')) {
            $dataArray = explode(',', $data);
            foreach ($dataArray as $src) {
                $result = 'private' === $type ?
                    @openssl_private_encrypt(base64_decode($src), $out, $this->privateKey()) :
                    @openssl_public_decrypt(base64_decode($src), $out, $this->publicKey());
                if (false === $result) {
                    throw new \ErrorException('Failed decrypt by ' . $type . ' key. string : ' . $src);
                }
                $str .= $out;
            }
        } else {
            $src    = base64_decode($data);
            $result = 'private' === $type ?
                @openssl_private_encrypt(base64_decode($src), $out, $this->privateKey()) :
                @openssl_public_decrypt(base64_decode($src), $out, $this->publicKey());
            if (false === $result) {
                throw new \ErrorException('Failed decrypt by ' . $type . ' key. string : ' . $src);
            }
            $str .= $out;
        }

        return $str;
    }
}
