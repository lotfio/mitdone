<?=singleView('tmp/header', $data)?>
    
      <div class="page-error tile">
        <h1><i class="fa fa-exclamation-circle"></i> <?=tr(3)?> </h1>
        <p><?=tr(3)?></p>
        <p><a class="btn btn-primary" href="javascript:window.history.back();"><?=tr(3)?></a></p>
      </div>

<?=singleView('tmp/footer', $data)?>