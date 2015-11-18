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
include 'apiRedsys.php';

class Sermepa extends Object {

    protected $_settings;
    protected $_notification;
    private $_hash_version = 'HMAC_SHA256_V1';

    public function __construct($settings) {
        $this->_settings = $settings;
    }

    public function getPostUrl() {
        return $this->_settings->serviceUrl;
    }

    public function getPostData($order, $amount, $transactionType = 0) {

        $api = new RedsysAPI;

        $api->setParameter('Ds_Merchant_Amount', $amount);
        $api->setParameter('Ds_Merchant_Currency', $this->_settings->currency);
        $api->setParameter('Ds_Merchant_Order', $order);
        $api->setParameter('Ds_Merchant_MerchantCode', $this->_settings->merchantCode);
        $api->setParameter('Ds_Merchant_MerchantURL', $this->_settings->merchantUrl);
        $api->setParameter('Ds_Merchant_UrlOK', $this->_settings->urlOk);
        $api->setParameter('Ds_Merchant_UrlKO', $this->_settings->urlKo);
        $api->setParameter('Ds_Merchant_MerchantName', $this->_settings->merchantName);
        $api->setParameter('Ds_Merchant_ConsumerLanguage', $this->_settings->consumerLanguage);
        $api->setParameter('Ds_Merchant_Terminal', $this->_settings->terminal);
        $api->setParameter('Ds_Merchant_TransactionType', $transactionType);

        $parameters = $api->createMerchantParameters();
        $signature = $api->createMerchantSignature($this->_settings->secretKey);

        return array(
            'Ds_SignatureVersion' => $this->_hash_version,
            'Ds_MerchantParameters' => $parameters,
            'Ds_Signature' => $signature
        );
    }

    /**
     * @throws CakeException
     */
    public function getNotificationData($data) {
        $version = $data["Ds_SignatureVersion"];
        if ($version !== $this->_hash_version) {
            throw new CakeException("Invalid signature version ($version) received from Sermepa.");
        }

        $api = new RedsysAPI;

		$parameters = $data["Ds_MerchantParameters"];
		$signature = $api->createMerchantSignatureNotif($this->_settings->secretKey, $parameters);

        if ($data["Ds_Signature"] !== $signature) {
            throw new CakeException("Invalid signature in Sermepa notification.");
        }

		$parameters = $api->decodeMerchantParameters($parameters);
		$this->_notification = json_decode($parameters);

        return $this->_notification;
    }

}
