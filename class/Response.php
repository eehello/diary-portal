<?php
/**
 * 返回结果集
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 17:02
 */

class Response
{
    var $success;
    var $info;

    public function __construct(string $info)
    {
        $this->info = $info;
    }

    public function toJson(){
        return json_encode($this);
    }


    public function getSuccess()
    {
        return $this->success;
    }

    public function setSuccess($success): void
    {
        $this->success = $success;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info): void
    {
        $this->info = $info;
    }
}