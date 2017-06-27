<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2017-06-27
 * Time: 15:20
 */

include_once ("Captcha.php");

login();

function login(){

    $cookie_file = tempnam('temp', '66_cookie');
    //获取登录页面的cookie
    $ch=curl_init("http://www.66.cn/login.asp");
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file);
    curl_exec($ch);
    curl_close($ch);

    //获取验证码
    $captcha = getCaptcha($cookie_file);

    //提交登录表单请求
    $login_url="http://www.66.cn/AgentLogin.asp";
    $post_fields="username=username&userpass=password&validcode_login=$captcha";
    $ch=curl_init($login_url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
    curl_exec($ch);
    curl_close($ch);

    //登录完成显示首页
    $url = "http://www.66.cn/default.asp";
    $ch=curl_init($url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
    $contents = curl_exec($ch);
    curl_close($ch);
    echo iconv('gbk', 'UTF-8', $contents);
}
function getCaptcha($cookie_file){
    //获取页面的验证码
    $ch=curl_init("http://www.66.cn/validcode.asp?sname=validcode_login");
    $fb = fopen('66_captcha.bmp','wb');
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_FILE,$fb);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
    curl_exec($ch);
    curl_close($ch);
    fclose($fb);

    $captcha = new Captcha();
    $result = $captcha->getCaptcha('66','66_captcha.bmp');
    return $result;
}