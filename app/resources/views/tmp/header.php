<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?=CSS?>main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="<?=CSS?>font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?=CSS?>style.css">
    <title><?=tr(20)?> <?=isset($data->title) ? $data->title : tr(21) ?></title>
    <script type="text/javascript">
      var base_uri = '<?=BASE_URI?>';
    </script>
  </head>
  <body class="app sidebar-mini rtl pace-done ">
  <script>
        if ( localStorage.getItem("nv_menu")==1 ){document.body.className += "sidenav-toggled";}
    </script>