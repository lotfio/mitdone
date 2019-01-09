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
              <h4><?=tr(27)?></h4>
              <h6><?=tr(28)?></h6>
              <p><b><?=$data->countOrdersRequests?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-cogs fa-3x"></i>
            <div class="info">
              <h4><?=tr(30)?></h4>
              <h6><?=tr(31)?></h6>
              <p><b><?=$data->countEngineers?></b></p>
            </div>
          </div>
        </div>
        <!-- =======================-->
        <div class="col-md-4">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4><?=tr(24)?></h4>
              <h6><?=tr(26)?></h6>
              <p><b><?=$data->countLastSevenDaysUsers?></b></p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-plus-circle fa-3x"></i>
            <div class="info">
              <h4><?=tr(27)?></h4>
              <h6><?=tr(29)?></h6>
              <p><b><?=$data->countLastSevenDaysOrdersRequests?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-cogs fa-3x"></i>
            <div class="info">
              <h4><?=tr(30)?></h4>
              <h6><?=tr(32)?></h6>
              <p><b><?=$data->countLastSevenDaysEngineers?></b></p>
            </div>
          </div>
        </div>
      </div>
    <!-- end statistics -->

    <div class="row">
      <div class="col-md-12">
        <h3 class="pager">Last month statistics :</h3>
      </div>
    </div>


    <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
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


                
                 <tr>
                 <th>Action</th>
                 <th>Action</th>
                 <th>Action</th>
                 <th>Action</th>
                 <th>Action</th>
                 <th>Action</th>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>


</main>


<?= singleView('tmp/js')?>
<script type="text/javascript">
    // Login Page Flipbox control
    $('.login-content [data-toggle="flip"]').click(function() {
    $('.login-box').toggleClass('flipped');
    return false;
    });
</script>
<?= singleView('tmp/footer')?>