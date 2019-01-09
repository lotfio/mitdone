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
            <li class="breadcrumb-item active">Show</li>
        </ol>
    </div>
</div>

    <div class="row">
    <div class="col-md-2"></div>
    
    <div class="col-md-12">
        <div class="tile">

             <?php if(is_object($data->user)):?>

            <div class="tile-title-w-btn">
                <div class="btn-group">
                    <a class="btn btn-primary" href="<?=BASE_URI.'admin/users/edit/'.$data->user->id?>"><i class="fa fa-lg fa-edit"></i></a>
                    <a class="btn btn-danger btn-delete" data-id="<?=$data->user->id?>" data-place="show"><i class="fa fa-lg fa-trash"></i></a>
                </div>
            </div>
           
            <div class="timeline-post"> <!-- Time line post -->
                <div class="col-md-4">
                <div class="post-media"><a href="#"><img src="<?=image($data->user->image, 'default-avatar.png')?>"></a>

                  <div class="content"> <!-- INFORMATION -->
                    <h5><a href="#"><?=$data->user->name?></a></h5>


                        <div class="table">
                            <div><b>User ID  :</b> <?=$data->user->id?></div>
                            <div><b>User Name  :</b> <?=$data->user->name?></div>
                            <div><b>Email :</b> <?=$data->user->Address?></div>
                            <div><b>Phone number :</b> <?=$data->user->phone?></div>
                            <div><b>JOIN DATE :</b> <?=$data->user->created_at?></div>
                        </div>
                      
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


<?= singleView('tmp/js', $data);?>
<script type="text/javascript" src="<?=JS?>plugins/sweetalert.min.js"></script>
<?= singleView('tmp/footer')?>