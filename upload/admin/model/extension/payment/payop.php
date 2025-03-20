<?php

/**
 * Class ModelExtensionPaymentPayop
 *
 * @property Loader			  $load
 * @property ModelSettingSetting $model_setting_setting
 * @property DB\MySQLi		   $db
 */
class ModelExtensionPaymentPayop extends Model {
	public function install() {
		$defaults = [
			'payment_payop_sort_order' => 0,
			'payment_payop_order_status_wait' => $this->config->get('config_order_status_id'),
			'payment_payop_order_status_success' => $this->config->get('config_order_status_id'),
			'payment_payop_order_status_error' => $this->config->get('config_order_status_id')
		];

		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('payment_payop', $defaults);
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('payment_payop');
	}
}
