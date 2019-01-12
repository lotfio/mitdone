<?= singleView('tmp/header', $data)?>
<?= singleView('tmp/top-nav', $data)?>
<?= singleView('tmp/left-nav', $data);?>


<main class="app-content">
    <div class="app-title">
    <div>
        <h1><i class="fa fa-gift"></i> Orders</h1>
        <p>Show Order</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#"><?tr(17)?></a></li>
    </ul>
    </div>

<div class="row">
<div class="col-md-12">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=BASE_URI?>admin/orders/">Orders</a></li>
        <li class="breadcrumb-item active">Show</li>
        <button onclick="javascript:window.history.back();"class="btn btn-primary btn-sm">back</button>
        <div class="clear-fix"></div>
    </ol>
</div>
</div>


    <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">

              <?php if(is_object($data->order)):?>

            <div class="tile-title-w-btn">
                <div class="btn-group">
                    <a class="btn btn-primary btn" href="<?=BASE_URI.'admin/orders/edit/'.$data->order->id?>"><i class="fa fa-lg fa-edit"></i></a>
                    <a href="<?=BASE_URI?>admin/users/notify/<?=e($data->order->user_id)?>" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notify"><i class="fa fa-lg fa-bell fa-fw"></i></a>
                    <a href="<?=BASE_URI?>admin/users/message/<?=e($data->order->user_id)?>" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Message"><i class="fa fa-lg fa-envelope fa-fw"></i></a>
                    <a data-uri="<?=BASE_URI?>/admin/orders/delete/<?=e($data->order->id)?>" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-lg fa-trash fa-fw"></i></a>
                </div>
            </div>


            
<div class="timeline-post"> <!-- Time line post -->
                <div class="col-md-4">
                <div class="post-media post-media-show">

                  <div class="content"> <!-- INFORMATION -->
                    <h5><a href="#"> Order number : <?=$data->order->id?></a></h5>


                        <div class="table">
                            <div><b>Order ID             :</b> <?=$data->order->id?></div>
                            <div><b>Order Price          :</b> <?=$data->order->price?></div>
                            <div><b>Order Description    :</b> <?=$data->order->description_problem?></div>
                            <div><b>Order Status         :</b> <?=$data->order->status?></div>
                            <div><b>Address              :</b> <?=$data->order->location_address?></div>
                            <div><b>Evaluation           :</b> <?=$data->order->evaluation?></div>
                            <div><b>Note                 :</b> <?=$data->order->note?></div>
                            <div><b>Town                 :</b> <?=$data->order->town?></div>
                            <div><b>City                 :</b> <?=$data->order->city?></div>
                            <div><b>Street               :</b> <?=$data->order->street?></div>
                        </div>
                      
                        </div>
                    </div>
                </div>
            <hr>












              <?php else: ?>
                    <div class="bs-component">
                      <div class="alert alert-dismissible alert-danger">
                          <button class="close" type="button" data-dismiss="alert">×</button>
                          <strong>Oh snap!</strong>No Orders Where Found ! 
                      </div>
                    </div>
                <?php endif?>
            </div>
          </div>
        </div>
    </div>

    
</main>

<?= singleView('tmp/js')?>
<script type="text/javascript" src="<?=JS?>plugins/sweetalert.min.js"></script>
<?= singleView('tmp/footer')?>