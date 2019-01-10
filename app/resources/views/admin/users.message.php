<?= singleView('tmp/header', $data)?>
<?= singleView('tmp/top-nav', $data)?>
<?= singleView('tmp/left-nav', $data);?>

<main class="app-content">
    <div class="app-title">
    <div>
        <h1><i class="fa fa-user"></i> Send Message</h1>
        <p>Send A message to a user</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#"><?tr(17)?></a></li>
    </ul>
    </div>
    <div class="row">
    <div class="col-md-12">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=BASE_URI?>admin/users/">Users</a></li>
            <li class="breadcrumb-item active">Message</li>
            <button onclick="javascript:window.history.back();"class="btn btn-primary btn-sm">back</button>
            <div class="clear-fix"></div>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    
    <div class="col-md-12">
        <div class="tile">
            <div class="timeline-post"> <!-- Time line post -->

            <?php if(is_object($data->user)): ?>
                <div class="col-md-4">
                    <form class="form-horizontal message-frm">

                        
                        <h6>Send Message To : <span><?=e($data->user->name)?></span></h6>
                    
                        <div class="tile-footer"></div>

                        <div class="form-group">
                            <div>
                            <textarea class="form-control" rows="3" placeholder="Enter your message here ..."></textarea>
                            </div>
                        </div>

                        <div class="tile-footer">
                        <button class="btn btn-primary" type="submit" name="update" value="update">
                        <i class="fa fa-fw fa-lg fa-envelope"></i> Send</button>
                        <button class="btn btn-secondary" type="reset" name="update" value="update">
                        <i class="fa fa-fw fa-lg fa-times"></i> Clear</button>
                        </div>
                        

                        </form>
                </div>
                <?php else:?>
                    <br>
                    <div class="alert alert-dismissible alert-danger">
                        <button class="close" type="button" data-dismiss="alert">×</button><strong>Ouch !</strong>
                        No User Were Found !
                    </div>
                <?php endif?>
            </div>
        </div>
    </div>
</div>



















</main>










<?= singleView('tmp/js')?>
<?= singleView('tmp/footer')?>