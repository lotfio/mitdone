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

            <div class="tile-title-w-btn">
                <div class="btn-group">
                    <a class="btn btn-primary btn" href="<?=BASE_URI.'admin/users/edit/'.$data->user->id?>"><i class="fa fa-lg fa-edit"></i></a>
                    <a href="<?=BASE_URI?>admin/users/notify/<?=e($data->user->id)?>" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notify"><i class="fa fa-lg fa-bell fa-fw"></i></a>
                    <a href="<?=BASE_URI?>admin/users/message/<?=e($data->user->id)?>" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Message"><i class="fa fa-lg fa-envelope fa-fw"></i></a>
                    <a data-id="<?=e($data->user->id)?>" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-lg fa-trash fa-fw"></i></a>
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
                            <div><b>Phone number :</b> <?=$data->user->phone?></div>
                            <div><b>Address :</b> <?=$data->user->Address?></div>
                            <div><b>JOIN DATE :</b> <?=$data->user->created_at?></div>
                        </div>
                      
                        </div>
                    </div>
                </div>
            <hr>
            <?php if(is_array($data->orders)):?>
            <div class="tile">
            <div class="tile-body">
           
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Number</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th>City</th>
                    <th>Street</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                 <?php foreach($data->orders as $order):?>
                 <tr>
                    <td><?=$order->id?></td>
                    <td><?=$order->price?></td>
                    <td><?=$order->description_problem?></td>
                    <td><?=$order->location_address?></td>
                    <td><?=$order->status?></td>
                    <td><?=$order->note?></td>
                    <td><?=$order->name?></td>
                    <td><?=$order->street?></td>
                    <td>
                      <a href="<?=BASE_URI?>admin/users/show/<?=e($user->id)?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Show"><i class="fa fa-eye fa-fw"></i></a>
                      <a href="<?=BASE_URI?>admin/users/edit/<?=e($user->id)?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="fa fa-edit fa-fw"></i></a>
                      <a href="<?=BASE_URI?>admin/users/notify/<?=e($user->id)?>" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notify"><i class="fa fa-bell fa-fw"></i></a>
                      <a href="<?=BASE_URI?>admin/users/message/<?=e($user->id)?>" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Message"><i class="fa fa-envelope fa-fw"></i></a>
                      <a data-id="<?=e($user->id)?>" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-trash fa-fw"></i></a>
                    </td>
                  </tr>
                 <?php endforeach?>
                 <?php else:?>
                    <div class="bs-component">
                      <div class="alert alert-dismissible alert-danger">
                          <button class="close" type="button" data-dismiss="alert">×</button>
                          <strong>Oh snap!</strong>No Orders Where Found ! 
                      </div>
                    </div>
                </tbody>
              </table>
            </div>
          </div>
          <?php endif?>


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