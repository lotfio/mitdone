<?= singleView('tmp/header', $data)?>
<?= singleView('tmp/top-nav', $data)?>
<?= singleView('tmp/left-nav', $data);?>


<main class="app-content">
    <div class="app-title">
    <div>
        <h1><i class="fa fa-user"></i> User Details</h1>
        <p>Show User Information</p>
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
            <li class="breadcrumb-item active">Edit</li>
            <button onclick="javascript:window.history.back();"class="btn btn-primary btn-sm">back</button>
            <div class="clear-fix"></div>
        </ol>
    </div>
</div>

    <div class="row">
    <div class="col-md-2"></div>
    
    <div class="col-md-12">
        <div class="tile">

             <?php if(is_object($data->user)):?>
           
            <div class="timeline-post"> <!-- Time line post -->
                <div class="col-md-4">

                <?php if(isset($data->edit)): ?>
                  <?php if(is_array($data->edit) || is_object($data->edit)):?>
                    <?php foreach($data->edit as $err):?>
                    <div class="alert alert-dismissible alert-danger">
                      <button class="close" type="button" data-dismiss="alert">×</button><strong>Ouch !</strong>
                      <?=$err?>
                    </div>
                    <?php endforeach;?>
                    <?php else:?>
                    <div class="alert alert-dismissible alert-success">
                      <button class="close" type="button" data-dismiss="alert">×</button><strong>Success !</strong>
                      User has been updated successfully !
                    </div>
                  <?php endif ?>
                <?php endif ?>
              
            <form class="update-frm" method="POST" enctype="multipart/form-data" href="<?=BASE_UIR?>admin/users/edit/<?=e($data->user->id)?>">

              <div class="img-modify" id="up-img" data-toggle="tooltip" data-placement="right" title="" data-original-title="change Image"><i class="fa fa-image fa-fw"></i></div>
                <div class="post-media"><a href="#"><img src="<?=image($data->user->image, 'default-avatar.png')?>"></a>
                <input class="form-control" type="file" name="image" id="up-img-input">

                  <div class="content"> <!-- INFORMATION -->

                <div class="form-group">
                  <label class="control-label">Name :</label>
                  <input class="form-control" type="text" name="name" placeholder="Enter full name" value="<?=e($data->user->name)?>">
                </div>

                <div class="form-group">
                  <label class="control-label">User Name :</label>
                  <input class="form-control" type="text" name="username" placeholder="Enter username" value="<?=e($data->user->username)?>">
                </div>

                <div class="form-group">
                  <label class="control-label">Phone</label>
                  <input class="form-control" type="phone" name="phone" placeholder="Enter phone number" value="<?=e($data->user->phone)?>">
                </div>

                <div class="form-group">
                  <label class="control-label">Address</label>
                  <textarea class="form-control" rows="4" name="address" placeholder="Enter your address"><?=e($data->user->Address)?></textarea>
                </div>

                <div class="form-group">
                  <label class="control-label">Role</label>
                  
                  <div class="animated-radio-button">
                    <label>
                      <input type="radio" name="role" value="2" <?=$data->user->role_id === 1 ?:e('checked')?>><span class="label-text">User</span>
                    </label>
                    <br>
                    <label>
                      <input type="radio"  name="role" value="1" <?=$data->user->role_id !== 1 ?:e('checked')?>><span class="label-text">Admin</span>
                    </label>
                  </div>
      

                </div>

            <div class="tile-footer">
              <button class="btn btn-primary" type="submit" name="update" value="update">
              <i class="fa fa-fw fa-lg fa-check-circle"></i>Register</button>&nbsp;&nbsp;&nbsp;
              <button class="btn btn-secondary" href="#"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</button>
            </div>
                      
                        </div>
                    </div>

                </div>

             </form> <!-- end of update form -->

              </div>
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

    
</main>


<?= singleView('tmp/js')?>
<?= singleView('tmp/footer')?>
