<?php
$out.='<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-auto">
            <div class="alert alert-primary" role="alert">
              Вы успешно вышли...
            </div>
        </div>
    </div>
</div>
<script>
if(localStorage.flr_secret!==undefined){
    localStorage.removeItem("flr_secret")
};
</script>
';
