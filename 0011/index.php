<?php

/**
 * role users.
 * 0-simple
 * 1-admin
 */

session_start();
function ip(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

if(!isset($_SESSION['time_token'])){
    $_SESSION['time_token']=time() + (60 * 60) . '*' . ip();//one hour
}

define('ROOT',getenv('DOCUMENT_ROOT'));
define('APP',ROOT.'/');

function url(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'];
}
function clear($data){
    $array1 = array('?','%', '/*', '*/', '//', 'wss:', 'ws:', 'blob:', 'localhost', 'http:', 'https:', 'script', 'base64', 'mysql', 'union', 'select','update','where','\0x','delete','drop','information','schema');
    $data = strip_tags($data);
    $data = htmlspecialchars($data, ENT_QUOTES);
    $data = trim($data);
    return str_ireplace($array1, '', $data);
}
function decrypt_($data){
    $method = "aes-256-cbc";
    $pass = "frep9drfhdrth46rtht";
    $iv = '8885-00-77227zxc';
    $data = base64_decode(base64_decode($data));
    return openssl_decrypt($data, $method, $pass, true, $iv);
}
function crypt_($data){
    $method = "aes-256-cbc";
    $pass = "frep9drfhdrth46rtht";
    $iv = '8885-00-77227zxc';
    $data = openssl_encrypt($data, $method, $pass, true, $iv);
    $data = base64_encode(base64_encode($data));
    return $data;
}

//init loginme
require_once ROOT.'/loginme/loginmeInit.php';
require_once ROOT.'/vendor/autoload.php';

class db extends PDO{
    public function __construct(){
        parent::__construct("mysql:host=localhost;dbname=".L_DB_NAME, L_DB_USER, L_DB_PASS);
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // always disable emulated prepared statement when using the MySQL driver
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}

$login=new loginme\loginme;
use loginme\users;
use loginme\products;
use loginme\suppliersClass;

class main{
    public $scripts=array();
    public $styles=array();
    public $customScripts=array();
    public $customScriptsAll='';

    public function script($name){
        array_push($this->scripts,$name);
        return $this->scripts;
    }
    public function customScript($code){
        array_push($this->customScripts,$code);
        return $this->customScripts;
    }
    public function css($name){
        array_push($this->styles,$name);
        return $this->styles;
    }

    public function getAllProducts(){
        $login=new loginme\loginme;
        $login->isAuthorized();

        $db=new db;

        $stmt=$db->prepare("SELECT `id`, `name`, `price`, `priceopt`, `quantity`, `comment`, `datecreate`, `who`, `state`, `valid` FROM `flr_goods`");
        $stmt->execute();
        $data = $stmt->fetchAll($db::FETCH_CLASS);

        return $data;
    }

    public function load($tpl,$title,$needLinks=false,$dataArray=array()){
        if(isset($_SESSION['errorsLast']) AND $_SESSION['errors']==$_SESSION['errorsLast']){
            unset($_SESSION['errors']);
            unset($_SESSION['errorsLast']);
        }
        if(isset($_SESSION['errors'])){$_SESSION['errorsLast']=$_SESSION['errors'];}
        $dir=APP."view/{$tpl}.php";
        $out= '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href=/sys/css/css.css>
    <title>'. $title .'</title>
    </head>
<body>';

        if(isset($_SESSION['errors'])AND!empty($_SESSION['errors'])){
            $out.= '<div class="container alert alert-danger" role="alert">'.$_SESSION['errors'] .'</div>';
        }

        if(!empty($needLinks) AND $needLinks!==false AND is_array($needLinks)){
            $out.='<nav class="z2 container"><ul class="nav">';
            foreach($needLinks as $k=>$v){
                if(!empty($v))$out.='<li class="nav-item">
    <a class="nav-link active" href="'.$k.'">'.$v.'</a>
  </li>';
            }
            $out.='</ul></nav>';
        }

        if(file_exists($dir))require_once $dir;

        foreach ($this->scripts as $script){
            $out.="\n<script src=/sys/js/{$script}.js></script>";
        }
        foreach ($this->customScripts as $script){
            $this->customScriptsAll.="\n{$script}";
        }

        if(!empty($this->customScriptsAll)){
            $out.=$this->customScriptsAll;
        }
        foreach ($this->styles as $style){
            $out.="\n<link rel=stylesheet href=/sys/css/{$style}.css>";
        }

        $out.= '
</body>
</html>';
        echo $out;
        exit;
    }
}

#fetch API
if($_SERVER['REQUEST_METHOD']=='POST') {
    if (stripos($_POST['location_'], url()) !== false
        && $_SERVER['HTTPS'] == 'on'
        /* && $_SERVER['HTTP_SEC_FETCH_SITE'] == 'same-origin'
        && $_SERVER['HTTP_SEC_FETCH_MODE'] == 'same-origin' */
        && $_SERVER['CONTENT_TYPE'] == 'application/x-www-form-urlencoded'
    ) {

        if (!empty($_POST["set"])) {
            if ($_POST["set"] == 'addUser') {
                $login->createUser();
                exit;
            }
            if ($_POST["set"] == 'editSingleUser') {
                $users = new users;
                $users->editSingleUser();
                exit;
            }
            if ($_POST["set"] == 'deleteSingleUser') {
                $users = new users;
                $users->deleteSingleUser();
                exit;
            }
            if ($_POST["set"] == 'editSingleProduct') {
                $product = new products;
                $product->editSingleProduct();
                exit;
            }
            if ($_POST["set"] == 'deleteSingleProduct') {
                $product = new products;
                $product->deleteSingleProduct();
                exit;
            }
            if ($_POST["set"] == 'addSingleProductAdmin') {
                $product = new products;
                $product->addSingleProductAdmin();
                exit;
            }
            if ($_POST["set"] == 'addSingleProductManager') {
                $product = new products;
                $product->addSingleProductManager();
                exit;
            }
            if ($_POST["set"] == 'deleteSingleSupplier') {
                $supplier = new suppliersClass;
                $supplier->deleteSingleSupplier();
                exit;
            }
            if ($_POST["set"] == 'saveSingleSupplier') {
                $supplier = new suppliersClass;
                $supplier->saveSingleSupplier();
                exit;
            }
            if ($_POST["set"] == 'saveNewSingleSupplier') {
                $supplier = new suppliersClass;
                $supplier->saveNewSingleSupplier();
                exit;
            }
            if ($_POST["set"] == 'addSingleSale') {//for edit single product in products table
                if ($login->isAdmin() != 1) {
                    echo $login->json('error', 'E021. \Index. Нет прав доступа.');
                    exit;
                }
                $products = new products;
                $products->addSingleSale();
                exit;
            }
        }
        if (!empty($_POST["get"])) {
            if ($_POST["get"] == 'login') {
                $login->login();
                exit;
            }

            if ($_POST["get"] == 'singleUser') {
                if ($login->isAdmin() != 1) {
                    echo $login->json('error', 'E017. \Index. Нет прав доступа.');
                    exit;
                }
                $users = new users;
                $users->getEditUser();
                exit;
            }
            if ($_POST["get"] == 'singleProduct') {
                $login->isAuthorized();
                $products = new products;
                $products->getSingleProduct();
                exit;
            }
            if ($_POST["get"] == 'singleProductManager') {
                $login->isAuthorized();
                $products = new products;
                $products->getSingleProduct(0);
                exit;
            }
            if ($_POST["get"] == 'getSupplier') {
                if ($login->isAdmin() != 1) {
                    echo $login->json('error', 'E018. \Index. Нет прав доступа.');
                    exit;
                }
                $supplier = new suppliersClass;
                $supplier->getSupplier();
                exit;
            }
            if ($_POST["get"] == 'getAllSuppliers') {
                if ($login->isAdmin() != 1) {
                    echo $login->json('error', 'E019. \Index. Нет прав доступа.');
                    exit;
                }
                $supplier = new suppliersClass;
                $supplier->begin(1);
                exit;
            }
            if ($_POST["get"] == 'allSuppliersNotIn') {//for edit single product in products table
                if ($login->isAdmin() != 1) {
                    echo $login->json('error', 'E020. \Index. Нет прав доступа.');
                    exit;
                }
                $supplier = new suppliersClass;
                $supplier->allSuppliersNotIn();
                exit;
            }
            if ($_POST["get"] == 'allSalesSingleProduct') {
                if ($login->isAdmin() != 1) {
                    echo $login->json('error', 'E022. \Index. Нет прав доступа.');
                    exit;
                }
                $products = new products;
                $products->allSalesSingleProduct();
                exit;
            }
        }
        exit;
    }else{echo 'E000. Error... Ошибка';exit;}
}
#\fetch API

$main=new main;
$links=[];
$route=getenv('REQUEST_URI');
if($login->isAdmin()==1){//admin
    $links=array('/'=>'Главная','/add-product'=>'Добавить товар','/products'=>'Управление товарами','/add-user'=>'Добавить пользователя','/users'=>'Управление пользователями','/suppliers'=>'Поставщики');
}else{//manager
    $links=array('/'=>'Главная','/add-product'=>'Добавить товар','/products'=>'Управление товарами');
}

if($login->isAdmin()!==false){
    $links+=['/logout'=>'Выйти'];
}

if(!isset($_SESSION['userid'])){
    $links=array('/'=>'Главная');
}

$linksArray=array();
foreach ($links as $k=>$v){
    $linksArray[]=$k;
}
if(!in_array($route,$linksArray)){
    header("HTTP/1.1 404 Not Found");
    header("Location: /err404.html");
    exit;
}

if($route=='/'){
    $login->isAuthorized();
    $main->script('jquery.min');
    $main->load('login','Авторизация');
}

//Удалим Главная (т.к. это авторизация)
if($login->isAdmin()!==false)unset($links['/']);

if($route=='/logout'){//auth only
    $login->isAuthorized();
    session_destroy();
    session_unset();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    header('refresh:5;url=/');
    $main->load('logout','Выйти из панели');
}

if($route=='/add-user'){
    $login->isAuthorized(1);//ADMIN only
    if($login->isAdmin()==1){//if user == admin
        $main->script('jquery.min');
        $main->load('addUser','Добавить пользователя',$links);
    }else{
        $main->load('error','Ошибка');
    }
}

if($route=='/users'){
    $users=new users;
    $users=$users->begin();
    $login->isAuthorized(1);//ADMIN only
    if($login->isAdmin()==1){//if user == admin
        $main->script('jquery.min');
        $main->load('users','Пользователи',$links,array($users));
    }else{
        $main->load('error','Ошибка');
    }
}

if($route=='/suppliers'){
    $suppliers=new suppliersClass;
    $suppliers=$suppliers->begin();
    $login->isAuthorized(1);//ADMIN only
    if($login->isAdmin()==1){//if user == admin
        $main->script('jquery.min');
        $main->load('suppliers','Поставщики',$links,array($suppliers));
    }else{
        $main->load('error','Ошибка');
    }
}

if($route=='/products'){//auth only
    $login->isAuthorized();
    if($login->isAdmin()){
        $main->script('jquery.min');
        $main->load('productsAdmin','Управление товарами',$links,$main->getAllProducts());
    }else{
        $main->script('jquery.min');
        $main->load('products','Управление товарами',$links,$main->getAllProducts());
    }
}

if($route=='/add-product'){//auth only
    $login->isAuthorized();
    if($login->isAdmin()){//admin
        $main->script('jquery.min');
        $main->load('addProductAdmin','Добавить товар',$links);
    }else{//manager
        $main->script('jquery.min');
        $main->load('addProduct','Добавить товар',$links);
    }
}
