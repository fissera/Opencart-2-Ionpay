<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-payza" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ionpay" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_env; ?></label>
						<div class="col-sm-4">
							<select name="ionpay_environment" class="form-control">
							<?php $options = array('development' => 'Sandbox', 'production' => 'Production') ?>
							<?php foreach ($options as $key => $value): ?>
								<option value="<?php echo $key ?>" <?php if ($key == $ionpay_environment) echo 'selected' ?> ><?php echo $value ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
					<div class="form-group required">
						<label class="col-sm-2 control-label"><?php echo $entry_merchant; ?></label>
						<div class="col-sm-4">
							<input type="text" name="ionpay_merchant" value="<?php echo $ionpay_merchant; ?>" class="form-control">
							<?php if ($error_merchant) { ?>
							<div class="text-danger"><?php echo $error_merchant; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group required">
						<label class="col-sm-2 control-label"><?php echo $entry_security; ?></label>
						<div class="col-sm-4">
							<input type="text" name="ionpay_security" value="<?php echo $ionpay_security; ?>" class="form-control">
							<?php if ($error_security) { ?>
							<div class="text-danger"><?php echo $error_security; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_ionpay_rate; ?></label>
						<div class="col-sm-4">
							<input type="text" name="ionpay_rate" value="<?php echo $ionpay_rate; ?>" class="form-control">
							<?php if ($error_security) { ?>
							<div class="text-danger"><?php echo $error_security; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_invoice; ?></label>
						<div class="col-sm-4">
							<input type="text" name="ionpay_inv_payment" value="<?php echo $ionpay_inv_payment; ?>" class="form-control">
							<?php if ($error_inv_payment) { ?>
							<div class="text-danger"><?php echo $error_inv_payment; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_callback; ?></label>
						<div class="col-sm-10">
							<strong style="position: relative; top: 30px;"><?php echo $callback; ?></strong>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>
						<div class="col-sm-4">
							<select name="ionpay_order_status_id" class="form-control" style="position: relative; top: 10px;">
								<?php foreach ($order_statuses as $order_status) { ?>
								<?php if ($order_status['order_status_id'] == $ionpay_order_status_id) { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_order_success_status; ?></label>
						<div class="col-sm-4">
							<select name="ionpay_success_status" class="form-control" style="position: relative; top: 10px;">
								<?php foreach ($order_statuses as $order_status) { ?>
								<?php if ($order_status['order_status_id'] == $ionpay_success_status) { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_geo_zone; ?></label>
						<div class="col-sm-4">
							<select name="ionpay_geo_zone_id" class="form-control">
								<option value="0"><?php echo $text_all_zones; ?></option>
								<?php foreach ($geo_zones as $geo_zone) { ?>
								<?php if ($geo_zone['geo_zone_id'] == $ionpay_geo_zone_id) { ?>
								<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
						<div class="col-sm-4">
							<select name="ionpay_status" class="form-control">
								<?php if ($ionpay_status) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
						<div class="col-sm-4">
							<input type="text" name="ionpay_sort_order" value="<?php echo $ionpay_sort_order; ?>" id="input-sort-order" class="form-control" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?> 