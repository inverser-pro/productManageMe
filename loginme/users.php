<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 23.02.2020
 * Time: 1:08
 */

namespace loginme;
use db;
use loginme\loginme;

class users{
    public function begin(){
        $login=new loginme;
        if($login->isAdmin()!==true){
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }

        $db=new db;

        $stmt=$db->prepare("SELECT `id`,`name`,`email`,`dateCreate`,`role`,`comment` FROM `flr_users` WHERE `state` = 1");
        $stmt->execute();
        $data = $stmt->fetchAll($db::FETCH_CLASS);

        return $data;
    }

    public function getEditUser(){
        $login=new loginme;
        if($login->isAdmin()!==true){
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }

        $q=str_replace('&quot;','"',clear($_POST['data']));
        $id=$login->dataPost($q)['thisId'];

        if(empty($id)){
            echo $login->json('error','E015. \Users. Проверьте ID.');
            exit;
        }

        $id=(int)decrypt_($id);
        $db=new db;

        $login->checkLastId($id);

        $stmt=$db->prepare("SELECT `id`,`name`,`email`,`dateCreate`,`role`,`comment` FROM `flr_users` WHERE `id` = ? AND `state`=1");
        $stmt->execute(array($id));
        $data = $stmt->fetchAll($db::FETCH_CLASS);

        if(!empty($data[0]->id)){$data[0]->id=crypt_($data[0]->id);}else{
            echo $login->json('error','E019. \Users. Данных нет.');
            exit;
        }
        echo json_encode($data[0],JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function editSingleUser(){
        $login=new loginme;
        if($login->isAdmin()!==true){
            $_SESSION['errors'] = 'ES001. Отказано в доступе';
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }
        $q=str_replace('&quot;','"',$_POST['data']);
        $id=clear($login->dataPost($q)['id']);
        $role=(string)clear($login->dataPost($q)['role']);
        $name=clear($login->dataPost($q)['name']);
        $email=clear($login->dataPost($q)['email']);
        $comment=clear($login->dataPost($q)['comment']);
        $password=$login->dataPost($q)['password'];
        $password2=$login->dataPost($q)['password2'];
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);

        if(isset($email) AND !empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                echo $login->json('error','Проверьте email');
                exit;
            }
            if($login->length($email)>50){
                echo $login->json('error','Проверьте email. Должно быть меньше 50-ти символов.');
                exit;
            }
        }

        if(isset($password) AND !empty($password)){
            if($login->length($password)<7){
                echo $login->json('error','Проверьте пароль. Должен быть больше 6-ти символов.');
                exit;
            }
            if($login->length($password)>30){
                echo $login->json('error','Проверьте пароль. Должен быть меньше 30-ти символов.');
                exit;
            }
            if($password!=$password2){
                echo $login->json('error','E030. Пароли не совпадают.');
                exit;
            }
        }

        $login->checkEmpty(array($id,$name,$role,$token,$tokenLS,$email));
        $login->checkToken($token,$tokenLS);

        $id=(int)decrypt_($id);
        $db=new db;

        $login->checkLastId($id);//check last user id

        if(!empty($password)){
            $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $sql = "UPDATE `flr_users` SET `name`=?, `role`=?, `comment`=?, `password`=? WHERE `id`=?";
            $stmt= $db->prepare($sql);
            $stmt->execute(array($name, $role, $comment, $password, $id));
        }else{
            $sql = "UPDATE `flr_users` SET `name`=?, `role`=?, `comment`=? WHERE `id`=?";
            $stmt= $db->prepare($sql);
            $stmt->execute(array($name, $role, $comment, $id));
        }

        echo $login->json('ok',"Данные пользователя {$name} обновлены.");
        exit;
    }
    public function deleteSingleUser(){
        $login=new loginme;
        if($login->isAdmin()!==true){
            $_SESSION['errors'] = 'ES002. Отказано в доступе';
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }

        $q=str_replace('&quot;','"',$_POST['data']);
        $id=clear($login->dataPost($q)['thisId']);
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);

        $login->checkEmpty(array($id,$token,$tokenLS));
        $login->checkToken($token,$tokenLS);

        $id=(int)decrypt_($id);
        $db=new db;

        $login->checkLastId($id);//check last user id

        $tokenUserSession=explode('*',decrypt_($_SESSION['userid']));//User ID (int) * IP
        if(empty($tokenUserSession[0])){
            echo $login->json('error','E040. \User. Возникла ошибка...');
            exit;
        }
        if($id==$tokenUserSession[0]){
            echo $login->json('error','Нельзя удалить самого себя.');
            exit;
        }
        $sql = "UPDATE `flr_users` SET `state`=? WHERE `id`=?";
        $stmt= $db->prepare($sql);
        $stmt->execute(array(0,$id));

        echo $login->json('ok',"+");
        exit;
    }

}