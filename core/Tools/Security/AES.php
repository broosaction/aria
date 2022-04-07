<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\Tools\Security;




class AES
{
   protected $key;
   protected $data;
   protected $method;

   protected $options = 0;

    /**
     * AES constructor.
     * @param $data
     * @param null $key
     * @param null $blockSize
     * @param string $mode
     */
    public function __construct($data=null, $key = null, $blockSize = null, $mode = 'CBC')
    {
       $this->setData($data);
       $this->setKey($key);

    }

    /**
     * @param $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    /**
     * @param $blockSize
     * @param string $mode
     * @throws \Exception
     */
    public function setMethod($blockSize, $mode='CBC'): void
    {
        if($blockSize === 192 && in_array('',array('CBC-HMAC-SHA1','CBC-HMAC-SHA256','XTS'))){
            $this->method = null;
            throw new \Exception('Invalid block size and mode compatibility');
        }

        $this->method = 'AES-'.$blockSize.'-'.$mode;
    }

    /**
     * @return bool
     */
    public function validateParams(): bool
    {
        return $this->data !== null && $this->method !== null;
    }

    /**
     * @return string
     */
    protected function getIV(): string
    {
        $iv= random_bytes(openssl_cipher_iv_length($this->method)-8);
        return bin2hex($iv);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function encrypt(): string
    {
        if($this->validateParams()){
            return trim(openssl_encrypt($this->data,$this->method, $this->key, $this->options, $this->getIV()));
        }

        throw new \Exception('Invalide params');
    }

    public function decrypt(): string
    {
        if($this->validateParams()){
            return trim(openssl_decrypt($this->data,$this->method, $this->key, $this->options, $this->getIV()));
        }

        throw new \Exception('Invalide params');
    }
/*
 * usage
 * $inputText = 'security';
 * $key = 'ok';
 * $aes = new AES($inputText, $key, 256);
 * $nt = $aes->encrypt();
 *
 *
 */

}