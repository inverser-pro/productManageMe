<?php
$out.=
'
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-auto">
            <h4>Вас приветствует система управления товарами</h4>
            <p>Прежде чем начать, необходимо авторизироваться.</p>
            <div class="form-group text-left">
                <label>Введите свой email</label>
                <input type="text" name="email" class="form-control email">
            </div>
            <div class="form-group text-left">
                <label>Введите свой пароль</label>
                <div class="fx fxac">
                    <input type="password" class="form-control password">
                    <small><button class="showPassword btn btn-dark">Показать пароль</button></small>
                </div>
            </div>
            <input type="hidden" value="'. crypt_($_SESSION['time_token']) .'" class="token">
            
            <div class="ok alert alert-primary" role="alert" style="display:none"></div>
            <div class="error alert alert-danger" role="alert" style="display:none"></div>
            <button class="btn btn-primary btn-lg btn-block send">Войти</button>
        </div>
    </div>
</div>';
/*date("Y-m-d h:i:s",$_SESSION['time_token'])*/
$scr='
<script>
function removeScript(){
    if($("script").is(".remove"))$("script.remove").remove();
};
let click=0,btSP=$(".showPassword"),pwd=$(".password");
btSP.on("click",function() {
  if(click==1){
      click=0;
      btSP.text("Показать пароль");
      pwd.attr("type","password")
  }else{
      click=1;
      btSP.text("Скрыть пароль");
      pwd.attr("type","text")
  }
});

$(".send").on("click",()=>{
    if (window.fetch){
        let form={};        
        form.password=$(".password").val().trim();
        form.email=$(".email").val().trim();
        form.token=$(".token").val();

        function formEncode(obj){
            var str = [];
            for(let p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            return str.join("&");
        };
        fetch("/", {
            method: "POST",
            headers: { "Content-type": "application/x-www-form-urlencoded"},
            mode: "same-origin",
            credentials: "same-origin",
            cache: "no-cache",
            body:formEncode({get:"login",data:JSON.stringify(form),location_:location.href})
        }).then(r=> r.json()
                .then(data => ({status: r.status, body: data}))
            ).then(
                obj=>{
                    if(obj.status==200){
                        let response=obj.body;
                        if(response){
                         if(response.state=="ok"){
                            $(".ok").slideDown().text(response.data);
                            $(".error").slideUp();
                            if(response.script!=undefined){
                                $("body").append(`<script class=remove>${response.script}<\/script>`);
                            }
                         }
                         if(response.state=="error"){
                            $(".error").slideDown().text(response.data);
                            $(".ok").slideUp()
                         }
                        }
                    }
                }
                ).catch(error => alert(\'Ошибка:\' + error));
    }else{$(".error").slideDown().text("Похоже, что Ваш браузер устарел. Зайдите сюда с нового браузера Google Chrome, Opera, YandexBrowser или FireFox")}
})
</script>
';

$this->customScript($scr);