<?php
//Date: 29.02.2020 Time: 16:05
$values='';
/**
$dataArray set in index.php from $main->load
 */
foreach ($dataArray[0] as $k){
    $values.='<tr data-id="'. crypt_($k->id).'"><th>'.$k->id.'</th>';
    $values.='<td>'.$k->name.'</td>';
    $values.='<td><small>'.$k->date.'</small></td>';
    $state='';
    ($k->state==1)?$state='Действущий':$state='<span class="badge badge-danger">Недействущий</span>';
    $values.='<td><small>'.$state.'</small></td>';
    $values.='<td><div class=txt><small title="'.$k->comment.'">'.$k->comment.'</small></div></td>
<td>';
    $values.=' <button class="btn btn-info btn-sm getSupplier">Редактировать</button> <button class="btn btn-danger btn-sm removeSupplier">Удалить</button> </td>
</tr>';
}

$out.='
<main class="container">
    <div class="errorAll alert alert-danger" style="display:none"></div>
    <div class="row justify-content-md-center">
        <div class="col-md-auto ova">
            <div class="btn-group">
                <button class="addSupplier btn btn-dark">Добавить поставщика</button>
            </div>
            <table class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Имя</th>
                  <th>Дата</th>
                  <th>Статус</th>
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
        <h5 class="modal-title"></h5>
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
function removeScript(){
    if($("script").is(".remove"))$("script.remove").remove();
};

$(".close").on("click",()=>{
    $(".modal").hide();
    $(".modal-body").html("")
});

let editUser=$(".editUser");

function formEncode(obj){
    var str = [];
    for(let p in obj)
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    return str.join("&");
};

if (!window.fetch){$(".error").slideDown().text("Похоже, что Ваш браузер устарел. Зайдите сюда с нового браузера Google Chrome, Opera, YandexBrowser или FireFox")};

$(".removeSupplier").on("click",function(){
    $(".errorAll").slideUp(100);
    let confirm_=confirm("Вы действительно хотите удалить этого поставщика?");
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
            body:formEncode({set:"deleteSingleSupplier",data:JSON.stringify(form),location_:location.href})
        }).then(r=> r.json()
                .then(data => ({status: r.status, body: data}))
            ).then(
                obj=>{
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

$(".getSupplier").on("click",function() {
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
        body:formEncode({get:"getSupplier",data:JSON.stringify(form),location_:location.href})
    }).then(r=> r.json()
            .then(data => ({status: r.status, body: data}))
        ).then(
            obj=>{
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
                     $(".modal-title").text(`Редактирование поставщика ${response.idRaw}`)
                     $(".modal-body").html(`
                     <div class="col-md-auto">
            <div class="form-group text-left">
                <label>Имя *</label>
                <input type="text" maxlength="29" class="name form-control" value="${response.name}">
            </div>

            <div class="form-group">
                <label>Заметка</label>
                <textarea class="comment form-control" rows="3">${response.comment}</textarea>
            </div>            
            <div class="form-group">
                <label>Дата добавления</label>
                <input class="form-control" disabled value="${response.date}">
            </div>
            
            <div>Статус *</div>
            <div class="mb-2">
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio1" data-state="1" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio1">Действующий</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio2" data-state="0" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio2">Недействующий</label>
                </div>
            </div>
            
            <div class="ok alert alert-primary" role="alert" style="display:none"></div>
            <div class="error alert alert-danger" role="alert" style="display:none"></div>
            
            <input type="hidden" class="id" value="${response.id}">
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <p><small>После редактирования, обновите страницу, чтобы данные в таблице обновились.</small></p>
            
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-lg btn-block saveSingleSupplier">Сохранить</button>
      </div>
      `);
             if(response.state==1){
                 $("#customRadio1").attr("checked","")
             }
             if(response.state==0){
                 $("#customRadio2").attr("checked","")
             }
                 
                 $(".saveSingleSupplier").on("click",()=>{
                    $(".error,.ok").slideUp();                     
                    let form={};
                    $(".custom-control-input").each(function() {
                        if($(this).is(":checked")){
                            form.state=$(this).data("state");
                        }
                    });
                    
                    if(form.state!=0&&form.state!=1){
                        $(".error").slideDown().text("EF104. Необходимо выбрать статус поставщика.");
                        return false;
                    }
                    
                    
                    form.id=$(".id").val();
                    form.name=$(".name").val();
                    form.comment=$(".comment").val();
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
                        body:formEncode({set:"saveSingleSupplier",data:JSON.stringify(form),location_:location.href})
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
});

$(".addSupplier").on("click",()=>{
   $(".modal").slideDown(100);
   $(".modal-title").text(`Добавить нового поставщика`);
   $(".modal-body").html(`<div class="col-md-auto">
            <div class="form-group text-left">
                <label>Имя *</label>
                <input type="text" maxlength="29" class="name form-control">
            </div>

            <div class="form-group">
                <label>Заметка</label>
                <textarea class="comment form-control" rows="3"></textarea>
            </div>
            
            <div>Статус *</div>
            <div class="mb-2">
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio1" checked data-state="1" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio1">Действующий</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio2" data-state="0" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio2">Недействующий</label>
                </div>
            </div>
            
            <div class="ok alert alert-primary" role="alert" style="display:none"></div>
            <div class="error alert alert-danger" role="alert" style="display:none"></div>
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <p><small>После редактирования, обновите страницу, чтобы данные в таблице обновились.</small></p>
            
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-lg btn-block saveNewSingleSupplier">Сохранить</button>
      </div>
      `);
      
      $(".saveNewSingleSupplier").on("click",()=>{
      

$(".error,.ok").slideUp();                     
let form={};
$(".custom-control-input").each(function() {
    if($(this).is(":checked")){
        form.state=$(this).data("state");
    }
});

if(form.state!=0&&form.state!=1){
    $(".error").slideDown().text("EF104. Необходимо выбрать статус поставщика.");
    return false;
}

form.name=$(".name").val();
form.comment=$(".comment").val();
form.token=$(".token").val();

if(localStorage.flr_secret===undefined){
    $(".error").slideDown().text("EF102. Необходимо заново авторизироваться.");
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
    body:formEncode({set:"saveNewSingleSupplier",data:JSON.stringify(form),location_:location.href})
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

      
      })
      
});


</script>
';

$this->customScript($scr);