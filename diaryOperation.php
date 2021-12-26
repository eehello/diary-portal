<?php
/**
 * 日记的相关操作方法
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 19:24
 */
require "common.php";
require "class/Response.php";
require "class/MSql.php";
require "class/ResponseError.php";
require "class/ResponseLogin.php";
require "class/ResponseSuccess.php";
require "class/Statistic.php";
require "class/StatisticMonth.php";

// 传 email 是为了避免，邮件正确，日记id不对的情况

if (checkLogin($_COOKIE['diaryEmail'], $_COOKIE['diaryToken'])) {
    switch ($_REQUEST['type']) {
        case 'query':
            queryDiary($_COOKIE['diaryUid'], $_GET['diaryId']);
            break;
        case 'modify':
            updateDiary($_COOKIE['diaryUid'], $_POST['diaryId'], $_POST['diaryTitle'], $_POST['diaryContent'], $_POST['diaryCategory'], $_POST['diaryWeather'], $_POST['diaryTemperature'], $_POST['diaryTemperatureOutside'], $_POST['diaryDate'], $_POST['diaryPublic']);
            break;
        case 'add':
            addDiary($_COOKIE['diaryUid'], $_POST['diaryTitle'], $_POST['diaryContent'], $_POST['diaryCategory'], $_POST['diaryWeather'], $_POST['diaryTemperature'], $_POST['diaryTemperatureOutside'], $_POST['diaryDate'], $_POST['diaryPublic']);
            break;
        case 'delete':
            deleteDiary($_COOKIE['diaryUid'], $_POST['diaryId']);
            break;
        case 'statistic':
            diaryStatistic($_COOKIE['diaryUid']);
            break;
        case 'search':
        case 'list':
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $categories = isset($_GET['diaryCategories']) ? json_decode($_GET['diaryCategories']) : '';
            searchDiary($_COOKIE['diaryUid'], $categories, $_GET['filterShared'], $_GET['dateRange']? $_GET['dateRange']: '' , $keyword, $_GET['pageCount'], $_GET['pageNo']);
            break;
        default:
            $response = new ResponseError('请求参数错误');
            echo $response->toJson();
            break;
    }
} else {
    $response = new ResponseError('密码错误，请重新登录');
    $response->setLogined(false);
    echo $response->toJson();
}



// 搜索，展示日记
function searchDiary($uid, $categories,$filterShared, $dateRange, $keyword, $pageCount, $pageNo)
{
    $startPoint = ($pageNo - 1) * $pageCount;
    $con = new dsqli();
    $response = '';
    $result = $con->query(MSql::SearchDiaries($uid, $categories, $filterShared, $dateRange, $keyword, $startPoint, $pageCount));
    if ($result) {
        $response = new ResponseSuccess();
        $diaries = $result->fetch_all(1); // 参数1会把字段名也读取出来
        // 处理数据，把带 emoji 表情的数据解析出来
        $decodedDiaries = array();
        foreach ($diaries as $diary){
            $diary['title'] = unicodeDecode($diary['title']);
            $diary['content'] = unicodeDecode($diary['content']);
            array_push($decodedDiaries, $diary);
        }
        $response->setData($decodedDiaries);
    } else {
        $response = new ResponseError();
    }
    echo $response->toJson();
    $con->close();
}

// 查询日记内容
function queryDiary($uid, $id)
{
    $con = new dsqli();
    $response = '';
    $result = $con->query(MSql::QueryDiaries($uid, $id));
    if ($result) {
        $response = new ResponseSuccess();
        $diary = $result->fetch_object();
        if ($diary){
            // 处理数据，把带 emoji 表情的数据解析出来
            $diary -> title = unicodeDecode($diary -> title);
            $diary -> content = unicodeDecode($diary -> content);
            $response->setData($diary);
        } else {
            $response = new ResponseError('查无此日记');
        }
    } else {
        $response = new ResponseError();
    }
    echo $response->toJson();
    $con->close();
}


//修改
function updateDiary($uid, $id, $title, $content, $category, $weather, $temperature, $temperature_outside, $date, $is_public)
{
    $con = new dsqli();
    $response = '';
    $title = unicodeEncode($title);
    $content = unicodeEncode($content);
    $result = $con->query(MSql::UpdateDiary($uid, $id, $title, $content, $category, $weather, $temperature, $temperature_outside, $date, $is_public));
    if ($result) {
        $response = new ResponseSuccess('修改成功');
    } else {
        $response = new ResponseError('修改失败');
    }
    echo $response->toJson();
    $con->close();
}


// 删除
function deleteDiary($uid, $id)
{
    $con = new dsqli();
    $response = '';
    $result = $con->query(MSql::DeleteDiary($uid, $id));
    if ($result) {
        $response = new ResponseSuccess('删除成功');
    } else {
        $response = new ResponseError('删除失败');
    }
    echo $response->toJson();
    $con->close();
}

// 日记统计
function diaryStatistic($uid){
    // TODO: 容错处理
    $con = new dsqli();
    $response = new ResponseSuccess();
    $resultCategory = $con->query(MSql::StatisticDiaryByCategory($uid));
    $statisticCategory = $resultCategory->fetch_object();
    $statisticMonthArray = array();
    $tempYear = date('y',time());
    for ($year=(int)date('Y',time()); $year >= 2010 ; $year--){
        $tempResult = $con->query(MSql::StatisticDiaryByMonth($uid, $year));
        $tempResultArray = $tempResult -> fetch_all(1);
        array_push($statisticMonthArray, new StatisticMonth($year, $tempResultArray));
    }
    $statistic = new Statistic($statisticCategory, $statisticMonthArray);
    $response->setData($statistic);
    echo $response->toJson();
    $con->close();
}

// 添加
function addDiary($uid, $title, $content, $category, $weather, $temperature, $temperature_outside, $date, $is_public)
{
    $con = new dsqli();
    $response = '';
    $title = unicodeEncode($title);
    $content = unicodeEncode($content);
    $result = $con->query(MSql::AddDiary($uid, $title, $content, $category, $weather, $temperature, $temperature_outside, $date, $is_public));
    if ($result) {
        $response = new ResponseSuccess('保存成功');
        $queryResult = $con->query('select * from diaries where id=LAST_INSERT_ID()');
        if ($queryResult){
            $response->setData($queryResult->fetch_object());
        }
    } else {
        $response = new ResponseError('保存失败');
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