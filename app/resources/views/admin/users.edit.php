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
                    <div class="img-modify"><i class="fa fa-image fa-fw"></i></div>
                <div class="post-media"><a href="#"><img src="<?=image($data->user->image, 'default-avatar.png')?>"></a>

                  <div class="content"> <!-- INFORMATION -->


                        <form>
                <div class="form-group">
                  <label class="control-label">Name</label>
                  <input class="form-control" type="text" placeholder="Enter full name">
                </div>
                <div class="form-group">
                  <label class="control-label">Email</label>
                  <input class="form-control" type="email" placeholder="Enter email address">
                </div>
                <div class="form-group">
                  <label class="control-label">Address</label>
                  <textarea class="form-control" rows="4" placeholder="Enter your address"></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">Gender</label>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" type="radio" name="gender">Male
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" type="radio" name="gender">Female
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Identity Proof</label>
                  <input class="form-control" type="file">
                </div>
                <div class="form-group">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox">I accept the terms and conditions
                    </label>
                  </div>
                </div>
                <div class="tile-footer">
              <button class="btn btn-primary" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Register</button>&nbsp;&nbsp;&nbsp;<a class="btn btn-secondary" href="#"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
            </div>
              </form>
                      
                        </div>
                    </div>

                </div>
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


<?= singleView('tmp/footer', $data);?>
