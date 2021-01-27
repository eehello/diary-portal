<?php
/**
 * 通用方法
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 20:11
 */

// 验证是否已登录
function checkLogin($email, $token){
    $con = new dsqli();
    $result = $con->query(MSql::QueryUserPassword($email));
    if ($result) {
        if ($result->num_rows !== 0) { // 存在用户
            $row = $result->fetch_array();
            if ($token === $row['password']) {
                $con->close();
                return true;
            }
        }
    }
    $con->close();
    return false;
}
