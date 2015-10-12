<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="aksi" value="bayar" />
  <input type="hidden" name="orderid" value="<?php echo $ap_itemcode; ?>" />
  <div class="buttons">
    <div class="pull-right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>
