<?= singleView('tmp/header', $data)?>
<?= singleView('tmp/top-nav', $data)?>
<?= singleView('tmp/left-nav', $data);?>


<main class="app-content">
    <div class="app-title">
    <div>
        <h1><i class="fa fa-users"></i> <?=tr(25)?></h1>
        <p><?=tr(37)?></p>
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

              <?php if(is_array($data->allUsers['users'])):?>
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>number</th>
                    <th>name</th>
                    <th>username</th>
                    <th>phone number</th>
                    <th>Join Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                 <?php foreach($data->allUsers['users'] as $user):?>
                 <tr>
                    <td><?=$user->id?></td>
                    <td><?=$user->name?></td>
                    <td><?=$user->username?></td>
                    <td><?=$user->phone?></td>
                    <td><?=$user->created_at?></td>
                    <td>
                      <a href="<?=BASE_URI?>admin/users/show/<?=e($user->id)?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Show"><i class="fa fa-eye fa-fw"></i></a>
                      <a href="<?=BASE_URI?>admin/users/edit/<?=e($user->id)?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="fa fa-edit fa-fw"></i></a>
                      <a href="<?=BASE_URI?>admin/users/notify/<?=e($user->id)?>" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notify"><i class="fa fa-bell fa-fw"></i></a>
                      <a href="<?=BASE_URI?>admin/users/message/<?=e($user->id)?>" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Message"><i class="fa fa-envelope fa-fw"></i></a>
                      <a data-id="<?=e($user->id)?>" class="btn btn-danger btn-sm btn-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa fa-trash fa-fw"></i></a>
                    </td>
                  </tr>
                 <?php endforeach?>
                </tbody>
              </table>

<?php $page = (object) ($data->allUsers['pagination'])?>
              <ul class="pagination"> <!-- pagination -->

                   <?php if($page->prev > 1):?>
                      <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/users/<?=$page->prev?>">«</a></li>
                  <?php else:?>
                      <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
                  <?php endif?>

               

                <?php if($page->prev >= 1):?>
                    <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/users/<?=$page->prev?>"><?=$page->prev?></a></li>
                <?php endif?>

                  <li class="page-item active"><a class="page-link" href="<?=BASE_URI?>admin/users/<?=$page->current?>"><?=$page->current?></a></li>


                <?php if($page->next < $page->total):?>
                    <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/users/<?=$page->next?>"><?=$page->next?></a></li>
                <?php endif?>

                <?php if($page->next + 10 < $page->total):?>
                    <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/users/<?=$page->next + 10?>"><?=$page->next + 10?></a></li>
                <?php endif?>
                  

                <?php if($page->next < $page->total):?>
                  <li class="page-item"><a class="page-link" href="<?=BASE_URI?>admin/users/<?=$page->next?>">»</a></li>
                <?php else:?>
                  <li class="page-item disabled"><a class="page-link" href="#">»</a></li>
                <?php endif?>
              </ul><!-- end pagination -->

     <?php else:?>
                    <div class="bs-component">
                      <div class="alert alert-dismissible alert-danger">
                          <button class="close" type="button" data-dismiss="alert">×</button>
                          <strong>Oh snap!</strong>No Users Where Found ! 
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
<script type="text/javascript" src="<?=JS?>plugins/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?=JS?>plugins/sweetalert.min.js"></script>
<script type="text/javascript"></script>
<?= singleView('tmp/footer')?>

