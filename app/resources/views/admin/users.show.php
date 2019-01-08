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
         <?php ?>
        </div>
    </div>

    
</main>


<?= singleView('tmp/footer', $data);?>