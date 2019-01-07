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
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>number</th>
                    <th>name</th>
                    <th>email</th>
                    <th>phone number</th>
                    <th>Join Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>


                 <?php foreach($data->allUsers as $user):?>
                 <tr>
                    <td><?=$user->id?></td>
                    <td><?=$user->name?></td>
                    <td><?=$user->email?></td>
                    <td><?=$user->phone?></td>
                    <td><?=$user->created_at?></td>
                    <td>
                      <button class="btn btn-info btn-sm">Edit</button>
                      <button class="btn btn-danger btn-sm">del</button>
                    </td>
                  </tr>

                 <?php endforeach?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>




    
</main>


<?= singleView('tmp/footer', $data);?>

<script type="text/javascript" src="<?=JS?>plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=JS?>plugins/dataTables.bootstrap.min.js"></script>

<script type="text/javascript">$('#sampleTable').DataTable();</script>
    <!-- Google analytics script-->
    <script type="text/javascript">
      if(document.location.hostname == 'pratikborsadiya.in') {
      	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      	ga('create', 'UA-72504830-1', 'auto');
      	ga('send', 'pageview');
      }
    </script>