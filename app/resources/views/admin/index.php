
<?= singleView('tmp/header', $data)?>
<?= singleView('tmp/top-nav', $data)?>
<?= singleView('tmp/left-nav', $data)?>

<main class="app-content">
    <div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> <?=tr(22)?></h1>
        <p><?=tr(23)?></p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#"><?tr(17)?></a></li>
    </ul>
    </div>

    <!-- statistics 1-4 2-5 3-6 -->
    <div class="row">
        <div class="col-md-4">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4><?=tr(24)?></h4>
              <h6><?=tr(25)?></h6>
              <p><b><?=$data->countAllUsers?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-plus-circle fa-3x"></i>
            <div class="info">
              <h4><?=tr(26)?></h4>
              <h6><?=tr(27)?></h6>
              <p><b>25</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-graduation-cap fa-3x"></i>
            <div class="info">
              <h4><?=tr(28)?></h4>
              <h6><?=tr(29)?></h6>
              <p><b>10</b></p>
            </div>
          </div>
        </div>
        <!-- =======================-->
        <div class="col-md-4">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4><?=tr(24)?></h4>
              <h6><?=tr(25)?></h6>
              <p><b>500</b></p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-plus-circle fa-3x"></i>
            <div class="info">
              <h4><?=tr(26)?></h4>
              <h6><?=tr(27)?></h6>
              <p><b>500</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-graduation-cap fa-3x"></i>
            <div class="info">
              <h4><?=tr(28)?></h4>
              <h6><?=tr(29)?></h6>
              <p><b>500</b></p>
            </div>
          </div>
        </div>



      </div>
    <!-- end statistics -->

</main>































<?= singleView('tmp/footer')?>