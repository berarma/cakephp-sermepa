<?php
/**
 *
 * CakePHP Library Object to interact with the Sermepa TPV service
 *
 * Copyright 2014 Bernat Arlandis i Mañó
 *
 * This package is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This package is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @copyright Copyright 2014 Bernat Arlandis i Mañó
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

class Sermepa extends Object {

	protected $_settings;

	protected $_notification;

	public function __construct($settings) {
		$this->_settings = $settings;
	}

	public function getPostUrl() {
		return $this->_settings->serviceUrl;
	}

	public function getPostData($order, $amount, $transactionType = 0) {
		$order = str_pad($order, 12, '0', STR_PAD_LEFT);
		if ($this->_settings->extendedSignature) {
			$signature = hash("sha256", $amount . $order . $this->_settings->merchantCode . $this->_settings->currency . $transactionType . $this->_settings->merchantUrl . $this->_settings->secretKey);
		} else {
			$signature = hash("sha256", $amount . $order . $this->_settings->merchantCode . $this->_settings->currency . $this->_settings->secretKey);
		}
		return array(
			'Ds_Merchant_Amount' => $amount,
			'Ds_Merchant_Currency' => $this->_settings->currency,
			'Ds_Merchant_Order' => $order,
			'Ds_Merchant_MerchantCode' => $this->_settings->merchantCode,
			'Ds_Merchant_MerchantURL' => $this->_settings->merchantUrl,
			'Ds_Merchant_UrlOK' => $this->_settings->urlOk,
			'Ds_Merchant_UrlKO' => $this->_settings->urlKo,
			'Ds_Merchant_MerchantName' => $this->_settings->merchantName,
			'Ds_Merchant_ConsumerLanguage' => $this->_settings->consumerLanguage,
			'Ds_Merchant_MerchantSignature' => $signature,
			'Ds_Merchant_Terminal' => $this->_settings->terminal,
			'Ds_Merchant_TransactionType' => $transactionType,
		);
	}

/**
 * @throws CakeException
 */
	public function getNotificationData($data) {
		$this->_notification = new stdClass();
		$this->_notification->date = $data['Ds_Date'];
		$this->_notification->hour = $data['Ds_Hour'];
		$this->_notification->amount = $data['Ds_Amount'];
		$this->_notification->currency = $data['Ds_Currency'];
		$this->_notification->order = $data['Ds_Order'];
		$this->_notification->merchantCode = $data['Ds_MerchantCode'];
		$this->_notification->terminal = $data['Ds_Terminal'];
		$this->_notification->signature = $data['Ds_Signature'];
		$this->_notification->response = $data['Ds_Response'];
		$this->_notification->merchantData = $data['Ds_MerchantData'];
		$this->_notification->securePayment = $data['Ds_SecurePayment'];
		$this->_notification->transactionType = $data['Ds_TransactionType'];
		$this->_notification->cardCountry = $data['Ds_Card_Country'];
		$this->_notification->authorisationCode = $data['Ds_AuthorisationCode'];
		$this->_notification->consumerLanguage = $data['Ds_ConsumerLanguage'];
		$this->_notification->cardType = $data['Ds_Card_Type'];
		$signature = hash("sha256", $this->_notification->amount . $this->_notification->order . $this->_notification->merchantCode . $this->_notification->currency . $this->_notification->response . $this->_settings->secretKey);
		if (strtolower($this->_notification->signature) !== $signature) {
			throw new CakeException("Invalid signature in Sermepa notification.");
		}
		return $this->_notification;
	}

}
