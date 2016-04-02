<?php
return array(
	//'配置项'=>'配置值'
   
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'attend',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    // 'DB_PWD'                =>  'tech@stunology',          // 密码
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
   
    'DOMAIN_URL' => 'http://www.dianming.com',
    'Wechat' => array(
       'token'=>'abcdabcdabcd', //填写你设定的key
       'encodingaeskey'=>'', //填写加密用的EncodingAESKey
       'appid'=>'wx150e95df0d2ffdef', //填写高级调用功能的app id
       'appsecret'=>'0c6542a51c50934aff79cfe09e030b9b' //填写高级调用功能的密钥
    ),
);