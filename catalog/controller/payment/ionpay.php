<?php

require_once(DIR_SYSTEM . 'library/ionpay/Ionpay.php');

class ControllerPaymentIONPay extends Controller {
	public function index() {

		$this->language->load('payment/ionpay');
		
		$data['button_confirm'] = $this->language->get('button_confirm');
		
		$data['action'] = $this->url->link('payment/ionpay/send');

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data['ap_merchant'] = $this->config->get('ionpay_merchant');

		$data['url_web'] = $this->url;
		$data['ap_security'] = $this->config->get('ionpay_security');
		$data['ap_payment'] = $this->config->get('ionpay_payment');
		//$data['ap_ionpay_rate'] = $this->config->get('ionpay_rate');
		$data['ap_inv_payment'] = $this->config->get('ionpay_inv_payment');
		$data['ap_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['ap_currency'] = $order_info['currency_code'];
		$data['ap_purchasetype'] = 'Item';
		$data['ap_itemname'] = $this->config->get('config_name') . ' - #' . $this->session->data['order_id'];
		$data['ap_itemcode'] = $this->session->data['order_id'];
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ionpay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/ionpay.tpl', $data);
			//$this->template = $this->config->get('config_template') . '/template/payment/ionpay.tpl';
		} else {
			return $this->load->view('default/template/payment/ionpay.tpl', $data);
			//$this->template = 'default/template/payment/ionpay.tpl';
		}
	}	 
	
	private function simpleXor($string, $password) {
		$data = array();

		for ($i = 0; $i < strlen($password); $i++) {
			$data[$i] = ord(substr($password, $i, 1));
		}

		$output = '';

		for ($i = 0; $i < strlen($string); $i++) {
			$output .= chr(ord(substr($string, $i, 1)) ^ ($data[$i % strlen($password)]));
		}

		return $output;		
	}

	public function send() {
		$this->load->model('checkout/order');
		$this->load->model('total/shipping');

		$data['errors'] = array();

		$order_info = $this->model_checkout_order->getOrder(
			$this->session->data['order_id']);

		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'],
			$this->config->get('ionpay_order_status_id'));

		$transaction_details = array();
		$transaction_details['mallId'] = $this->config->get('ionpay_merchant');
		$transaction_details['invoiceNo'] = $this->config->get('ionpay_inv_payment') . $this->session->data['order_id'];
		$transaction_details['amount'] = $order_info['total'] + 0;
		$transaction_details['currencyCode'] = 360;

		$products = $this->cart->getProducts();
		$item_details = array();

		foreach ($products as $product) {
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || ! $this->config->get('config_customer_price')) {
				$product['price'] = $this->tax->calculate(
					$product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
			}

			$item = array(
				'description' => $product['name']." - ".$product['model'],
				'quantity' => $product['quantity'],
				'price' => $product['price']
				);

			$item_details[] = $item;
		}

		unset($product);

		$num_products = count($item_details);

		if ($this->cart->hasShipping()) {
			$shipping_info = $this->session->data['shipping_method'];
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || ! $this->config->get('config_customer_price')) {
				$shipping_info['cost'] = $this->tax->calculate(
						$shipping_info['cost'],
						$shipping_info['tax_class_id'],
						$this->config->get('config_tax'));
			}

			$shipping_item = array(
				'description'	=> 'SHIPPING',
				'quantity'	=> 1,
				'price'	=> $shipping_info['cost']
				);

			$item_details[] = $shipping_item;
		}

		if ($this->config->get('config_currency') != 'IDR') {
			if ($this->currency->has('IDR')) {
				foreach ($item_details as &$item) {
					$item['price'] = intval($this->currency->convert(
						$item['price'], $this->config->get('config_currency'), 'IDR'));
				}

				unset($item);

				$transaction_details['amount'] = intval($this->currency->convert(
						$transaction_details['amount'],
						$this->config->get('config_currency'),
						'IDR'
					));
			}
			else if ($this->config->get('ionpay_rate') > 0) {
				foreach ($item_details as &$item) {
					$item['price'] = intval($item['price'] * $this->config->get('ionpay_rate'));
				}

				unset($item);

				$transaction_details['amount'] = intval($transaction_details['amount'] * $this->config->get('ionpay_rate'));
			}
			else {
				$data['errors'][] = 'Currency IDR tidak terinstall atau Ionpay currency conversion rate tidak valid. Silahkan check option kurs dollar.';
			}
		}

		$total_price = 0;
		foreach ($item_details as $item) {
			$total_price += $item['price'] * $item['quantity'];
		}

		if ($total_price != $transaction_details['amount']) {
			$coupon_item = array(
				'description'	=> 'COUPON',
				'quantity'	=> 1,
				'price'	=> $transaction_details['amount'] - $total_price
				);

			$item_details[] = $coupon_item;
		}

		$transaction_details['orderInfo'] = $item_details;
		$transaction_details['paymentMethod'] = 5;
		$transaction_details['returnUrl'] = $this->url->link('payment/ionpay/success', 'order_id=' . $this->session->data['order_id']);
		$transaction_details['callbackUrl'] = $this->url->link('checkout/callback') . '?invoiceNo=' . $transaction_details['invoiceNo'];
		$transaction_details['customerFirstName'] = $order_info['payment_firstname'];
		$transaction_details['customerLastName'] = $order_info['payment_lastname'];
		$transaction_details['customerAddress'] = $order_info['payment_address_1'];
		//$transaction_details['customerCity'] = $order_info['payment_city'];
		$transaction_details['customerCountry'] = $order_info['payment_country'];
		//$transaction_details['customerZipCode'] = $order_info['payment_postcode'];
		$transaction_details['customerPhone'] = $order_info['telephone'];

		$payloads = json_encode($transaction_details);

		$hash_mac = hash_hmac('sha1', $payloads, $this->config->get('ionpay_security'), FALSE);

		$req_params = array(
			'type'	=> 1,
			'req'	=> $payloads,
			'mac'	=> $hash_mac
			);

		if ($this->config->get('ionpay_environment') == 'development') {
			$req_url = 'https://dev.ionpay.net/IonPay/Request';
		}
		else if ($this->config->get('ionpay_environment') == 'production') {
			$req_url = 'https://pay.ionpay.net/IonPay/Request';
		}

		$get_request = Ionpay::remote_caller($req_url, $req_params);
		
		$this->response->redirect($get_request->url);
	}

	public function success() {

		$this->cart->clear();
		$this->response->redirect($this->url->link('checkout/success'));
		
	}

    public function callback(){
    	header("HTTP/1.1 200 OK");

        $this->load->model('checkout/order');

        $payloads['mallId'] = $this->config->get('ionpay_merchant');
        $payloads['tid'] = $this->request->get['TID'];

        $hash_mac = hash_hmac('sha1', json_encode($payloads), $this->config->get('ionpay_security'), FALSE);

        $req_params = array(
        	'type'	=> 2,
        	'req'	=> json_encode($payloads),
        	'mac'	=> $hash_mac
        	);

        if ($this->config->get('ionpay_environment') == 'development') {
        	$req_url = 'https://dev.ionpay.net/IonPay/Request';
        }
        else if ($this->config->get('ionpay_environment') == 'production') {
        	$req_url = 'https://pay.ionpay.net/IonPay/Request';
        }

        $get_request = Ionpay::remote_caller($req_url, $req_params);

        if ($get_request->resultCode == 0) {
    		$inv_pref = explode('-', $this->request->get['invoiceNo']);
        	$this->model_checkout_order->update(
        		$inv_pref[1],
        		$this->config->get('ionpay_success_status'),
        		'Payment successfully through Ionpay Credit Card. With Transaction ID '. $get_request->tid, TRUE);
        }

        echo "OK";
    }
}