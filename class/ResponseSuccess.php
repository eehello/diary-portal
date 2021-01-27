<?php
class ResponseSuccess extends Response
{
    var $data;
    var $success = true;
    var $info = '';

    public function __construct(string $info = '请求成功')
    {
        parent::__construct($info);
        logLastLoginTime($_COOKIE['diaryEmail']); // 所有成功都记录最后请求时间
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}

// 记录用户登录时间
function logLastLoginTime($email){
    $con = new dsqli();
    $result = $con->query(MSql::InsertLoginLog($email));
    $con->close();
}