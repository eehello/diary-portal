<?php
class ResponseLogin extends Response
{
    var $success = true;
    var $info = '';
    var $token = '';
    var $email = '';
    var $uid = '';
    var $username='';

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function __construct(string $info = '登录成功')
    {
        parent::__construct($info);
        logLastLoginTime($_COOKIE['diaryEmail']); // 所有成功都记录最后请求时间
    }
    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}