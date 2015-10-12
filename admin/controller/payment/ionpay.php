<?php
class ControllerPaymentIONPay extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/ionpay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ionpay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		
		$data['entry_env'] = $this->language->get('entry_env');
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_security'] = $this->language->get('entry_security');
		$data['entry_payment'] = $this->language->get('entry_payment');
		$data['entry_ionpay_rate'] = $this->language->get('entry_ionpay_rate');
		$data['entry_invoice'] = $this->language->get('entry_invoice');
		$data['entry_callback'] = $this->language->get('entry_callback');
		$data['entry_total'] = $this->language->get('entry_total');	
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_success_status'] = $this->language->get('entry_order_success_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

  		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}

 		if (isset($this->error['security'])) {
			$data['error_security'] = $this->error['security'];
		} else {
			$data['error_security'] = '';
		}

 		if (isset($this->error['payment'])) {
			$data['error_payment'] = $this->error['payment'];
		} else {
			$data['error_payment'] = '';
		}

 		if (isset($this->error['inv_payment'])) {
			$data['error_inv_payment'] = $this->error['inv_payment'];
		} else {
			$data['error_inv_payment'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/ionpay', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$data['action'] = $this->url->link('payment/ionpay', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		$inputs = array(
			'ionpay_environment',
			'ionpay_merchant',
			'ionpay_security',
			'ionpay_payment',
			'ionpay_rate',
			'ionpay_inv_payment',
			'ionpay_total',
			'ionpay_order_status_id',
			'ionpay_success_status',
			'ionpay_geo_zone_id',
			'ionpay_status',
			'ionpay_sort_order'
		);

		// For better performance
		for ($i=0; $i < count($inputs); $i++) { 
			if (isset($this->request->post[$inputs[$i]])) {
				$data[$inputs[$i]] = $this->request->post[$inputs[$i]];
			} else {
				$data[$inputs[$i]] = $this->config->get($inputs[$i]);
			}
		}
		
		$data['callback'] = HTTP_CATALOG . 'index.php?route=payment/ionpay/callback';
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		/*
		$this->template = 'payment/ionpay.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		*/
				
		$this->response->setOutput($this->load->view('payment/ionpay.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/ionpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['ionpay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['ionpay_security']) {
			$this->error['security'] = $this->language->get('error_security');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>