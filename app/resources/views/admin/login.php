<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
    <h1><?=tr(2)?></h1>
    </div>
    <div class="login-box">
    <form class="login-form" action="<?=BASE_URI?>admin/login" method="POST" enctype="application/x-www-form-urlencoded">

    <?php if(!empty($data->errors)): ?>
    <?php foreach($data->errors as $err):?>
        <div class="bs-component">
            <div class="alert alert-dismissible alert-danger">
            <button class="close" type="button" data-dismiss="alert">×</button><strong><?=tr(3)?></strong>
            <?=$err?>
            </div>
        </div>
    <?php endforeach?>
    <?php endif?>

        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i><?=tr(2)?></h3>
        <div class="form-group">
        <label class="control-label"><?=tr(6)?></label>
        <input class="form-control" type="text" name="phone" placeholder="Phone Number" autofocus>
        </div>
        <div class="form-group">
        <label class="control-label"><?=tr(7)?></label>
        <input class="form-control" type="password" name="passwd" placeholder="Password">
        <div class="form-group">
        <?=validate()->generateCSRF()?>
        </div>
      
        </div>
        <div class="form-group">
        <div class="utility">
            <div class="animated-checkbox">
            <label>
                <input type="checkbox"><span class="label-text"><?=tr(8)?></span>
            </label>
            </div>
            <p class="semibold-text mb-2"><a href="#" data-toggle="flip"><?=tr(9)?></a></p>
        </div>
        </div>
        <div class="form-group btn-container">
        <button class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i><?=tr(10)?></button>
        </div>
    </form>

    <form class="forget-form" action="index.html">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i><?=tr(9)?></h3>
        <div class="form-group">
        <label class="control-label"><?=tr(11)?></label>
        <input class="form-control" type="text" placeholder="Email">
        </div>
        <div class="form-group btn-container">
        <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i><?=tr(12)?></button>
        </div>
        <div class="form-group mt-3">
        <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i><?=tr(13)?></a></p>
        </div>
    </form>
    </div>
</section>