<?= singleView('tmp/header', $data)?>
<?= singleView('tmp/top-nav', $data)?>
<?= singleView('tmp/left-nav', $data);?>


<main class="app-content">
    <div class="app-title">
    <div>
        <h1><i class="fa fa-gift"></i> Orders</h1>
        <p>Information about orders</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#"><?tr(17)?></a></li>
    </ul>
    </div>


    <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">

              <?php if(is_array($data->orders['orders'])):?>
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Price</th>
                    <th>Description</th> 
                    <th>Status</th>
                    <th>Note</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($data->orders['orders'] as $order):?>
                 <tr>
                    <td><?=$order->id?></td>
                    <td><?=$order->u_name?></td>
                    <td><?=$order->price?></td>
                    <td><?=$order->description_problem?></td> 
                    <td><?=$order->note?></td>
                    <td><?=$order->street?></td>
                    <td> 
                            <a href="<?=BASE_URI?>admin/orders/show/<?=e($order->id)?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Show"><i class="fa fa-eye fa-fw"></i></a>
                            <a href="<?=BASE_URI?>admin/orders/edit/<?=e($order->id)?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="fa fa-edit fa-fw"></i></a>
                            <a href="<?=BASE_URI?>admin/users/notify/<?=e($order->user_id)?>" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notify"><i class="fa fa-bell fa-fw"></i></a>
                            <a href="<?=BASE_URI?>admin/users/message/<?=e($order->user_id)?>" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Message"><i class="fa fa-envelope fa-fw"></i></a>
                
                <a data-uri="<?=BASE_URI?>admin/orders/delete/<?=e($order->id)?>" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-trash fa-fw"></i></a>
                      
                    </td>
                  </tr>
                 <?php endforeach?>
                </tbody>
              </table>

<?php $page = (object) ($data->orders['pagination'])?>
              <ul class="pagination"> <!-- pagination -->

                   <?php if($page->prev > 1):?>
                      <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/orders/<?=$page->prev?>">«</a></li>
                  <?php else:?>
                      <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
                  <?php endif?>

               

                <?php if($page->prev >= 1):?>
                    <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/orders/<?=$page->prev?>"><?=$page->prev?></a></li>
                <?php endif?>

                  <li class="page-item active"><a class="page-link" href="<?=BASE_URI?>admin/orders/<?=$page->current?>"><?=$page->current?></a></li>


                <?php if($page->next < $page->total):?>
                    <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/orders/<?=$page->next?>"><?=$page->next?></a></li>
                <?php endif?>

                <?php if($page->next + 10 < $page->total):?>
                    <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/orders/<?=$page->next + 10?>"><?=$page->next + 10?></a></li>
                <?php endif?>
                  

                <?php if($page->next < $page->total):?>
                  <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/orders/<?=$page->next?>">»</a></li>
                <?php else:?>
                  <li class="page-item disabled"><a class="page-link" href="#">»</a></li>
                <?php endif?>
              </ul><!-- end pagination -->

     <?php else:?>
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
<script type="text/javascript" src="<?=JS?>plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=JS?>plugins/sweetalert.min.js"></script>
<?= singleView('tmp/footer')?>

