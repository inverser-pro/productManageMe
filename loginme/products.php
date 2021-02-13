<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 24.02.2020
 * Time: 21:11
 */

namespace loginme;
use db;

class products{

    public function getSingleProduct($role=NULL){/*For authorized users*/
        $login=new loginme;
        $login->isAuthorized();

        $q=str_replace('&quot;','"',$_POST['data']);
        $id=clear($login->dataPost($q)['thisId']);
        $token=clear($login->dataPost($q)['token']);
        $login->checkEmpty([$id,$token]);
        $login->checkToken($token);
        $id=(int)decrypt_($id);

        $db=new db;

        if($role!==NULL AND $role===0){//manager only
            $stmt=$db->prepare("SELECT `id`, `name`, `price`, `quantity`, `datecreate`, `state` FROM `flr_goods` WHERE `id`=? LIMIT 1");
        }else{//admin only
            $stmt=$db->prepare("SELECT `id`, `name`, `price`, `priceopt`, `quantity`, `comment`, `datecreate`, `who`, `state`, `din`, `suppliers`, `valid` FROM `flr_goods` WHERE `id`=? AND `state` != 2 LIMIT 1");
        }

        $stmt->execute(array($id));
        $data = $stmt->fetchAll($db::FETCH_CLASS);

        if(!empty($data[0]->id)){
            $data[0]->idInt=$data[0]->id;
            $data[0]->id=crypt_($data[0]->id);
            $data[0]->name=str_replace('**',',',$data[0]->name);
            $w='';
            if(!empty($data[0]->valid) AND $data[0]->valid!='0000-00-00'){
                $w=$data[0]->valid;
                $dateNow = strtotime(date('Y-m-d').'+3 month');
                $dateValid = strtotime($w);
                $data[0]->validDate=$data[0]->valid;
                if($dateValid < $dateNow)$data[0]->valid='<span class="badge badge-danger">'.$w.'</span>';
            }else{
                $data[0]->valid='';
                $data[0]->validDate=date('Y-m-d',strtotime(date('Y-m-d').'+3 month'));
            }

            if(!empty($data[0]->din) AND $data[0]->din=='0000-00-00'){$data[0]->din=date('Y-m-d');}


            $suppliersIds='';
            $suppliersPrices=[];
            if(!empty($data[0]->suppliers)){
                foreach(json_decode($data[0]->suppliers,true) as $k=>$v){
                    $suppliersIds.=$k.',';
                    $suppliersPrices[]=$v;
                }
                $suppliersIds=trim($suppliersIds,',');
                //var_dump($suppliersIds);exit;
                //,$suppliersPrices);
                $stmt=$db->prepare("SELECT `id`, `name`, `state` FROM `flr_suppliers` WHERE `state`!=2 AND `id` IN ($suppliersIds)");
                $stmt->execute();
                $dataSuppliers = $stmt->fetchAll($db::FETCH_ASSOC);
                $i=0;

                foreach ($dataSuppliers as $k){
                    $dataSuppliers[$i]['id']=crypt_($k['id']);
                    $dataSuppliers[$i]['price']=$suppliersPrices[$i];
                    $i++;
                }
                $data[0]->suppliers=$dataSuppliers;
            }
        }else{
            echo $login->json('error','E019. \Products. Данных нет.');
            exit;
        }
        echo json_encode($data[0],JSON_UNESCAPED_UNICODE);
        exit;

    }

    public function editSingleProduct(){
        $login=new loginme;
        $login->isAuthorized();

        $q=str_replace('&quot;','"',$_POST['data']);
        $id=clear($login->dataPost($q)['id']);
        $state=(string)clear($login->dataPost($q)['state']);
        $name=clear($login->dataPost($q)['name']);
        $price=(string)clear($login->dataPost($q)['price']);
        $priceopt=(string)clear($login->dataPost($q)['priceopt']);
        $quantity=(string)clear($login->dataPost($q)['quantity']);
        $comment=clear($login->dataPost($q)['comment']);
        $din=clear($login->dataPost($q)['din']);
        $valid='';
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);

        $suppliersjson='';
        if(!empty($login->dataPost($q)['suppliers'])){
            $suppliersjson=[];
            $suppliers=$login->dataPost($q)['suppliers'];
            $suppliers = json_decode(json_encode($suppliers), true);
            foreach ($suppliers as $k=>$v) {
                if((int)$v!=0)$suppliersjson[(int)decrypt_($k)]=(int)$v;
            }
            $suppliersjson=json_encode($suppliersjson);
        }
        if($suppliersjson=='[]')$suppliersjson='';

        $id=(int)decrypt_($id);
        if($state != '0' AND $state != '1'){
            echo $login->json('error',"E033. \Product. Ошибка.");
            exit;
        }

        $login->checkToken($token,$tokenLS);
        $login->checkEmpty([$id,$state,$name,$quantity,$price,$din,$token,$tokenLS]);
        $db=new db;

        if(!empty(clear($login->dataPost($q)['valid']))){
            $valid=clear($login->dataPost($q)['valid']);
        }else{
            $valid=date('Y-m-d',strtotime(date('Y-m-d').'+3 month'));
        }

        $sql = "UPDATE `flr_goods` SET `name`=?, `price`=?, `priceopt`=?, `quantity`=?, `comment`=?, `state`=?, `valid`=?, `suppliers`=?, `din`=? WHERE `id`=?";
        $stmt= $db->prepare($sql);
        $stmt->execute(array($name, $price, $priceopt, $quantity, $comment, $state, $valid, $suppliersjson, $din, $id));
        /*}else{
            $sql = "UPDATE `flr_goods` SET `name`=?, `price`=?, `priceopt`=?, `quantity`=?, `comment`=?, `state`=? WHERE `id`=?";
            $stmt= $db->prepare($sql);
            $stmt->execute(array($name, $price, $priceopt, $quantity, $comment, $state, $id));
        }*/

        echo $login->json('ok',"Данные товара \"{$name}\" обновлены.");
        exit;
    }

    public function deleteSingleProduct(){/*ADMIN ONLY*/
        $login=new loginme;
        $login->isAuthorized();
        if($login->isAdmin()===0){
            echo $login->json('error',"E050. \Products. Нет прав доступа.");
            exit;
        }
        $q=str_replace('&quot;','"',$_POST['data']);
        $id=clear($login->dataPost($q)['id']);
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);
        $login->checkEmpty(array($id,$token,$tokenLS));
        $login->checkToken($token,$tokenLS);
        $id=(int)decrypt_($id);
        $db=new db;
        $sql = "UPDATE `flr_goods` SET `state`=? WHERE `id`=?";
        $stmt= $db->prepare($sql);
        $stmt->execute(array(2,$id));
        echo $login->json('ok',"Товар ID `{$id}` удален.");
        exit;
    }

    public function addSingleProductAdmin(){
        $login=new loginme;
        $login->isAuthorized();
        if($login->isAdmin()===0){
            echo $login->json('error',"E051. \Products. Нет прав доступа.");
            exit;
        }
        $q=str_replace('&quot;','"',$_POST['data']);
        $state=(string)clear($login->dataPost($q)['state']);
        $name=clear($login->dataPost($q)['name']);
        $price=(int)clear($login->dataPost($q)['price']);
        $priceopt=(string)clear($login->dataPost($q)['priceopt']);
        $quantity=(int)clear($login->dataPost($q)['quantity']);
        $comment=clear($login->dataPost($q)['comment']);
        $din=clear($login->dataPost($q)['din']);//date in
        $valid=clear($login->dataPost($q)['valid']);//date годен до
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);
        $suppliersjson='';
        if(!empty($login->dataPost($q)['suppliers'])){
            $suppliersjson=[];
            $suppliers=$login->dataPost($q)['suppliers'];
            $suppliers = json_decode(json_encode($suppliers), true);
            foreach ($suppliers as $k=>$v) {
                $suppliersjson[(int)decrypt_($k)]=(int)$v;
            }
            $suppliersjson=json_encode($suppliersjson);
        }
        $login->checkEmpty([$state,$name,$quantity,$price,$token,$tokenLS]);
        if($state != '0' AND $state != '1'){
            echo $login->json('error',"E034. \Product. Ошибка.");
            exit;
        }
        if($login->length($name)<3 OR $login->length($name)>51){
            echo $login->json('error',"E035. \Product. Проверьте название. Должно быть более 2-х и менее 50-ти символов.");
            exit;
        }
        if($login->length($price)<2 OR $login->length($price)>10){
            echo $login->json('error',"E036. \Product. Проверьте цену. Должна быть более 2-х и менее 11-ти символов.");
            exit;
        }
        if($login->length($quantity)<1 OR $login->length($quantity)>10){
            echo $login->json('error',"E037. \Product. Проверьте количество. Не должно быть пустым и не более 11-ти символов.");
            exit;
        }
        if(!empty($priceopt) AND $login->length($priceopt)<3 OR $login->length($priceopt)>10){
            echo $login->json('error',"E038. \Product. Проверьте оптовую цену. Должна быть более 2-х и менее 11-ти символов.");
            exit;
        }
        if(!empty($comment) AND $login->length($comment)<3 OR $login->length($comment)>2001){
            echo $login->json('error',"E039. \Product. Проверьте комментарий. Должен быть более 2-х и менее 2000 символов.");
            exit;
        }

        //user ID
        if(!isset($_SESSION['userid']) OR empty($_SESSION['userid'])){echo $login->json('error',"E060. \Product. Необходимо заново авторизироваться.");exit;}
        $tokenUserSession=explode('*',decrypt_($_SESSION['userid']));//User ID (int) * IP
        $userId=$tokenUserSession[0];

        $login->checkToken($token,$tokenLS);
        $db=new db;
        $sql = "INSERT INTO `flr_goods`(`name`, `price`, `priceopt`, `quantity`, `comment`, `datecreate`, `who`, `state`,`suppliers`,`din`,`valid`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmt= $db->prepare($sql);
        $stmt->execute(array($name, $price, $priceopt, $quantity, $comment,date('Y-m-d h:i:s', time()), $userId,$state,$suppliersjson,$din,$valid));
        echo $login->json('ok',"Товар \"{$name}\" создан.");
        exit;
    }

    public function addSingleProductManager(){
        $login=new loginme;
        $login->isAuthorized();
        $q=str_replace('&quot;','"',$_POST['data']);
        $state=(string)clear($login->dataPost($q)['state']);
        $name=clear($login->dataPost($q)['name']);
        $price=(int)clear($login->dataPost($q)['price']);
        $quantity=(int)clear($login->dataPost($q)['quantity']);
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);
        $login->checkEmpty([$state,$name,$quantity,$price,$token,$tokenLS]);
        if($state != '0' AND $state != '1'){
            echo $login->json('error',"E034. \Product. Ошибка.");
            exit;
        }
        if($login->length($name)<3 OR $login->length($name)>51){
            echo $login->json('error',"E035. \Product. Проверьте название. Должно быть более 2-х и менее 50-ти символов.");
            exit;
        }
        if($login->length($price)<2 OR $login->length($price)>10){
            echo $login->json('error',"E036. \Product. Проверьте цену. Должна быть более 2-х и менее 11-ти символов.");
            exit;
        }
        if($login->length($quantity)<1 OR $login->length($quantity)>10){
            echo $login->json('error',"E037. \Product. Product. Проверьте количество. Не должно быть пустым и не более 11-ти символов.");
            exit;
        }
        //user ID
        $tokenUserSession=explode('*',decrypt_($_SESSION['userid']));//User ID (int) * IP
        $userId=$tokenUserSession[0];

        $login->checkToken($token,$tokenLS);
        $db=new db;
        $sql = "INSERT INTO `flr_goods`(`name`, `price`, `quantity`, `datecreate`, `who`, `state`) VALUES (?,?,?,NOW(),?,?)";
        $stmt= $db->prepare($sql);
        $stmt->execute(array($name, $price, $quantity, $userId, $state));
        echo $login->json('ok',"Товар \"{$name}\" создан.");
        exit;
    }

    public function addSingleSale(){

        /**
         * TODO:
         * 1. Узнать какое количество сейчас.
         * 2. Проверить количество +/-.
         * 3. Отнять количество продажи.
         * 4. Обновить количество в конкретном товаре.
        */

        $login=new loginme;
        $login->isAuthorized();
        $q=str_replace('&quot;','"',$_POST['data']);
        $id=abs((int)clear(decrypt_($login->dataPost($q)['id'])));/*Product ID*/
        $date=(string)clear($login->dataPost($q)['date']);
        $quantity=(int)clear($login->dataPost($q)['quantity']);
        $text=(string)clear($login->dataPost($q)['text']);
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);
        $login->checkEmpty([$id,$date,$quantity,$token,$tokenLS]);
        $login->checkToken($token,$tokenLS);
        if($quantity<1 OR $quantity>99999){echo $login->json('error',"E062. \Product. Неполадки с количеством. Должно быть больше 1 шутки и меньше 99999.");exit;}
        if($login->length($date)!=10){echo $login->json('error',"E062. \Product. Неполадки с датой.");exit;}
        if($login->length($text)>2000){echo $login->json('error',"E061. \Product. Текст описания продажи должен быть меньше 2000 символов.");exit;}
        $tokenUserSession=explode('*',decrypt_($_SESSION['userid']));//User ID (int) * IP
        $userId=$tokenUserSession[0];
        $db=new db;
/**/
        $msg='Данные добавлены. Количество товара (всего) обновлено.';
        $forUpdate=true;
        $difference='';
        $stmt=$db->prepare("SELECT `quantity` FROM `flr_goods` WHERE `id`=? AND `state`!=2 LIMIT 1");
        $stmt->execute(array($id));
        $data = $stmt->fetch();
        if(!empty($data)){
            $difference=$data['quantity']-$quantity;
            if($data['quantity']==0){
                $msg='Данные добавлены, но количество товара (всего) равно 0! Вы продали сейчас: '.$quantity;
                $forUpdate=false;
            }
            if($difference<1){
                $msg="Данные добавлены, но количество товара (всего: '{$data['quantity']}') меньше, чем Вы продали сейчас: '{$quantity}'. Количество товара (всего) установлено: 0. Состояние переведено в \"Нет в наличии\"";
                $difference=0;
            }
            if($forUpdate){
                if($difference!=0){
                    $sql = "UPDATE `flr_goods` SET `quantity`=? WHERE `id`=? AND `state`!=2 LIMIT 1";
                    $stmt= $db->prepare($sql);
                    $stmt->execute(array($difference, $id));
                }else{
                    $sql = "UPDATE `flr_goods` SET `quantity`=?,`state`=0 WHERE `id`=? AND `state`!=2 LIMIT 1";
                    $stmt= $db->prepare($sql);
                    $stmt->execute(array($difference, $id));
                }
            }

        }else{
            echo $login->json('nodata','Данных нет, возможно, товар удален. <small style="color:#ccc">ErrBackForJS_002</small>');
            exit;
        }
/**/


        $sql = "INSERT INTO `flr_sales`(`pid`, `date`, `uid`, `quantity`, `dateadd`, `text`) VALUES (?,?,?,?,?,?)";
        $stmt= $db->prepare($sql);
        $stmt->execute(array($id, $date, $userId, $quantity, date('Y-m-d h:i:s', time()),$text));
        echo $login->json('ok',$msg);
        exit;
    }

    public function allSalesSingleProduct(){
        $login=new loginme;
        $login->isAuthorized();
        $q=str_replace('&quot;','"',$_POST['data']);
        $id=abs((int)clear(decrypt_($login->dataPost($q)['id'])));
        $token=clear($login->dataPost($q)['token']);
        $tokenLS=clear($login->dataPost($q)['tokenLS']);
        $login->checkEmpty([$id,$token,$tokenLS]);
        $login->checkToken($token,$tokenLS);
        $db=new db;
        $stmt=$db->prepare("SELECT `id`, `date`, `uid`, `quantity`, `dateadd`, `text`, `state` FROM `flr_sales` WHERE `pid`=?");
        $stmt->execute(array($id));
        $data = $stmt->fetchAll($db::FETCH_CLASS);
        if(!empty($data)){
            foreach($data as $k){
                $k->id=crypt_($k->id);
            }
        }else{
            echo $login->json('nodata','Данных нет. <small style="color:#ccc">ErrBackForJS_001</small>');
            exit;
        }
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;

        //






    }
}