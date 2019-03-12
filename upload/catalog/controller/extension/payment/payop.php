<?php

/**
 * Class ControllerExtensionPaymentPayop
 *
 * @property Language                           $language
 * @property \Cart\Currency                     $currency
 * @property Config                             $config
 * @property Url                                $url
 * @property Loader                             $load
 * @property Session                            $session
 * @property Request                            $request
 * @property Response                           $response
 *
 * @property ModelCheckoutOrder                 $model_checkout_order
 * @property ModelExtensionPaymentPayop         $model_extension_payment_payop
 */
class ControllerExtensionPaymentPayop extends Controller {
	/** @var  resource */
	private $curl;

	public function index() {
		$this->load->language('extension/payment/payop');

		$lang_code = $this->language->get('code');
		switch ($lang_code) {
			case 'ru':
				$widget_lang = 'ru-RU';
				break;
			default:
				$widget_lang = 'en-US';
		}

		$data = array(
			'button_pay'=> $this->language->get('button_pay'),
			'payop_url' => $this->url->link('extension/payment/payop/pay')
		);

		return $this->load->view('extension/payment/payop', $data);
	}

	public function pay() {
		$this->response->addHeader('Content-Type: application/json');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$order_products = $this->model_checkout_order->getOrderProducts($this->session->data['order_id']);

		$payop_order_items = array();		
		foreach ($order_products as $product) {
			$item = array(
				'id' => $product['order_product_id'],
				'name' => trim($product['name'] . ' ' . $product['model']),
				'price' => $product['price']				
			);
			array_push($payop_order_items, $item);
		}
		$request = array(
			'publicKey' => $this->config->get('payment_payop_public_id'),
			'order' => array(
				'id' => $order_info['order_id'],
				'amount' => number_format($this->currency->format($order_info['total'], $order_info['currency_code'], '', false), 4),
				'currency' => $order_info['currency_code'],
				'description' => sprintf($this->language->get('order_description'), $order_info['order_id']),
				'items' => $payop_order_items,
			),
			'customer' => array(
				'email' => $order_info['email'],
				'phone' => $order_info['telephone'],
				'name' => $order_info['firstname'] . ' ' . $order_info['lastname']
			),
			'resultUrl' => $this->url->link('checkout/success'),
			'failUrl' => $this->url->link('checkout/failure'),
		);
		$request['signature'] = $this->signature($request);
		$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('payment_payop_order_status_wait'));
		$this->response->setOutput($this->makeRequest('payments/payment', $request));
	}

	/**
	 * @param $request
	 */
	private function signature($request) {
		$sign_string = $request['order']['amount'] . ':' . $request['order']['currency'] . ':' . $request['order']['id'] . ':' . $this->config->get('payment_payop_secret_key');
        return hash('sha256', $sign_string);
	}

	public function callback() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$callback = json_decode(file_get_contents('php://input'));		
			$sign_string = $callback->amount . ':' . $callback->currency . ':' . $callback->orderId . ':' . $callback->status . ':' . $this->config->get('payment_payop_secret_key');
			if ($callback->signature == hash('sha256', $sign_string)) {
				$this->saveTransaction($callback);
				$this->load->model('checkout/order');
				if ($callback->status === 'success')
					$this->model_checkout_order->addOrderHistory($callback->orderId, $this->config->get('payment_payop_order_status_success'));
				else if ($callback->status === 'error')
					$this->model_checkout_order->addOrderHistory($callback->orderId, $this->config->get('payment_payop_order_status_error'));
			}
			else
				$this->log->write("Payop Callback invalid signature\n" . print_r(file_get_contents('php://input'), true));
		}		
	}

	/**
	 * @param $callback
	 */
	private function saveTransaction($callback) {
		$transaction = array(
			'txid' => $callback->txid,
			'status' => $callback->status,
			'publicKey' => $callback->publicKey,
			'type' => $callback->type,
			'amount' => $callback->amount,
			'currency' => $callback->currency,
			'signature' => $callback->signature,
			'language' => $callback->language,
			'date' => $callback->date,
			'orderId' => $callback->orderId,
			'email' => $callback->email,
			'payopId' => $callback->payopId,
			'error' => json_encode($callback->error),
		);
		$this->load->model('extension/payment/payop');
		$this->model_extension_payment_payop->addTransaction($transaction);
	}	

	/**
	 * @param string $location
	 * @param array  $request
	 * @return bool|array
	 */
	private function makeRequest($location, $request = array()) {
		$request = json_encode($request);
		if (!$this->curl) {
			$this->curl = curl_init();
        	curl_setopt($this->curl, CURLOPT_URL, 'https://payop.com/api/v1.1/' . $location);
        	curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		}		               
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
        ));
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($this->curl);
		if ($response === false || curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 200) {
			$this->log->write('Payop Failed API request' . "\n" .
				' Location: ' . $location . "\n" .
				' Request: ' . print_r($request, true) . "\n" .
				' Response: ' . print_r($response, true) . "\n" .
				' HTTP Code: ' . curl_getinfo($this->curl, CURLINFO_HTTP_CODE) . "\n" .
				' Error: ' . curl_error($this->curl). "\n"
			);
		}
		$resp = json_decode($response);
		if (count($resp->errors) > 0) {
			$this->log->write('Payop Failed API request' . "\n" .
				' Location: ' . $location . "\n" .
				' Request: ' . print_r($request, true) . "\n" .
				' Response: ' . print_r($response, true) . "\n" .
				' HTTP Code: ' . curl_getinfo($this->curl, CURLINFO_HTTP_CODE) . "\n" .
				' Error: ' . curl_error($this->curl) . "\n"
			);
		}
		curl_close($this->curl);
		return $response;
	}
}
