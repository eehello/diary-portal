<?php

class ResponseError extends Response
{
    var $success = false;
    var $logined = true;
    var $info   = '';

    public function isLogined(): bool
    {
        return $this->logined;
    }

    public function setLogined(bool $logined): void
    {
        $this->logined = $logined;
    }

    public function __construct(string $info = '请求失败')
    {
        parent::__construct($info);
    }
}