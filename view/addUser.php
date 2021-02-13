<?php
$out.=
    '<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-auto">
            <h4>Добавить пользователя</h4>
            <div class="form-group text-left">
                <label for="name">Введите имя *</label>
                <input type="text" maxlength="29" id="name" class="form-control">
            </div>
            <div class="form-group text-left">
                <label for="email">Введите email *</label>
                <div class="fx fxac">
                    <input type="text" minlength="5" maxlength="50" id="email" class="form-control">
                </div>
            </div>
            <div class="form-group text-left">
                <label for="password">Введите пароль *</label>
                <div class="fx fxac">
                    <input type="text" minlength="5" maxlength="30" id="password" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label for="comment">Заметка</label>
                <textarea class="form-control" id="comment" rows="3"></textarea>
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
            
            <button class="btn btn-primary btn-lg btn-block">Добавить</button>
            
            <p><small>Поля, отмеченные звездочкой, необходимо заполнить...</small></p>
            
        </div>
    </div>
</div>';



$scr='
<script>
let fetchSend=false;
$("button").on("click",()=>{
    if (window.fetch){
        if(fetchSend===true){return false};
        fetchSend=true;
        function formEncode(obj){
            let str = [];
            for(let p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            return str.join("&");
        };
        
        let getRole="",form={};
        
        $(".custom-control-input").each(function() {
              if($(this).is(":checked")){
                getRole=$(this).data("role")
              }
          });
        
        form.role=getRole;
        form.name=$("#name").val();
        form.email=$("#email").val();
        form.password=$("#password").val().trim();
        form.comment=$("#comment").val();
        
        fetch("/", {
            method: "POST",
            headers: { "Content-type": "application/x-www-form-urlencoded"},
            mode: "same-origin",
            credentials: "same-origin",
            cache: "no-cache",
            body:formEncode({set:"addUser",data:JSON.stringify(form),location_:location.href})
        }).then(r=> r.json()
                .then(data => ({status: r.status, body: data}))
            ).then(obj=>{
                fetchSend=false;
                if(obj.status==200){
                    let response=obj.body;
                    if(response){
                     if(response.state=="ok"){
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
';

$this->customScript($scr);