<?php
/**
 * 用户的相关操作方法
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 19:24
 */

require "class/Response.php";
require "class/ResponseSuccess.php";
require "class/ResponseError.php";
require "class/ResponseLogin.php";
require "common.php";
require "class/MSql.php";

switch ($_REQUEST['type']){
    case 'insert':
        if (isset($_POST['invitation']) && $_POST['invitation'] == INVITATION){
            addNewUser($_POST['email'], $_POST['password'], $_POST['username']);
        } else {
            $response = new ResponseError('邀请码不正确');
            echo $response->toJson();
        }
        break;
    case 'login':
        login($_POST['email'],$_POST['password']);
        break;
    case 'update':
        if (checkLogin($_COOKIE['diaryEmail'],$_COOKIE['diaryToken'])){
            updatePassword($_COOKIE['diaryEmail'],$_POST['oldPassword'],$_POST['newPassword']);
        } else {
            $response = new ResponseError('请先登录');
            $response->setLogined(false);
            echo $response->toJson();
        }
        break;
    default:
        $response = new ResponseError('请求参数错误');
        echo $response->toJson();
}



// 查询注册用户是否存在
function addNewUser($email, $password, $username){
    $con = new dsqli();
    $result = $con->query(MSql::QueryEmailExitance($email));
    $response = '';

    if ($result){
        if ($result->num_rows === 0){ // 如果用户不存在，新建用户
        $result = $con->query(MSql::InsetNewUser($email, password_hash($password,PASSWORD_DEFAULT), $username));
            if ($result) {
                $response = new ResponseSuccess('注册成功');
            } else {
                $response = new ResponseError('注册失败');
            }
        } else { // 如果用户已存在，返回结果
            $response =  new ResponseError('用户已存在');
        }
    } else {
        $response = new ResponseError('系统错误');
    }
    echo $response->toJson();
    $con->close();
}

// 修改密码
function updatePassword($email, $oldPassword, $newPassword){
    $con = new dsqli();
    $result = $con->query(MSql::QueryUserPassword($email));
    $response = '';
    if ($email == "test@163.com") {
        $response = new ResponseError('体验账户密码不可修改');
    } else if ($result){
        $response = '';
        if ($result->num_rows !== 0){ // 存在用户
            $row = $result->fetch_array();
            if (password_verify($oldPassword, $row['password'])){
                if ($con->query(MSql::UpdateUserPassword($email, password_hash($newPassword, PASSWORD_DEFAULT))) === true) {
                    $response = new ResponseSuccess('密码修改成功');
                } else {
                    $response = new ResponseError('修改密码失败');
                }
            } else {
                $response = new ResponseError('原密码不正确');
            }
        } else { // 查无此用户 查询失败
            $response =  new ResponseError('用户不存在');
        }
    } else {
        $response = new ResponseError('系统错误');
    }
    echo $response->toJson();
    $con->close();
}


//登录
function login($email, $password)
{
    $con = new dsqli();
    $result = $con->query(MSql::QueryUserPassword($email));
    $response = '';

    if ($result) {
        if ($result->num_rows !== 0) { // 存在用户
            $row = $result->fetch_array();
            if (password_verify($password, $row['password'])) {
                $response = new ResponseLogin();
                $response->setEmail($row['email']);
                $response->setToken($row['password']);
                $response->setUsername($row['username']);
                $response->setUid($row['uid']);
            } else {
                $response = new ResponseError('密码不正确');
            }
        } else { // 查无此用户 查询失败
            $response = new ResponseError('用户不存在');
        }
    } else {
        $response = new ResponseError('系统错误');
    }

    echo $response->toJson();
    $con->close();
}
