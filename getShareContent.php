<?php

/**
 *
 */

require "class/Response.php";
require "class/ResponseSuccess.php";
require "class/ResponseError.php";
require "class/ResponseLogin.php";
require "class/MSql.php";
require "common.php";


queryDiary($_GET['diaryId']);


//查询日记内容
function queryDiary($id)
{
    $con = new dsqli();
    $con->set_charset('utf8');
    $response = '';
    $result = $con->query(MSql::QuerySharedDiaries($id));
    if ($result) {
        $response = new ResponseSuccess();
        $diary = $result->fetch_object(); // 参数1会把字段名也读取出来
        // 处理数据，把带 emoji 表情的数据解析出来
        if ($diary){
            $diary -> title = unicodeDecode($diary -> title);
            $diary -> content = unicodeDecode($diary -> content);
            $response->setData($diary);
            if ($diary -> is_public == '0'){
                $password_result = $con->query(MSql::QueryUserPassword($_COOKIE['diaryEmail']));
                if ($password_result) {
                    if ($password_result->num_rows !== 0) { // 存在用户
                        $row = $password_result->fetch_array();
                        if ($_COOKIE['diaryToken'] == $row['password']) {
                            $response->setData($diary);
                        } else {
                            $response = new ResponseError('无权查看');
                        }
                    } else {
                        $response = new ResponseError('无权查看');
                    }
                } else {
                    $response = new ResponseError('无权查看该日记');
                }
            } else{
                $response->setData($diary);
            }
        } else {
            $response = new ResponseError('查无此日记');
        }
    } else {
        $response = new ResponseError();
    }
    echo $response->toJson();
    $con->close();
}



/*
 * unicode -> text
 */
function unicodeEncode($str){
    if(!is_string($str))return $str;
    if(!$str || $str=='undefined')return '';

    $text = json_encode($str);
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
        return addslashes($str[0]);
    },$text);
    return json_decode($text);
}

/**
 * text -> unicode
 */
function  unicodeDecode($str)
{
    $text = json_encode($str);
    $text = preg_replace_callback('/\\\\\\\\/i', function ($str) {
        return '\\';
    }, $text);
    return json_decode($text);
}