<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 29.02.2020
 * Time: 16:09
 */

namespace loginme;
use db;

class suppliersClass{
    public function begin($getAllFetch=NULL){
        $login=new loginme;
        if($login->isAdmin()!==true){
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }
        $query='WHERE `state`!=2';
        if($getAllFetch!==NULL){
            $query='WHERE `state`=1';
        }
        $db=new db;
        $stmt=$db->prepare("SELECT  `id`, `name`, `date`, `comment`,`state` FROM `flr_suppliers` $query");//0-off,1-on,2-del
        $stmt->execute();
        $data = $stmt->fetchAll($db::FETCH_CLASS);
        if($getAllFetch!==NULL){
            foreach($data as $k){$k->id=crypt_($k->id);}
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;
        }
        return $data;
    }

    public function getSupplier(){
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
        $stmt=$db->prepare("SELECT `id`,`name`,`date`,`state`,`comment` FROM `flr_suppliers` WHERE `id` = ?");
        $stmt->execute(array($id));
        $data = $stmt->fetchAll($db::FETCH_CLASS);
        if(!empty($data[0]->id)){
            $data[0]->idRaw=$data[0]->id;
            $data[0]->id=crypt_($data[0]->id);
        }else{
            echo $login->json('error','E016. \Suppliers. Данных нет.');
            exit;
        }
        echo json_encode($data[0],JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function saveSingleSupplier(){
        $login=new loginme;
        if($login->isAdmin()!==true){
            $_SESSION['errors'] = 'ES001. \Suppliers. Отказано в доступе';
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }
        $q=str_replace('&quot;','"',$_POST['data']);
        $id=clear($login->dataPost($q)['id']);
        $name=clear($login->dataPost($q)['name']);
        $comment=clear($login->dataPost($q)['comment']);
        $state=clear($login->dataPost($q)['state']);
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);

        $login->checkEmpty(array($id,$name,$token,$tokenLS));
        $login->checkToken($token,$tokenLS);

        $id=(int)decrypt_($id);
        $db=new db;

        if(abs((int)$state)!==0 AND abs((int)$state)!==1){
            echo $login->json('error','Необходимо выбрать статус поставщика.');
            exit;
        }

        $sql = "UPDATE `flr_suppliers` SET `name`=?, `comment`=?, `state`=? WHERE `id`=?";
        $stmt= $db->prepare($sql);
        $stmt->execute(array($name, $comment,$state, $id));

        echo $login->json('ok',"Данные поставщика \"{$name}\" обновлены.");
        exit;
    }

    public function saveNewSingleSupplier(){
        $login=new loginme;
        if($login->isAdmin()!=1){
            echo self::json('error','E055. \Suppliers. Нет прав доступа. Обратитесь к администратору ресурса.');
            exit;
        }

        $q=str_replace('&quot;','"',clear($_POST['data']));

        $name=$login->dataPost($q)['name'];
        $state=(string)$login->dataPost($q)['state'];
        $token=(string)$login->dataPost($q)['token'];
        $tokenLS=(string)$login->dataPost($q)['tokenLS'];
        $comment=(!empty($login->dataPost($q)['comment']))?$login->dataPost($q)['comment'] : '';

        $login->checkEmpty(array($name,$state,$token,$tokenLS));
        $login->checkToken($token,$tokenLS);

        if(isset($name) AND !empty($name)){
            if($login->length($name)<1){
                echo $login->json('error','Проверьте имя. Должно быть больше 1 символов.');
                exit;
            }
            if($login->length($name)>30){
                echo $login->json('error','Проверьте имя. Должно быть меньше 30-ти символов.');
                exit;
            }
        }

        $db=new db;
        $stmt=$db->prepare("SELECT `name` FROM `flr_suppliers` WHERE `name`=?");
        $stmt->execute(array( $name ));
        $data = $stmt->fetchAll();

        if(!empty($data)){
            echo $login->json('error','Такой поставщик уже есть в нашей базе.');
            exit;
        }else{
            $sql = "INSERT INTO `flr_suppliers`(`name`, `date`, `comment`, `state`) VALUES (?,NOW(),?,1)";
            $stmt= $db->prepare($sql);
            $stmt->execute(array($name, $comment));
            echo $login->json('ok',"Поставщик \"{$name}\" добавлен.",'$("input,textarea").val("")');
            exit;
        }
    }

    public function deleteSingleSupplier(){
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

        $sql = "UPDATE `flr_suppliers` SET `state`=? WHERE `id`=?";
        $stmt= $db->prepare($sql);
        $stmt->execute(array(2,$id));

        echo $login->json('ok',"+");
        exit;
    }

    public function allSuppliersNotIn(){
        $login=new loginme;
        if($login->isAdmin()!==true){
            header("HTTP/1.1 403 Forbidden");
            header("Location: /");
            exit;
        }
        $q=str_replace('&quot;','"',$_POST['data']);
        $ids=$login->dataPost($q)['ids'];/*all suppliers ids in single product*/
        $token=clear($login->dataPost($q)['token']);
        $login->checkEmpty(array($ids,$token));
        $login->checkToken($token);
        $idsNotIn='';
        foreach ($ids as $id) {$idsNotIn.=(int)decrypt_($id).',';}
        $idsNotIn=trim($idsNotIn,',');
        if(!empty($idsNotIn)){
            $idsNotIn=" AND `id` NOT IN ($idsNotIn)";
        }
        $db=new db;
        $stmt=$db->prepare("SELECT  `id`, `name` FROM `flr_suppliers` WHERE `state`=1 $idsNotIn");//0-off,1-on,2-del
        $stmt->execute();
        $data = $stmt->fetchAll($db::FETCH_CLASS);
        foreach($data as $k){$k->id=crypt_($k->id);}
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }
}