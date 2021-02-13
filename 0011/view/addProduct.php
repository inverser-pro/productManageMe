<?php
$out.=
    '<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-auto">
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
                <label>Количество *</label>
                <input maxlength="20" type=number min=0 max=99999999 class="quantity form-control">
            </div>
            
            <!--<div class="form-group">
                <label>Дата поступления (сегодня или выберите)</label>
                <input maxlength="20" type=number min=0 max=99999999 class="din form-control">
            </div>-->
            
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
            
        
            <div class="pr loading loadingCl w1 fx fxjc fxac"><button type="button" class="btn btn-primary btn-lg btn-block addSingleProductManager">Сохранить</button></div>
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            <input type=hidden value="'. crypt_($_SESSION['time_token']) .'" class="token">
            
        </div>
    </div>
</div>';



$scr='
<script>
let fetchSend=false;
$(".addSingleProductManager").on("click",()=>{
    
if(fetchSend===true){return false};
fetchSend=true;
    
    if (window.fetch){
        function formEncode(obj){
            let str = [];
            for(let p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            return str.join("&");
        };
        
        let form={},loading=$(".loading");
        
        $(".custom-control-input").each(function() {
              if($(this).is(":checked")){
                form.state=$(this).data("state")
              }
          });
        
        form.name=$(".name").val();
        form.price=$(".price").val();
        form.quantity=$(".quantity").val();
        form.token=$(".token").val();
        
        if(localStorage.flr_secret===undefined){
            $(".error").slideDown().text("EF107. Необходимо заново авторизироваться.");
            $(".ok").slideUp();
            return false;
        }else{form.tokenLS=localStorage.flr_secret};
        loading.removeClass("loadingCl");
        fetch("/", {
            method: "POST",
            headers: { "Content-type": "application/x-www-form-urlencoded"},
            mode: "same-origin",
            credentials: "same-origin",
            cache: "no-cache",
            body:formEncode({set:"addSingleProductManager",data:JSON.stringify(form),location_:location.href})
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
                }).catch(error => console.error(\'Ошибка:\', error));
    }else{$(".error").slideDown().text("Похоже, что Ваш браузер устарел. Зайдите сюда с нового браузера Google Chrome, Opera, YandexBrowser или FireFox")}
})
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

$this->customScript($scr);