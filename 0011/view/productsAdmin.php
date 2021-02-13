<?php

$values='';
foreach ($dataArray as $k){
    if($k->state!==2){
        $is='';
        $k->name=str_ireplace('**',',',$k->name);

        ($k->state===1)?$is='ok':$is='<span class=\"badge badge-warning\">нет в наличии<\/span>';
        $q=$k->quantity;
        $w=$k->valid;
        if($k->quantity===0)$q='<span class=\"badge badge-danger\">отсутствует<\/span>';

        if(!empty($w) AND $w!='0000-00-00'){
            $dateNow = strtotime(date('Y-m-d').'+3 month');
            $dateValid = strtotime($w);
            if ($dateValid < $dateNow)$w= '<span class=\"badge badge-danger\">'.$w.'<\/span>';
        }else{
            $w='';
        }

        $values.='{id:"'.$k->id.'",name:"'.$k->name.'",price:"'.$k->price.'",priceopt:"'.$k->priceopt.'",quantity:"'.$q.'",comment:"<div class=\"txt\"><small title=\"'.$k->comment.'\">'.$k->comment.'<\/small><\/div>",datecreate:"'.$k->datecreate.'",who:"'.$k->who.'",state:"'.$is.'",valid:"'.$w.'",management:"<div class=\"fx\"><button class=\"btn btn-sm blue-gradient btn-rounded\"  onclick=\"e(\''.crypt_($k->id).'\')\"><i class=\"fa fa-pen\"><\/i><\/button><button onclick=\"d(\''.crypt_($k->id).'\',$(this).parents(\'tr\').index())\" class=\"btn btn-sm purple-gradient btn-rounded\"><i class=\"fa fa-trash\"><\/i><\/button><\/div>"},';
    }
}

$values=trim($values,',');

$out.='
<div class="container">
    <div class="row">
    <div class="allok alert alert-primary" style="display:none"></div>
    <div class="allerror alert alert-danger" style="display:none"></div>
        <div id=app></div>
        
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
(function(e){function t(t){for(var n,l,i=t[0],c=t[1],u=t[2],s=0,d=[];s<i.length;s++)l=i[s],Object.prototype.hasOwnProperty.call(a,l)&&a[l]&&d.push(a[l][0]),a[l]=0;for(n in c)Object.prototype.hasOwnProperty.call(c,n)&&(e[n]=c[n]);f&&f(t);while(d.length)d.shift()();return o.push.apply(o,u||[]),r()}function r(){for(var e,t=0;t<o.length;t++){for(var r=o[t],n=!0,i=1;i<r.length;i++){var c=r[i];0!==a[c]&&(n=!1)}n&&(o.splice(t--,1),e=l(l.s=r[0]))}return e}var n={},a={app:0},o=[];function l(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,l),r.l=!0,r.exports}l.m=e,l.c=n,l.d=function(e,t,r){l.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},l.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,t){if(1&t&&(e=l(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(l.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)l.d(r,n,function(t){return e[t]}.bind(null,n));return r},l.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return l.d(t,"a",t),t},l.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},l.p="/";var i=window["webpackJsonp"]=window["webpackJsonp"]||[],c=i.push.bind(i);i.push=t,i=i.slice();for(var u=0;u<i.length;u++)t(i[u]);var f=c;o.push([0,"chunk-vendors"]),r()})({0:function(e,t,r){e.exports=r("56d7")},"56d7":function(e,t,r){"use strict";r.r(t);r("cadf"),r("551c"),r("f751"),r("097d"),r("becf"),r("cabf"),r("3c76");var n=r("2b0e"),a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("mdb-datatable",{attrs:{data:e.data,striped:"",bordered:""}})},o=[],l=r("91c9"),i={name:"DatatablePage",components:{mdbDatatable:l["mdbDatatable"]},data:function(){return{data:{columns:[{label:"id",field:"id",sort:"asc"},{label:"Имя",field:"name",sort:"asc"},{label:"Цена",field:"price",sort:"asc"},{label:"Опт цена",field:"priceopt",sort:"asc"},{label:"Количество",field:"quantity",sort:"asc"},{label:"Инфо",field:"comment",sort:"asc"},{label:"Годен",field:"valid",sort:"asc"},{label:"Добавлен",field:"datecreate",sort:"asc"},{label:"Добавил",field:"who",sort:"asc"},{label:"Состояние",field:"state",sort:"asc"},{label:"Управление",field:"management"}],rows:['. $values .']}}}},c=i,u=r("2877"),f=Object(u["a"])(c,a,o,!1,null,null,null),s=f.exports;n["a"].config.productionTip=!1,new n["a"]({render:function(e){return e(s)}}).$mount("#app")}});

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
        $(".error").slideDown().text("EF101. Необходимо заново авторизироваться.");
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
        body:formEncode({get:"singleProduct",data:JSON.stringify(form),location_:location.href})
    }).then(r=> r.json()
            .then(data => ({status: r.status, body: data}))
        ).then(
            obj=>{
                if(obj.status==200){
                    let response=obj.body,data={};
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
                    if(response.valid!=""){
                        data.valid=`<div class="mb divValid">
                        <small>Срок годности:</small> ${response.valid}
                        <button class="btn btn-success btn-sm c0 changeValid">Изменить срок годности</button>
                        <div class=showValid style="display:none">
                            <div class=fx>
                            <input type=text class="form-control changedValid" value="${response.validDate}">
                            <button class="btn btn-danger btn-sm removeChangedValid">Очистить</button>
                            </div>
                            <span class="badge badge-info с0">При нажатии "Очистить" данные о сроке годности НЕ изменятся.</span>
                            <span class="badge badge-info с0">Если срок годности НЕ установлен в поле выше, то он автоматически обновится с сегодня + 3 месяца.</span>
                        </div>
                        </div>`;
                    }else{data.valid=`<div class="mb divValid">
                        <small>Срок годности:</small> <u>не установлен</u>
                        <button class="btn btn-success btn-sm c0 changeValid">Изменить срок годности</button>
                        <div class=showValid style="display:none">
                            <div class=fx>
                            <input type=text class="form-control changedValid" value="${response.validDate}">
                            <button class="btn btn-danger btn-sm removeChangedValid">Очистить</button>
                            </div>
                            <span class="badge badge-info с0">При нажатии "Очистить" данные о сроке годности НЕ изменятся.</span>
                        </div>
                        <span class="badge badge-info с0">Если срок годности НЕ установлен, то он автоматически добавится с сегодня + 3 месяца.</span>
                        </div>`};
                    if(response.suppliers!=""){
                    data.suppliersHtml="<span class=\"badge badge-info с0\">При необходимости, измените цены. Если изменения не требуются, то проигнорирутйе данные поля. Если необходимо убрать конкретные или все цены поставщиков, удалите цены.</span>";
                        response.suppliers.forEach(function(e){
                        let suppliersUnpublishClass=suppliersUnpublishTxt="";
                        if(e.state==0){suppliersUnpublishClass=" supUnpublish";suppliersUnpublishTxt=" | <u>неактивен<\/u>"};
                            data.suppliersHtml+=
`<div class="t${suppliersUnpublishClass}"><small>${e.name}${suppliersUnpublishTxt}</small></div>
<input type="number" class="addSingleSupplierData form-control" data-id="${e.id}" value="${e.price}">`
                        });
                        
                    }else{data.suppliersHtml=""};
                    data.today=new Date();
                    data.year=data.today.getFullYear();/*2020*/
                    data.month=data.today.getMonth()+1;/*0-11*/
                    data.day=data.today.getDate();/*1-31*/
                    if(data.month<10)data.month="0"+data.month;
                    if(data.day<10)data.day="0"+data.day;
                    data.today=`${data.year}-${data.month}-${data.day}`;
                     $("body").addClass("ovh");
                     $(".modal").slideDown(100);
                     $(".modal-body").html(`
                     <div class="col-md-auto">
                     <span class="badge badge-dark">ID товара: ${response.idInt}</span>
                     <div class=accHD>
                        <div class=accHeader>Товар</div>
                        <div class="accHeader salesHeader">Продажи</div>
                     </div>
                     <div class=accDiv>
            <div class="form-group">
                <label>Название *</label>
                <input maxlength="50" class="name form-control" value="${response.name}">
            </div>
            <div class="form-group">
                <label>Цена *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="price form-control" value="${response.price}">
            </div>
            <div class="form-group">
                <label>Оптовая цена</label>
                <input maxlength="20" type=number min=0 max=99999999 class="priceopt form-control" value="${response.priceopt}">
            </div>
            <div class="form-group">
                <label>Количество *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="quantity form-control" value="${response.quantity}">
            </div>
            <div class="form-group">
                <label>Комментарий</label>
                <textarea class="comment form-control" rows="3" maxlength=2000>${response.comment}</textarea>
            </div>
            <div class="form-group divSuppliers">
                <label>Поставщики</label>
                ${data.suppliersHtml}
                <button class="addAllSuppliers btn btn-sm c0 btn-default">Добавить поставщиков</button>
            </div>
            <div class=mb><small>Дата создания: ${response.datecreate}</small></div>
            <div class="mb fx"><small>Дата прихода: <input type=text class="form-control setSingleDateIn" value="${response.din}"></small></div>
            ${data.valid}
            <div class="form-group">
                Создал сотрудник с ID <strong><span class="badge badge-secondary">${response.who}</span></strong>
                <span class="badge badge-info с0">Вы можете посмотреть, кто это, перейдя в "Управление пользователями"</span>
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
            <input type="hidden" class="id" value="${response.id}">
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <p><small>После редактирования, при необходимости, обновите страницу, чтобы данные в таблице с товарами обновились.</small></p>
            <div class="modal-footer">
                <div class="pr loading loadingCl w1 fx fxjc fxac"><button type="button" class="btn btn-primary btn-lg btn-block saveSingleProduct">Сохранить</button></div>
            </div>
        </div>
                </div><!--/single acc div-->
                
                <div class=accDiv>
                    <div class=sales>Загрузка...</div>
                    <div class="prw1 fx fxjc fxac"><button type="button" class="btn btn-secondary btn-lg btn-block setSaleProduct">Добавить продажу</button></div>
                    
                    <div class="mt-2 salesAdd" style="display:none">
                        <small>Количество товара на данный момент: <span class="badge badge-secondary">${response.quantity}</span></small>
                        <div class=mb>Дата: <input class="form-control singleSaleDate" value="${data.today}"></div>
                        <div class=mb>Количество: <input class="form-control singleSaleQuantity" type=number></div>
                        <div>Комментарий</div>
                        <textarea class="saleText form-control" rows=3 maxlength=2000></textarea>
                        <div class="saleOk alert alert-primary" style="display:none"></div>
                        <div class="saleError alert alert-danger" style="display:none"></div>
                        <small>Внимание! Количество товара (всего) отнимется от введенного Вами выше.</small>
                        <button class="addSingleSale btn c0 btn-default">Сохранить продажу</button>
                    </div>
                </div>
      `);
      
      $(".setSaleProduct").on("click",function(){
          $(".salesAdd").slideToggle(100);
          let text=$(this).text();
          $(this).text((text == "Добавить продажу") ? "Скрыть добавление продажи" : "Добавить продажу")
      });     
      let acch=$(".accHeader"),accd=$(".accDiv"),acci;
      $(acch[0]).addClass("accHActive");
      $(accd[0]).slideDown(100);
      $(acch).on("click",function(){
        acci=$(acch).index(this)
        $(acch).removeClass("accHActive");
        $(this).addClass("accHActive");
        $(accd).slideUp(100)
        $(accd[acci]).slideDown(100)
      });
      
      $(".changeValid").on("click",()=>{
        $(".showValid").slideToggle(100);
        $(".divValid").toggleClass("divValidCl")
      });
      
      $(".removeChangedValid").on("click",()=>{
        $(".changedValid").val("")
      });

$(".changedValid,.setSingleDateIn,.singleSaleDate").pickadate({today: "Сегодня",
clear: "Очистить",
close: "Закрыть",
format: "yyyy-mm-dd"});
      
/*From first fetch*/
if(response.state==1){
 $("#customRadio1").attr("checked","")
};
if(response.state==0){
 $("#customRadio2").attr("checked","")
};
/* \ */
let fetchSend=fetchSend2=false,fetchtf={};

      
$(".addSingleSale").on("click",()=>{
    $(".saleOk,.saleError").slideUp();
    if(fetchtf.addSingleSale===true){return false};
    fetchtf.addSingleSale=true;
    let formSale={};
    formSale.token=$(".token").val();
    formSale.text=$(".saleText").val();
    
    if($(".singleSaleDate").val()!=""){
        formSale.date=$(".singleSaleDate").val()
    }else{$(".saleError").slideDown().text("Выберите дату продажи");fetchtf.addSingleSale=false;return false};
    if($(".singleSaleQuantity").val()!=""){
        formSale.quantity=$(".singleSaleQuantity").val()
    }else{$(".saleError").slideDown().text("Выберите количество товара");fetchtf.addSingleSale=false;return false};
    if(localStorage.flr_secret===undefined){
        $(".saleError").slideDown().text("EF103. Необходимо заново авторизироваться.");
        return false;
    }else{formSale.tokenLS=localStorage.flr_secret};
    formSale.id=$(".id").val();
    fetch("/",{
    method:"POST",
    headers:{"Content-type":"application/x-www-form-urlencoded"},
    mode:"same-origin",
    credentials:"same-origin",
    cache:"no-cache",
    body:formEncode({set:"addSingleSale",data:JSON.stringify(formSale),location_:location.href})
}).then(r=> r.json()
        .then(data => ({status: r.status, body: data}))
    ).then(obj=>{
        fetchtf.addSingleSale=false;
        if(obj.status==200){
            let response=obj.body;
            if(response){
            removeScript();
            if(response.script!==undefined){
                $("body").append(`<script class="remove">${response.script}<\/script>`);
            };
             if(response.state=="error"){
                $(".saleError").slideDown().text(response.data);
                $(".saleOk").slideUp()
             };
             $(".saleOk,.saleError").slideUp();
                if(response.state=="ok"){
                    $(".saleOk").slideDown().text(response.data);
                }
                $(".singleSaleDate,.singleSaleQuantity,.saleText").val("")
            }
        }
    }).catch(error => console.error("Ошибка: ", error));
});






$(".salesHeader").on("click",()=>{
    if(fetchtf.salesHeader===true){return false};
    fetchtf.salesHeader=true;
    let formSaleAll={};
    formSaleAll.token=$(".token").val();
    $(".allerror").attr("style","position:fixed!important;z-index:1100;top:0;left:0;right:0;bottom:auto").hide();
    if(localStorage.flr_secret===undefined){
        $(".allerror").slideDown().text("EF103. Необходимо заново авторизироваться.");
        return false;
    }else{formSaleAll.tokenLS=localStorage.flr_secret};
    formSaleAll.id=$(".id").val();
    fetch("/",{
    method:"POST",
    headers:{"Content-type":"application/x-www-form-urlencoded"},
    mode:"same-origin",
    credentials:"same-origin",
    cache:"no-cache",
    body:formEncode({get:"allSalesSingleProduct",data:JSON.stringify(formSaleAll),location_:location.href})
}).then(r=> r.json()
        .then(data => ({status: r.status, body: data}))
    ).then(obj=>{
        fetchtf.salesHeader=false;
        if(obj.status==200){
            let response=obj.body;
            if(response){
            removeScript();
            if(response.script!==undefined){
                $("body").append(`<script class="remove">${response.script}<\/script>`);
            };
            if(response.state=="error"){
                $(".allerror").slideDown().text(response.data);
                $(".allok").slideUp()
            };
            if(response.state=="nodata"){
                $(".sales").html(response.data);
            };
            response.html_=`<table class="table tableSales"><tbody><tr>
<th>#</th>
<th>Дата продажи</th>
<th>Дата добавления</th>
<th>Штук</th>
<th>Комментарий</th>
<th>Кто добавил</th>
</tr>`;
let si=1;
            response.forEach(function(e){
response.html_+=`
<tr data-id=${e.id}><td>${si}</td>
<td>${e.date}</td>
<td>${e.dateadd}</td>
<td>${e.quantity}</td>
<td>${e.text}</td>
<td>${e.uid}</td></tr>
`;
                si++
            });
            response.html_+=`</tbody></table>`;
            $(".sales").html(response.html_)
            }
        }
    }).catch(error => console.error("Ошибка: ", error));
});






$(".addAllSuppliers").on("click",()=>{
    $(".error,.ok").slideUp();
    if(fetchSend2===true){return false};
    fetchSend2=true;
    let formSuppliers={};
    formSuppliers.ids=[];/*all existing suppliers ids*/
    formSuppliers.token=$(".token").val();
    $(".addSingleSupplierData").each(function(e,w){formSuppliers.ids.push($(w).data("id"))});
fetch("/", {
    method: "POST",
    headers: { "Content-type": "application/x-www-form-urlencoded"},
    mode: "same-origin",
    credentials: "same-origin",
    cache: "no-cache",
    body:formEncode({get:"allSuppliersNotIn",data:JSON.stringify(formSuppliers),location_:location.href})
}).then(r=> r.json()
        .then(data => ({status: r.status, body: data}))
    ).then(obj=>{
        fetchSend2=false;
        if(obj.status==200){
            let response=obj.body,dataSuppliers={};
            if(response){
            removeScript();
            if(response.script!==undefined){
                $("body").append(`<script class="remove">${response.script}<\/script>`);
            };
             if(response.state=="error"){
                $(".error").slideDown().text(response.data);
                $(".ok").slideUp()
             };
             $(".addAllSuppliers").slideUp(100,function(){$(".addAllSuppliers").remove()});
            fetchSend2=false;
            
            dataSuppliers.suppliersHtml="";
            if(response==""){
                $(".addAllSuppliers").before("Поставщиков больше нет.")
            }else{
            response.forEach(function(e){
                dataSuppliers.suppliersHtml+=
`<div class="t txt"><small>${e.name}</small></div>
<input type="number" class="addSingleSupplierData form-control" data-id="${e.id}">`
            });
            $(".addAllSuppliers").before(`<span class="badge badge-info с0">При необходимости, заполните новые данные о ценах поставщиков.</span>${dataSuppliers.suppliersHtml}`)
                    };
            }
        }
    }).catch(error => console.error("Ошибка: ", error));
});
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
                    };
                    
                    form.suppliers={};
                    $(".addSingleSupplierData").each(function(){
                        if($(this).val()!="")form.suppliers[$(this).data("id")]=$(this).val();
                    });
                    form.id=$(".id").val();
                    form.name=$(".name").val();
                    form.price=$(".price").val();
                    form.priceopt=$(".priceopt").val();
                    form.quantity=$(".quantity").val();
                    form.comment=$(".comment").val();
                    form.din=$(".setSingleDateIn").val();
                    form.token=$(".token").val();
                    
                    if($(".changedValid").val()!=""){
                        form.valid=$(".changedValid").val()
                    };
                    
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
function d(id,this_){/*delete product*/

let confirm_=confirm("Вы действительно хотите удалить этот товар?");
    if(!confirm_){return false};

    $(".allerror,.allok").slideUp();                       
    let form={};
    
    form.id=id;
    form.token=$(".token").val();
    
    if(localStorage.flr_secret===undefined){
        $(".error").slideDown().text("EF101. Необходимо заново авторизироваться.");
        $(".ok").slideUp();
        return false;
    }else{form.tokenLS=localStorage.flr_secret};
    
    fetch("/", {
        method: "POST",
        headers: { "Content-type": "application/x-www-form-urlencoded"},
        mode: "same-origin",
        credentials: "same-origin",
        cache: "no-cache",
        body:formEncode({set:"deleteSingleProduct",data:JSON.stringify(form),location_:location.href})
    }).then(r=> r.json()
            .then(data => ({status: r.status, body: data}))
        ).then(obj=>{
                if(obj.status==200){
                    let response=obj.body;
                    if(response){
                        removeScript();
                        $("tr").eq(this_+1).slideUp(100);
                        if(response.script!==undefined){
                            $("body").append(`<script class="remove">${response.script}<\/script>`);
                        }
                        if(response.state=="ok"){
                            $(".allok").slideDown().text(response.data);
                            $(".allerror").slideUp();
                        }
                        if(response.state=="error"){
                            $(".allerror").slideDown().text(response.data);
                            $(".allerror").slideUp()
                        }
                    }
                }
            }).catch(error => console.error("Ошибка: ", error));
};


$("body").on("click",".error",function(){
    $(".error").slideToggle(100)
});

</script>

<style>
.loading:before,.loading:after{content:"";position:absolute;z-index:2;top:0;right:0;bottom:0;left:0;background:rgba(255,255,255,.55)}
.loading:after{top:auto;right:auto;bottom:auto;left:auto;width:2rem;height:2rem;border-radius:50%;background:none;border:2px solid;border-color:transparent #666;animation:al ease .75s infinite}
@keyframes al {50%{transform: rotate(360deg)}}
.loadingCl:before,.loadingCl:after{display:none}

.c0{color:#000!important}

.divValid,.divSuppliers{border-radius:3px;padding:4px}
.divValidCl,.divSuppliers{box-shadow:-2px 0 #0086ff,0 0 0 1px #62b4fd}

.alert-danger{position:sticky!important;bottom:0}
.supUnpublish{opacity:.75;color:red}

.accHeader{cursor:pointer;display:inline-block;padding:4px;border:1px solid #0086ff;border-bottom:0;border-radius:3px 3px 0 0;transition:background-color .35s,color .35s}
.accHActive{background-color:#4968ff;color:#fff}
.accDiv{display:none}
.accHD{border-bottom:1px solid #0086ff;margin-top:10px}
.sales{min-height:3rem}
.tableSales *{font-size:.75rem;line-height:.8rem}
</style>

';

$this->css('chunk-vendors.4e206b88');
$this->script('chunk-vendors.ed32299e');
$this->customScript($scr);

$this->script('picker');
$this->script('picker.date');
$this->css('default');
$this->css('default.date');