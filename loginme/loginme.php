<?php
/**
 * Created by INVERSER
 * Date: 17.02.2020
 * Time: 9:50
 */

namespace loginme;
use db;

class loginme{
    private $post=array();

    public function isAdmin(){
        /** false = unauthorized
         * 0 - manager
         * 1 - admin
        */
        if(!isset($_SESSION['userid'])){
            return false;
        }
        $isAdmin=0;
        if(self::isAuthorized()==1){
            $isAdmin=true;
        }
        return $isAdmin;
    }

    public function dataPost($post){
        //var_dump($post);exit;
        //role:0,name:TEST,email:go@inverser.pro,password:123456,comment:вапвап ваппп
        //""password":"fghfdthdfth6r7676","email":"go@inverser.pro","token":"aYMHdWd0tnTDA3M2x6bE9pb2M1ejZEMS8vWllWRVphV3JPbXNUVEZzQT0=""

        $return_data = false;
        $isJson = (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $post : TRUE) : FALSE;
        if(!$isJson){return false;exit;}
        $post=json_decode($post);
        foreach ($post as $q=>$v){
            $this->post[$q]=$v;
        }
        return $this->post;
    }

    public function json($stateTxt,$text,$script=''){
        if(empty($script)){
            return json_encode(['state'=>$stateTxt,'data'=>$text],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['state'=>$stateTxt,'data'=>$text,'script'=>$script],JSON_UNESCAPED_UNICODE);
        }
    }

    public function checkEmpty($array){
        foreach ($array as $q){
            if($q==''){
                echo self::json('error','Проверьте данные...');
                exit;
            }
        }
    }
    public function checkEmptyReturn($array){
        foreach ($array as $q){
            if(empty($q)){
                return false;
            }
        }
    }

    /** only for fetch
    */
    public function checkToken($token,$tokenLS=NULL){

        if(stripos(getenv('HTTP_REFERER'),url())===false){//not exist
            echo self::json('error','E002. Referer error... Ваш браузер не передает нам нужные данные. Обратитесь к администратору ресурса...');
            exit;
        }

        if(self::length($token)<5 AND self::length($token) > 100){
            echo self::json('error','E055. Попробуйте еще раз... Если ошибка повторяется, обновите страницу и попытайтесь снова. Если после этого снова возникает ошибка, выйдите (вверху кнопка "Выйти") и войдите снова.');
                exit;
        }

        $tokenTxt='. Token error... Попробуйте выйти и авторизироваться снова.';
        $tokenSession=explode('*',$_SESSION['time_token']);//1582274371*127.0.0.1|0-time, 1-ip()

        $tokenUserSession=explode('*',decrypt_($_SESSION['userid']));//User ID (int) * IP
        if($tokenLS!==NULL){

            if(self::length($tokenLS)<5 AND self::length($tokenLS) > 250){
                echo self::json('error','E056. Попробуйте еще раз... Если ошибка повторяется, обновите страницу и попытайтесь снова. Если после этого снова возникает ошибка, выйдите (вверху кнопка "Выйти") и войдите снова.');
                exit;
            }

            //Проверка трех токенов для изменений в БД. Уровень доступа admin..
            //Сверим токен в сессии, токен из формы и токен из localStorage
            $token=explode('*',decrypt_(clear($token)));//1582274371*127.0.0.1|0-time, 1-ip()
            $tokenLS=explode('*',decrypt_(decrypt_(clear($tokenLS))));//ID: 1* IP: 127.0.0.1

            if(empty($token[1]) OR empty($tokenLS[1]) OR empty($tokenUserSession[1])){
                echo self::json('error','E024. Попробуйте еще раз... Если ошибка повторяется, обновите страницу и попытайтесь снова. Если после этого снова возникает ошибка, выйдите (вверху кнопка "Выйти") и войдите снова.','$(".token").val("'.crypt_($_SESSION['time_token']).'")');
                exit;
            }
            //Проверим, совпадают ли все IP
            $ipsArray=array($token[1] , $tokenLS[1] , $tokenUserSession[1]);
            foreach ($ipsArray as $ip){
                if($ip != ip()){
                    echo self::json('error','E025'.$tokenTxt);
                    exit;
                }
            }
            //Проверим, совпадают ли все ID
            if($tokenLS[0] != $tokenUserSession[0]){
                echo self::json('error','E026'.$tokenTxt);
                exit;
            }

            $userIdFromToken=$tokenLS[0];
            $db=new db;

            //ID в токене НЕ больше ли, чем максимальный в базе
            $stmt=$db->prepare("SELECT max(`id`) FROM `flr_users`");
            $stmt->execute();
            $data = $stmt->fetch();
            if($userIdFromToken > $data[0]){
                echo self::json('error','E027'.$tokenTxt);
                exit;
            }

            //Проверим есть ли такой ID в базе из токенов
            $stmt=$db->prepare("SELECT `id`,`role` FROM `flr_users` WHERE `id` = ? AND `state`=1");
            $stmt->execute(array( $userIdFromToken ));
            $data = $stmt->fetch();

            if(empty($data)){
                echo self::json('error','E028'.$tokenTxt);
                exit;
            }

        }else{
            $token=explode('*',decrypt_(clear($token)));//1582274371*127.0.0.1|0-time, 1-ip()
        }
        if($this->checkEmptyReturn(array($token[0],$token[1]))===false){
            echo self::json('error','E001'.$tokenTxt);
            exit;
        }
        $timeLen=self::length((int)$token[0]);
        if($timeLen>12 OR $timeLen<10){
            echo self::json('error','E004'.$tokenTxt);
            exit;
        }
        if((int)$token[0]==0){
            //create new token
            $_SESSION['time_token']=time() + (60 * 60) . '*' . ip();
            echo self::json('error','E005. Попробуйте еще раз... Если ошибка повторяется, обновите страницу и попытайтесь снова.','$(".token").val("'.crypt_($_SESSION['time_token']).'")','localStorage.setItem("flr_secret","'. crypt_($_SESSION['userid']) .'");');
            exit;
        }
        if((time() + (60 * 60))-(int)$token[0]>3600){
            //create new token
            $_SESSION['time_token']=time() + (60 * 60) . '*' . ip();
            echo self::json('error','E005. Попробуйте еще раз... Если ошибка повторяется, обновите страницу и попытайтесь снова.','$(".token").val("'.crypt_($_SESSION['time_token']).'")');
            exit;
        }
        if(ip()!=$token[1]){
            echo self::json('error','E003'.$tokenTxt);
            exit;
        }
        if (!filter_var($token[1], FILTER_VALIDATE_IP)) {
            echo self::json('error','E006'.$tokenTxt);
            exit;
        }
        /*check IP in token vs token IP in session*/
        if($tokenSession[1]!=$token[1]){//chekc IP's
            echo self::json('error','E007'.$tokenTxt);
            exit;
        }
    }

    public function login(){
        $q=str_replace('&quot;','"',$_POST['data']);
        $token=clear(self::dataPost($q)['token']);
        $email=clear(self::dataPost($q)['email']);
        $password=self::dataPost($q)['password'];

        self::checkEmpty(array($token,$email,$password));

        if(isset($email) AND !empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                echo self::json('error','Проверьте email');
                exit;
            }
            if(self::length($email)>50){
                echo self::json('error','Проверьте email. Должно быть меньше 50-ти символов.');
                exit;
            }
        }

        if(isset($password) AND !empty($password)){
            if(self::length($password)<7){
                echo self::json('error','Проверьте пароль. Должен быть больше 6-ти символов.');
                exit;
            }
            if(self::length($password)>30){
                echo self::json('error','Проверьте пароль. Должен быть меньше 30-ти символов.');
                exit;
            }
        }

        self::checkEmpty(array(getenv('HTTP_REFERER')));
        // self::checkToken($token);
        //DB checkers
        $db=new db;

        $stmt=$db->prepare("SELECT `id`,`email`,`password` FROM `flr_users` WHERE `email` = ? AND `state`=1");
        $stmt->execute(array( $email ));
        $data = $stmt->fetch();

        if(empty($data)){
            echo self::json('error','Такого email нет или пользователь удален...');
            exit;
        }
        if (!password_verify($password, $data['password'])) {
            echo self::json('error','Неверный пароль...');
            exit;
        }

        //all ok
        $_SESSION['userid']=crypt_($data['id'].'*'.ip());
        echo self::json('ok','Авторизация успешна. Сейчас Вы будете перенаправлены...','localStorage.setItem("flr_secret","'. crypt_($_SESSION['userid']) .'");setTimeout(function(){window.location.href="/products"},5000);');
        exit;
    }

    public function length($str){
        return iconv_strlen($str,'UTF-8');
    }

    /** check is user authorized
     * return int 0-manager, 1-admin
     */
    public function isAuthorized($role=NULL){

        if(isset($_POST['get']) AND $_POST['get']=='login')return false;
        if(getenv('REQUEST_URI') !='/' OR isset($_POST['get']) OR isset($_POST['set'])) {
            if (!isset($_SESSION['userid']) OR empty($_SESSION['userid'])) {
                $_SESSION['errors'] = 'Авторизируйтесь...';
                header("HTTP/1.1 403 Forbidden");
                header("Location: /");
                exit;
            }

            $data = explode('*', decrypt_($_SESSION['userid']));

            if (self::checkEmptyReturn($data) === true) {//not exist user id OR ip in session 'userid'
                //not authorized, redirect to login
                $_SESSION['errors'] = 'E014. Авторизируйтесь...';
                header("HTTP/1.1 403 Forbidden");
                header("Location: /");
                exit;
            }
            $id = abs((int)$data[0]);//id (int) 1
            $ip = $data[1];//id (int) 1
            $idLen = self::length($id);
            if ($idLen > 4 OR $idLen < 1 OR $idLen == 0 OR $id > 9999 OR $id < 1 OR $id == 0) {
                $_SESSION['errors'] = 'E015. Авторизируйтесь...';
                header("HTTP/1.1 401 Unauthorized");
                header("Location: /");
                exit;
            }
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $_SESSION['errors'] = 'E016. Авторизируйтесь...';
                header("HTTP/1.1 401 Unauthorized");
                header("Location: /");
                exit;
            }

            $db = new db;
            $stmt = $db->prepare("SELECT `id`,`role` FROM `flr_users` WHERE `id` = ? AND `state`=1");
            $stmt->execute(array($id));
            $data = $stmt->fetch();

            if (empty($data)) {
                $_SESSION['errors'] = 'E008. Свяжитесь с администратором ресурса...';
                header("HTTP/1.1 403 Forbidden");
                header("Location: /");
                exit;
            }

            /** user role - manager - 0, admin - 1
             */
            if ($role !== NULL AND $role != $data['role']) {
                $_SESSION['errors'] = 'E009. Нет прав доступа. Свяжитесь с администратором ресурса...';
                header("HTTP/1.1 403 Forbidden");
                header("Location: /products");
                exit;
            }
        }else{
            if(isset($_SESSION['userid']) OR !empty($_SESSION['userid'])) {
                header("HTTP/1.1 403 Forbidden");
                header("Location: /products");
                exit;
            }
        }


        if(!isset($data)){
            $data=[];
            $data['role']=-1;
        }
        return $data['role'];
    }

    public function createUser(){
        if(self::isAdmin()!=1){
            echo self::json('error','E010. Нет прав доступа. Обратитесь к администратору ресурса.');
            exit;
        }

        $db=new db;
        $q=str_replace('&quot;','"',clear($_POST['data']));

        $name=$this->dataPost($q)['name'];
        $email=$this->dataPost($q)['email'];
        $password=$this->dataPost($q)['password'];
        $role=(string)$this->dataPost($q)['role'];
        $comment=(!empty($this->dataPost($q)['comment']))?$this->dataPost($q)['comment'] : '';

        $this->checkEmpty(array($name,$email,$password,$role));

        //var_dump($name,$email,$password,$role);exit;

        if(isset($email) AND !empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                echo self::json('error','Проверьте email');
                exit;
            }
            if(self::length($email)>50){
                echo self::json('error','Проверьте email. Должно быть меньше 50-ти символов.');
                exit;
            }
        }
        if(isset($name) AND !empty($name)){
            if(self::length($name)<2){
                echo self::json('error','Проверьте имя. Должно быть больше 2-х символов.');
                exit;
            }
            if(self::length($name)>30){
                echo self::json('error','Проверьте имя. Должно быть меньше 30-ти символов.');
                exit;
            }
        }
        if(isset($password) AND !empty($password)){
            if(self::length($password)<7){
                echo self::json('error','Проверьте пароль. Должен быть больше 6-ти символов.');
                exit;
            }
            if(self::length($password)>30){
                echo self::json('error','Проверьте пароль. Должен быть меньше 30-ти символов.');
                exit;
            }
        }
        if(isset($role) AND !empty($role)){
            if((int)$role>6){
                echo self::json('error','Выберите уровень доступа.');
                exit;
            }
        }

        $stmt=$db->prepare("SELECT `email` FROM `flr_users` WHERE `email` LIKE ?");
        $stmt->execute(array('%' . $email . '%'));
        $data = $stmt->fetchAll();

        if(!empty($data)){
            echo self::json('error','Такой email уже занят.');
            exit;
        }else{
            $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $sql = "INSERT INTO `flr_users`(`name`, `email`, `dateCreate`, `role`, `comment`, `password`, `state`) VALUES (?,?,NOW(),?,?,?,1)";
            $stmt= $db->prepare($sql);
            $stmt->execute(array($name, $email, $role, $comment, $password));
            echo self::json('ok',"Пользователь {$name} добавлен.");
            exit;
        }
    }

    public function checkLastId($id){
        $id=(int)decrypt_($id);
        $db=new db;

        $stmt=$db->prepare("SELECT max(`id`) FROM `flr_users`");
        $stmt->execute();
        $dataMaxId = $stmt->fetch();

        if(!$dataMaxId){
            echo self::json('error','E021. \Users. Проверьте ID.');
            exit;
        }
        if($id>$dataMaxId[0]){
            echo self::json('error','E022. \Users. Проверьте ID.');
            exit;
        }
    }
}

if(count(get_included_files()) ==1) {
    header("HTTP/1.1 403 Forbidden");
    header("Location: /");
    exit;
}