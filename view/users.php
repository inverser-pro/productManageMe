<?php
$values='';
/**
$dataArray set in index.php from $main->load
 */
foreach ($dataArray[0] as $k){
    $values.='<tr data-id="'. crypt_($k->id).'"><th>'.$k->id.'</th>';
    $values.='<td>'.$k->name.'</td>';
    $values.='<td><div class=txt><small title="'.$k->email.'">'.$k->email.'</small></div></td>';
    $values.='<td><small>'.$k->dateCreate.'</small></td>';
    $role=$k->role;
    if($role==0){$role='Продавец';}
    if($role==1){$role='Админ';}
    $values.='<td><small>'.$role.'</small></td>';

    $values.='<td><div class=txt><small title="'.$k->comment.'">'.$k->comment.'</small></div></td>
<td> <button class="btn btn-info btn-sm editUser">Редактировать</button> <button class="btn btn-danger btn-sm removeUser">Удалить</button> </td>
</tr>';
}

$out.='<main class="container">
    <div class="errorAll alert alert-danger" style="display:none"></div>
    <div class="row justify-content-md-center">
        <div class="col-md-auto ova">
            
            <table class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Имя</th>
                  <th>Email</th>
                  <th>Дата регистрации</th>
                  <th>Роль</th>
                  <th>Комментарий</th>
                  <th>Действия</th>
                </tr>
              </thead>
              <tbody>
                '.$values.'
              </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Редактирование пользователя</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Загрузка...</p>
      </div>
        <input type="hidden" value="'. crypt_($_SESSION['time_token']) .'" class="token">
    </div>
  </div>
</div>
';

$scr='<script>
let fetchSendDelete=fetchSendEdit=false;

function removeScript(){
    if($("script").is(".remove"))$("script.remove").remove();
};

$(".close").on("click",()=>{
    $(".modal").hide()
});

let editUser=$(".editUser");

function formEncode(obj){
    var str = [];
    for(let p in obj)
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    return str.join("&");
};

if (!window.fetch){$(".error").slideDown().text("Похоже, что Ваш браузер устарел. Зайдите сюда с нового браузера Google Chrome, Opera, YandexBrowser или FireFox")};

$(".removeUser").on("click",function(){
if(fetchSendDelete===true){return false};
fetchSendDelete=true;
    $(".errorAll").slideUp(100);
    let confirm_=confirm("Вы действительно хотите удалить этого пользователя?");
    if(confirm_){
        let form={};
        form.thisId=$(this).parents("tr").attr("data-id");
        if(localStorage.flr_secret===undefined){
            alert("EF102. Необходимо заново авторизироваться...");
            return false;
        };
        if(localStorage.flr_secret!==undefined)form.tokenLS=localStorage.flr_secret;
        form.token=$(".token").val();
        fetch("/", {
            method: "POST",
            headers: { "Content-type": "application/x-www-form-urlencoded"},
            mode: "same-origin",
            credentials: "same-origin",
            cache: "no-cache",
            body:formEncode({set:"deleteSingleUser",data:JSON.stringify(form),location_:location.href})
        }).then(r=> r.json()
                .then(data => ({status: r.status, body: data}))
            ).then(
                obj=>{
                fetchSendDelete=true;
                    if(obj.status==200){
                            let response=obj.body;
                            if(response.state=="error"){
                                $(".errorAll").slideDown(100).html(`${response.data}`);
                                return false;
                            }
                            if(response){
                                $("tr[data-id="+form.thisId+"]").slideUp(100);
                            }
                        }
                    })
    }
});

editUser.on("click",function() {
if(fetchSendEdit===true){return false};
fetchSendEdit=true;
    let form={};
    form.thisId=$(this).parents("tr").attr("data-id");
    if(localStorage.flr_secret===undefined){
        $(".error").slideDown().text("EF101. Необходимо заново авторизироваться.");
        $(".ok").slideUp();
        return false;
    }
    if(localStorage.flr_secret!==undefined)form.tokenLS=localStorage.flr_secret;
    fetch("/", {
        method: "POST",
        headers: { "Content-type": "application/x-www-form-urlencoded"},
        mode: "same-origin",
        credentials: "same-origin",
        cache: "no-cache",
        body:formEncode({get:"singleUser",data:JSON.stringify(form),location_:location.href})
    }).then(r=> r.json()
            .then(data => ({status: r.status, body: data}))
        ).then(
            obj=>{
                fetchSendEdit=false;
                if(obj.status==200){
                    let response=obj.body;
                    if(response){
                    removeScript();
                    if(response.script!=undefined){
                        $("body").append(`<script class=remove>${response.script}<\/script>`);
                    } 
                    if(response.state=="error"){
                     $(".modal-body").html(`${response.data}`);
                     $(".modal").slideDown(100);
                    return false;
                    }
                     
                     $(".modal").slideDown(100);
                     $(".modal-body").html(`
                     <div class="col-md-auto">
            <div class="form-group text-left">
                <label for="name">Имя *</label>
                <input type="text" maxlength="29" id="name" class="form-control" value="${response.name}">
            </div>
            <div class="form-group text-left">
                <label for="email">Email</label>
                <br><small>Email нельзя изменить. Если это необходимо, то создайте нового пользователя...</small>
                <div class="fx fxac">
                    <input type="text" minlength="5" maxlength="50" id="email" class="form-control" disabled value="${response.email}">
                </div>
            </div>
            <div class="form-group text-left">
                <label for="password">Пароль</label> <button class="btn btn-secondary btn-sm showPassword">Показать пароли</button>
                <br><small>Если необходимо изменить, заполните это поле и подтвердите ниже.</small>
                <input type="password" maxlength="30" id="password" class="form-control">
            </div>            
            <div class="form-group text-left">
                <label for="passwordConfirm">Повторите пароль</label>
                <input type="password" maxlength="30" id="passwordConfirm" class="form-control">
            </div>            
            <div class="form-group">
                <label for="comment">Заметка</label>
                <textarea class="form-control" id="comment" rows="3">${response.comment}</textarea>
            </div>            
            <div class="form-group">
                <label for="date">Дата регистрации</label>
                <input class="form-control" id="date" disabled value="${response.dateCreate}">
            </div>
            <div>Уровень доступа *</div>
            <div class="mb-2">
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio1" data-role="1" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio1">Администратор</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio2" data-role="0" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio2">Продавец</label>
                </div>
            </div>
            
            <div class="ok alert alert-primary" role="alert" style="display:none"></div>
            <div class="error alert alert-danger" role="alert" style="display:none"></div>
            
            <input type="hidden" class="id" value="${response.id}">
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <p><small>После редактирования, обновите страницу, чтобы данные в таблице с пользователями обновились.</small></p>
            
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-lg btn-block saveSingleUser">Сохранить</button>
      </div>
      `);
                     
                 if(response.role==1){
                     $("#customRadio1").attr("checked","")
                 }
                 if(response.role==0){
                     $("#customRadio2").attr("checked","")
                 }
                 let shwPwd=false;
                 $(".showPassword").on("click",function(){
                   if(!shwPwd){
                       $(this).text("Скрыть пароли");
                       $("#password,#passwordConfirm").attr("type","text");
                       shwPwd=true;
                   }else{
                       $(this).text("Показать пароли");
                       $("#password,#passwordConfirm").attr("type","password");
                       shwPwd=false;
                   }
                 })
                 
                 $(".saveSingleUser").on("click",()=>{
                    $(".error,.ok").slideUp();                         
                    if($("#password").val().trim()!=""){
                     if($("#password").val().trim()!=$("#passwordConfirm").val().trim()){
                        $(".error").slideDown().text("Пароли не совпадают");
                        $(".ok").slideUp()
                        return false;
                     }
                    }                         
                    let getRole="",form={};
                    
                    $(".custom-control-input").each(function() {
                        if($(this).is(":checked")){
                            getRole=$(this).data("role")
                        }
                    });
                    
                    form.role=getRole;
                    form.id=$(".id").val();
                    form.name=$("#name").val();
                    form.password=$("#password").val().trim();
                    form.password2=$("#passwordConfirm").val().trim();
                    form.email=$("#email").val();
                    form.comment=$("#comment").val();
                    form.token=$(".token").val();
                    
                    if(localStorage.flr_secret===undefined){
                        $(".error").slideDown().text("EF101. Необходимо заново авторизироваться.");
                        $(".ok").slideUp();
                        return false;
                    }
                    if(localStorage.flr_secret!==undefined)form.tokenLS=localStorage.flr_secret;
                    
                    fetch("/", {
                        method: "POST",
                        headers: { "Content-type": "application/x-www-form-urlencoded"},
                        mode: "same-origin",
                        credentials: "same-origin",
                        cache: "no-cache",
                        body:formEncode({set:"editSingleUser",data:JSON.stringify(form),location_:location.href})
                    }).then(r=> r.json()
                            .then(data => ({status: r.status, body: data}))
                        ).then(obj=>{
                                if(obj.status==200){
                                    let response=obj.body;
                                    if(response){
                                    removeScript();
                                    if(response.script!=undefined){
                                        $("body").append(`<script class="remove">${response.script}<\/script>`);
                                    }
                                     if(response.state=="ok"){
                                        $(".ok").slideDown().text(response.data);
                                        $(".error").slideUp();
                                     }
                                     if(response.state=="error"){
                                        $(".error").slideDown().text(response.data);
                                        $(".ok").slideUp()
                                     }
                                    }
                                }
                            }).catch(error => console.error("Ошибка: ", error));
                        
                     });                     
                    }
                }
            }
            ).catch(error => console.error("Ошибка: ", error));
    

    
})

</script>
';

$this->customScript($scr);