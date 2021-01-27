<?php
/**
 * SQL 操作
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-12
 * Time: 18:00
 */

require "config.php";

date_default_timezone_set('Asia/Shanghai');


class MSql
{

    public static $CREATEDIARIES = "CREATE TABLE `diaries` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `date_create` datetime NOT NULL,
                                      `content` varchar(255) NOT NULL,
                                      `category` enum('life','study','film','game','work','sport','bigevent','other') NOT NULL DEFAULT 'life',
                                      `date_modify` datetime DEFAULT NULL,
                                      `date` datetime NOT NULL,
                                      `uid` int(11) NOT NULL,
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    public static $CREATELOGINLOG = "CREATE TABLE `login_log` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `date` datetime NOT NULL,
                                      `email` varchar(50) NOT NULL,
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    public static $CREATEUSERS = "CREATE TABLE `users` (
                                      `uid` int(11) NOT NULL AUTO_INCREMENT,
                                      `email` varchar(50) NOT NULL,
                                      `password` varchar(100) NOT NULL,
                                      `last_visit_time` datetime DEFAULT NULL,
                                      `username` varchar(50) DEFAULT NULL,
                                      `register_time` datetime DEFAULT NULL,
                                      PRIMARY KEY (`uid`,`email`)
                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";


    /************************* 日记操作 *************************/

    // 搜索日记
    public static function SearchDiaries($uid, $categories, $filterShared, $dateRange, $keyword, $startPoint, $pageCount)
    {
        $dateRangeStr = '';
        if ($dateRange){
            $year = substr($dateRange,0,4);
            $month = substr($dateRange,4,2);
            $dateRangeStr = "and  YEAR(date)='${year}' AND MONTH(date)='${month}'";
        }

        $categoryStr = '';
        if (count($categories) > 0) {
            for($i=0; $i<count($categories); $i++){
                $template = " or category='${categories[$i]}'";
                $categoryStr .= $template;
            }
            $categoryStr = substr($categoryStr,4);
        } else {
            $categoryStr = "category = ''";
        }
        $shareStr = $filterShared === '1'? "and is_public = 1": "";
        $sql = "SELECT *
                  from diaries 
                  where uid='${uid}' 
                  and (${categoryStr}) ${shareStr} ${dateRangeStr}
                  and ( title like '%${keyword}%' or content like '%${keyword}%')
                  order by date desc  
                  limit $startPoint, $pageCount";
        return $sql;
    }



    // 日记统计：类别
    public static function DiaryStatisticCategory($uid)
    {
        return "
                select  
                count(case when category='life' then 1 end) as life,
                count(case when category='study' then 1 end) as study,
                count(case when category='film' then 1 end) as film,
                count(case when category='game' then 1 end) as game,
                count(case when category='work' then 1 end) as work,
                count(case when category='sport' then 1 end) as sport,
                count(case when category='bigevent' then 1 end) as bigevent,
                count(case when category='week' then 1 end) as week,
                count(case when category='article' then 1 end) as article
                from diaries where uid='${uid}'
        ";
    }

    // 添加日记
    public static function AddDiary($uid, $title, $content, $category, $weather, $temperature, $temperature_outside, $date, $is_public)
    {
        $timeNow = date('Y-m-d H:i:s');
        $parsed_title = addslashes($title);
        $parsed_content = addslashes($content);
        return "INSERT into diaries(title,content,category,weather,temperature,temperature_outside,date_create,date_modify,date,uid, is_public )
                VALUES('${parsed_title}','${parsed_content}','${category}','${weather}','${temperature}','${temperature_outside}','${timeNow}','${timeNow}','${date}','${uid}','${is_public}')";
    }

    // 日记统计: category
    public static function StatisticDiaryByCategory($uid)
    {
        return "select  
                count(case when category='life' then 1 end) as life,
                count(case when category='study' then 1 end) as study,
                count(case when category='film' then 1 end) as film,
                count(case when category='game' then 1 end) as game,
                count(case when category='work' then 1 end) as work,
                count(case when category='sport' then 1 end) as sport,
                count(case when category='bigevent' then 1 end) as bigevent,
                count(case when category='week' then 1 end) as week,
                count(case when category='article' then 1 end) as article,
                count(case when is_public='1' then 1 end) as shared,
                count(*) as amount
                from diaries where uid='${uid}'";
    }

    // 日记统计: month
    public static function StatisticDiaryByMonth($uid, $year)
    {
        return "select 
                date_format(date,'%Y%m') as id,
                date_format(date,'%m') as month,
                count(*) as 'count'
                from diaries 
                where year(date) = ${year}
                and uid = ${uid}
                group by month
                order by month desc";
    }

    // 删除日记
    public static function DeleteDiary($uid, $id)
    {
        return "DELETE from diaries
                WHERE id='${id}'
                and uid='${uid}'";
    }

    // 更新日记
    public static function UpdateDiary($uid, $id, $title, $content, $category, $weather, $temperature, $temperature_outside, $date, $is_public)
    {
        $timeNow = date('Y-m-d H:i:s');
        $parsed_title = addslashes($title);
        $parsed_content = addslashes($content);
        $sql =  "update diaries 
                set diaries.date_modify='${timeNow}', 
                  diaries.date='${date}', 
                  diaries.category='${category}',
                  diaries.title='${parsed_title}',
                  diaries.content='${parsed_content}',
                  diaries.weather='${weather}',
                  diaries.temperature='${temperature}',
                  diaries.temperature_outside='${temperature_outside}',
                  diaries.is_public='${is_public}'
                WHERE id='${id}' and uid='${uid}'";
        return $sql;
    }

    // 查询日记内容
    public static function QueryDiaries($uid, $id)
    {
        return "select * from diaries
                where uid = '${uid}' and id = '${id}'";
    }

    // 查询日记内容 - 分享时
    public static function QuerySharedDiaries($id)
    {
        return "select * from diaries
                where id = '${id}'";
    }


    /************************* 用户操作 *************************/

    //  更新密码
    public static function UpdateUserPassword($email, $password)
    {
        return "update users set `password` = '${password}' where email='${email}'";
    }

    // 查询密码
    public static function QueryUserPassword($email)
    {
        return "select * from users where email='${email}'";
    }

    //  新增用户
    public static function InsetNewUser($email, $password, $username)
    {
        $timeNow = date('Y-m-d H:i:s');
        return "insert into users(email, password, register_time, username) VALUES ('${email}','${password}','${timeNow}','${username}')";
    }

    // 查询用户是否存在
    public static function QueryEmailExitance($email)
    {
        return "select email from users where email='${email}'";
    }

    //  记录用户最后登录时间
    public static function InsertLoginLog($email)
    {
        $timeNow = date('Y-m-d H:i:s');
        return "update users set last_visit_time='${timeNow}' where email='${email}'";
    }

}

/**
 * dsqli 继承 mysqli
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-12
 * Time: 18:00
 */
class dsqli extends mysqli
{
    public function __construct()
    {
        parent::__construct(HOST, USER, PASSWORD, DATABASE, PORT);
        $this -> set_charset('utf8');
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        parent::query("SET NAMES 'utf8'");
        return parent::query($query, $resultmode);
    }
}

