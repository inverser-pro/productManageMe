<?php

$values='';

foreach ($dataArray as $k){
    if($k->state!==2){
        $is='';
        $k->name=str_ireplace('**',',',$k->name);

        ($k->state===1)?$is='ok':$is='<span class=\"badge badge-warning\">нет в наличии<\/span>';
        $q=$k->quantity;
        if($k->quantity===0)$q='<span class=\"badge badge-danger\">отсутствует<\/span>';

        $values.='{id:"'.$k->id.'",name:"'.$k->name.'",price:"'.$k->price.'",quantity:"'.$q.'",datecreate:"'.$k->datecreate.'",who:"'.$k->who.'",state:"'.$is.'",management:"<div class=\"fx\"><button class=\"btn btn-sm blue-gradient btn-rounded\"  onclick=\"e(\''.crypt_($k->id).'\')\"><i class=\"fa fa-pen\"><\/i><\/button><\/div>"},';
    }
}

$values=trim($values,',');

$out.='
<div class="container">
    <div class="row">
    <div class="allok alert alert-primary" style="display:none"></div>
    <div class="allerror alert alert-danger" style="display:none"></div>
        <div id=app></div>
        <link href=/sys/css/chunk-vendors.4e206b88.css rel=stylesheet>
    </div>
</div>

<div class="modal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Редактирование товара</h5>
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
(function(e){function t(t){for(var n,l,i=t[0],c=t[1],u=t[2],s=0,d=[];s<i.length;s++)l=i[s],Object.prototype.hasOwnProperty.call(a,l)&&a[l]&&d.push(a[l][0]),a[l]=0;for(n in c)Object.prototype.hasOwnProperty.call(c,n)&&(e[n]=c[n]);f&&f(t);while(d.length)d.shift()();return o.push.apply(o,u||[]),r()}function r(){for(var e,t=0;t<o.length;t++){for(var r=o[t],n=!0,i=1;i<r.length;i++){var c=r[i];0!==a[c]&&(n=!1)}n&&(o.splice(t--,1),e=l(l.s=r[0]))}return e}var n={},a={app:0},o=[];function l(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,l),r.l=!0,r.exports}l.m=e,l.c=n,l.d=function(e,t,r){l.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},l.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,t){if(1&t&&(e=l(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(l.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)l.d(r,n,function(t){return e[t]}.bind(null,n));return r},l.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return l.d(t,"a",t),t},l.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},l.p="/";var i=window["webpackJsonp"]=window["webpackJsonp"]||[],c=i.push.bind(i);i.push=t,i=i.slice();for(var u=0;u<i.length;u++)t(i[u]);var f=c;o.push([0,"chunk-vendors"]),r()})({0:function(e,t,r){e.exports=r("56d7")},"56d7":function(e,t,r){"use strict";r.r(t);r("cadf"),r("551c"),r("f751"),r("097d"),r("becf"),r("cabf"),r("3c76");var n=r("2b0e"),a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("mdb-datatable",{attrs:{data:e.data,striped:"",bordered:""}})},o=[],l=r("91c9"),i={name:"DatatablePage",components:{mdbDatatable:l["mdbDatatable"]},data:function(){return{data:{columns:[{label:"id",field:"id",sort:"asc"},{label:"Имя",field:"name",sort:"asc"},{label:"Цена",field:"price",sort:"asc"},{label:"Количество",field:"quantity",sort:"asc"},{label:"Добавлен",field:"datecreate",sort:"asc"},{label:"Добавил",field:"who",sort:"asc"},{label:"Состояние",field:"state",sort:"asc"},{label:"Управление",field:"management"}],rows:['. $values .']}}}},c=i,u=r("2877"),f=Object(u["a"])(c,a,o,!1,null,null,null),s=f.exports;n["a"].config.productionTip=!1,new n["a"]({render:function(e){return e(s)}}).$mount("#app")}});

function removeScript(){
    if($("script").is(".remove"))$("script.remove").remove();
};

if (!window.fetch){$(".error").slideDown().text("Похоже, что Ваш браузер устарел. Зайдите сюда с нового браузера Google Chrome, Opera, YandexBrowser или FireFox")};

function formEncode(obj){
    var str = [];
    for(let p in obj)
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    return str.join("&");
};

$(".close").on("click",()=>{
    $(".modal").slideUp(100);
    $("body").removeClass("ovh");
});

function e(id){/*edit product*/
    let form={};
    form.thisId=id;
    if(localStorage.flr_secret===undefined){
        $(".error").slideDown().text("EF104. Необходимо заново авторизироваться.");
        $(".ok").slideUp();
        return false;
    }
    form.token=$(".token").val();
    fetch("/", {
        method: "POST",
        headers: { "Content-type": "application/x-www-form-urlencoded"},
        mode: "same-origin",
        credentials: "same-origin",
        cache: "no-cache",
        body:formEncode({get:"singleProductManager",data:JSON.stringify(form),location_:location.href})
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
                     $("body").addClass("ovh");
                     $(".modal").slideDown(100);
                     $(".modal-body").html(`
                     <div class="col-md-auto">
                     <span class="badge badge-dark">ID товара: ${response.idInt}</span>
            <div class="form-group">
                <label>Название *</label>
                <input maxlength="50" class="name form-control" value="${response.name}">
            </div>
            
            <div class="form-group">
                <label>Цена *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="price form-control" value="${response.price}">
            </div>
            
            <div class="form-group">
                <label>Количество *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="quantity form-control" value="${response.quantity}">
            </div>
            
            <div class=mb><small>Дата создания: ${response.datecreate}</small></div>
            
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
            
            <input type="hidden" class="id" value="${response.id}">
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <p><small>После редактирования, при необходимости, обновите страницу, чтобы данные в таблице с товарами обновились.</small></p>
            
        </div>
      <div class="modal-footer">
        <div class="pr loading loadingCl w1 fx fxjc fxac"><button type="button" class="btn btn-primary btn-lg btn-block saveSingleProduct">Сохранить</button></div>
      </div>
      `);
                 /*From first fetch*/
                 if(response.state==1){
                     $("#customRadio1").attr("checked","")
                 }
                 if(response.state==0){
                     $("#customRadio2").attr("checked","")
                 }
                 /* \ */
let fetchSend=false;

                 $(".saveSingleProduct").on("click",()=>{
                     
                    if(fetchSend===true){return false};
                    fetchSend=true;
                     
                    $(".error,.ok").slideUp();                       
                    let form={},loading=$(".loading");
                    
                    $(".custom-control-input").each(function(){
                        if($(this).is(":checked")){
                            form.state=$(this).data("state")
                        }
                    });
                    if(form.state===undefined){
                        $(".error").slideDown(100).text("E103. Проверьте состояние товара.")
                    }
                    
                    form.id=$(".id").val();
                    form.name=$(".name").val();
                    form.price=$(".price").val();
                    form.priceopt=$(".priceopt").val();
                    form.quantity=$(".quantity").val();
                    form.comment=$(".comment").val();
                    form.token=$(".token").val();
                    
                    if(localStorage.flr_secret===undefined){
                        $(".error").slideDown().text("EF101. Необходимо заново авторизироваться.");
                        $(".ok").slideUp();
                        return false;
                    }else{form.tokenLS=localStorage.flr_secret};
                    
                    loading.removeClass("loadingCl")

                    fetch("/", {
                        method: "POST",
                        headers: { "Content-type": "application/x-www-form-urlencoded"},
                        mode: "same-origin",
                        credentials: "same-origin",
                        cache: "no-cache",
                        body:formEncode({set:"editSingleProduct",data:JSON.stringify(form),location_:location.href})
                    }).then(r=> r.json()
                            .then(data => ({status: r.status, body: data}))
                        ).then(obj=>{
                            fetchSend=false;
                            loading.addClass("loadingCl");
                            if(obj.status==200){
                                let response=obj.body;
                                if(response){
                                removeScript();
                                if(response.script!==undefined){
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
};
</script>

<style>
.loading:before,.loading:after{content:"";position:absolute;z-index:2;top:0;right:0;bottom:0;left:0;background:rgba(255,255,255,.55)}
.loading:after{top:auto;right:auto;bottom:auto;left:auto;width:2rem;height:2rem;border-radius:50%;background:none;border:2px solid;border-color:transparent #666;animation:al ease .75s infinite}
@keyframes al {
    50%{transform: rotate(360deg)}
}
.loadingCl:before,.loadingCl:after{display:none}

</style>

';

$this->script('chunk-vendors.ed32299e');
$this->customScript($scr);
$this->css('chunk-vendors.4e206b88');