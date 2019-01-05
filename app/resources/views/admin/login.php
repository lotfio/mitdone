<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
    <h1><?=tr(2)?></h1>
    </div>
    <div class="login-box">
    <form class="login-form" action="<?=BASE_URI?>admin/login" method="POST" enctype="application/x-www-form-urlencoded">

    <?php if(!empty($data)): ?>
    <?php foreach($data as $err):?>
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
        </div>
        <div class="form-group">
        <div class="utility">
            <div class="animated-checkbox">
            <label>
                <input type="checkbox"><span class="label-text">Stay Signed in</span>
            </label>
            </div>
            <p class="semibold-text mb-2"><a href="#" data-toggle="flip">Forgot Password ?</a></p>
        </div>
        </div>
        <div class="form-group btn-container">
        <button class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN</button>
        </div>
    </form>

    <form class="forget-form" action="index.html">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>Forgot Password ?</h3>
        <div class="form-group">
        <label class="control-label">EMAIL</label>
        <input class="form-control" type="text" placeholder="Email">
        </div>
        <div class="form-group btn-container">
        <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i>RESET</button>
        </div>
        <div class="form-group mt-3">
        <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Back to Login</a></p>
        </div>
    </form>
    </div>
</section>