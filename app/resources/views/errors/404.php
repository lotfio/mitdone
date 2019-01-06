<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?=CSS?>main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="<?=CSS?>fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="<?=CSS?>style.css">
    <title><?=tr(20)?></title>
  </head>
  <body>
    
      <div class="page-error tile">
        <h1><i class="fa fa-exclamation-circle"></i> Error 404: Page not found</h1>
        <p>The page you have requested is not found.</p>
        <p><a class="btn btn-primary" href="javascript:window.history.back();">Go Back</a></p>
      </div>


    <!-- Essential javascripts for application to work-->
    <script src="<?=JS?>jquery-3.2.1.min.js"></script>
    <script src="<?=JS?>popper.min.js"></script>
    <script src="<?=JS?>bootstrap.min.js"></script>
    <script src="<?=JS?>main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?=JS?>plugins/pace.min.js"></script>
    </body>
</html>