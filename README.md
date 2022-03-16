# Diary 后台 for [diary-vue](https://github.com/KyleBing/diary-vue)

## 一、数据库的结构在项目的根目录下 `diary.sql`

## 二、修改 `./class/config.php` 中的变量。

   > `INVITATION` 是前端注册时使用的邀请码，只有对应上才能正常注册用户
    ```php
    define('HOST',          '127.0.0.1');       // 数据库地址
    define('PORT',          '3306');            // 数据库端口
    define('DATABASE',      'diary');           // 数据库名
    define('USER',          '----');            // 数据库用户名
    define('PASSWORD',      '----');            // 数据库密码
    define('INVITATION',    '----');            // 邀请码，注册用户时使用，
    ```
