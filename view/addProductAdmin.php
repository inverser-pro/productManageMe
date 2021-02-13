<?php
$out.=
    '<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-auto">
            <div class="errorAll alert alert-danger" role="alert" style="display:none"></div>

            <h4>Добавить товар</h4>
            <div class="form-group">
                <label>Название *</label>
                <input minlength=2 maxlength="50" class="name form-control">
            </div>
            
            <div class="form-group">
                <label>Цена *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="price form-control">
            </div>
            
            <div class="form-group">
                <label>Оптовая цена</label>
                <input maxlength="20" type=number min=0 max=99999999 class="priceopt form-control">
            </div>
            
            <div class="form-group">
                <label>Количество *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="quantity form-control">
            </div>
            
            
            <div class="form-group fx fxac">
                <label>Поставщики и их цены</label>
                <button class="btn btn-success suppliers">Выбрать</button>
            </div>
            
            <div class=loadSuppliersInfo></div>
            
            <div class=form-group>
                <label>Дата поступления (сегодня или выберите)</label>
                <input type=text class="din form-control" value="'. date('Y-m-d h:i:s') .'">
            </div>
            
            <div class=form-group>
                <label>Годен до <small>(по умолчанию +6 месяцев, с сегодня)</small></label>
                <input type=text class="valid form-control" value="'. date('Y-m-d',strtotime(date('Y-m-d').'+6 month')) .'">
            </div>
            
            <div class="form-group">
                <label>Комментарий</label>
                <textarea class="comment form-control" rows="3" maxlength=2000></textarea>
            </div>
            
            <div>Состояние *</div>
            <div class="mb-2">
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio1" data-state="1" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio1">В наличии</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio2" data-state="0" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio2">Нет в наличии</label>
                </div>
            </div>
            
            <div class="ok alert alert-primary" role="alert" style="display:none"></div>
            <div class="error alert alert-danger" role="alert" style="display:none"></div>
            
        
            <div class="pr loading loadingCl w1 fx fxjc fxac"><button type="button" class="btn btn-primary btn-lg btn-block addSingleProduct">Сохранить</button></div>
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <input type=hidden value="'. crypt_($_SESSION['time_token']) .'" class="token">
            
        </div>
    </div>
</div>

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

$scr='
<script>

function removeScript(){
    if($("script").is(".remove"))$("script.remove").remove();
};

$(".close").on("click",()=>{
    $(".modal").hide();
    $(".modal-body").html("")
});

function formEncode(obj){
    var str = [];
    for(let p in obj)
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    return str.join("&");
};

if (!window.fetch){$(".errorAll").slideDown().text("Похоже, что Ваш браузер устарел. Зайдите сюда с нового браузера Google Chrome, Opera, YandexBrowser или FireFox")};

let fetchSend=false;
$(".addSingleProduct").on("click",()=>{
if(fetchSend===true){return false};
fetchSend=true;
let form={},loading=$(".loading");
$(".custom-control-input").each(function() {
  if($(this).is(":checked")){
    form.state=$(this).data("state")
  }
});

if($("input").is(".addSingleSupplierData")){
    form.suppliers={};
    $(".addSingleSupplierData").each(function(){
       form.suppliers[$(this).data("id")]=$(this).val();
    });
};

form.name=$(".name").val();
form.price=$(".price").val();
form.priceopt=$(".priceopt").val();
form.quantity=$(".quantity").val();
form.comment=$(".comment").val();
form.din=$(".din").val();
form.valid=$(".valid").val();
form.token=$(".token").val();
if(localStorage.flr_secret===undefined){
    $(".error").slideDown().text("EF107. Необходимо заново авторизироваться.");
    $(".ok").slideUp();
    return false;
}else{form.tokenLS=localStorage.flr_secret};
loading.removeClass("loadingCl");
fetch("/", {
    method: "POST",
    headers: {"Content-type":"application/x-www-form-urlencoded"},
    mode: "same-origin",
    credentials: "same-origin",
    cache: "no-cache",
    body:formEncode({set:"addSingleProductAdmin",data:JSON.stringify(form),location_:location.href})
}).then(r=> r.json()
        .then(data => ({status: r.status, body: data}))
    ).then(obj=>{
        loading.addClass("loadingCl");
        fetchSend=false;
            if(obj.status==200){
                let response=obj.body;
                if(response){
                 if(response.state=="ok"){
                     $(".name").val("");
                    $(".ok").slideDown().text(response.data);
                    $(".error").slideUp()
                 }
                 if(response.state=="error"){
                    $(".error").slideDown().text(response.data);
                    $(".ok").slideUp()
                 }
                }
            }
        }).catch(error => $(".errorAll").text("Ошибка:" + error).slideDown(100));
});
                
// The date picker (read the docs)
$(".din").pickadate({today: "Сегодня",
clear: "Очистить",
close: "Закрыть",
format: "yyyy-mm-dd",});
$(".valid").pickadate({today: "Сегодня",
clear: "Очистить",
close: "Закрыть",
format: "yyyy-mm-dd",});

$(".suppliers").on("click",()=>{
    let form={};
    if(localStorage.flr_secret===undefined){
        $(".error").slideDown().text("EF1031. Необходимо заново авторизироваться.");
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
        body:formEncode({get:"getAllSuppliers",data:JSON.stringify(form),location_:location.href})
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
                    };
                     $(".modal").slideDown(100);
                     $(".modal-title").text(`Данные о поставщиках`);
                     let html_="",suppliersUnpublishClass=suppliersUnpublishTxt="";
                     if(!response){$(".errorAll").slideDown(100).text("EF0120. Возникла ошибка.")};
                     response.forEach(function(e,r) {/*e-data,r-number | {id: "RlYvbHJIWHNKbGVySkpsdVFkSytvQT09", name: "Поставщик 5", date: "2020-02-29 20:42:12", comment: "Заметка 5", state: 0} 4*/
                         
                         
                        if(e.state==0){suppliersUnpublishClass=" supUnpublish";suppliersUnpublishTxt="<br><small><u>неактивен<\/u><\/small>"};
                         
html_+=`<div class="form-group fx fxac supplierDataInput${suppliersUnpublishClass}">
<div title="${e.name} / ${e.date} / ${e.comment}" class="t"><small>${e.name}</small>${suppliersUnpublishTxt}</div>
<input type=number class="addSingleSupplierData form-control" data-id="${e.id}">
</div>`;
                     });
                     $(".modal-body").html(`<small>При необходимости, укажите цену для каждого поставщика. Если данные оставить пустыми, то ничего не добавится.<\/small>${html_}<button class="btn btn-primary btn-lg btn-block addAllSuppliersData">Добавить<\/button>`);
                     $(".modal-body input").on("input keydown keypress",function() {
                       $(this).attr("value",$(this).val())
                     });
                     
                     $(".addAllSuppliersData").on("click",()=>{
                         $(".modal-body input").each(function(e,r) {
                           if($(r).val()==""){
                               $(this).parent("div.supplierDataInput").remove()
                           }
                         });
                         let htmlInputData="";
                         $(".modal-body .supplierDataInput").each(function(e,t) {
                           htmlInputData+=$(t).html()
                         });
                         $(".loadSuppliersInfo").html(htmlInputData);
                         $(".close").trigger("click");
                     });
                     
    if(response.state==1){$("#customRadio1").attr("checked","")};
    if(response.state==0){$("#customRadio2").attr("checked","")};
     
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
        };
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
    }).catch(error => $(".errorAll").text("Ошибка: ", error).slideDown(100));
});

</script>

<style>

.loading:before,.loading:after{content:"";position:absolute;z-index:2;top:0;right:0;bottom:0;left:0;background:rgba(255,255,255,.55)}
.loading:after{top:auto;right:auto;bottom:auto;left:auto;width:2rem;height:2rem;border-radius:50%;background:none;border:2px solid;border-color:transparent #666;animation:al ease .75s infinite}
@keyframes al {
    50%{transform: rotate(360deg)}
}
.loadingCl:before,.loadingCl:after{display:none}

.supUnpublish{opacity:.75;color:red}
</style>

';

$this->customScript($scr);
$this->script('picker');
$this->script('picker.date');
$this->css('default');
$this->css('default.date');