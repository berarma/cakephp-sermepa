<?php
/**
 *
 * CakePHP Component to interact with the Sermepa TPV service
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

App::uses('Component', 'Controller');
App::uses('Sermepa', 'Sermepa.Lib');

class SermepaComponent extends Component {

    public $serviceUrl;

	public $merchantCode;

	public $merchantName;

	public $terminal;

	public $secretKey;

	public $extendedSignature;

	public $merchantUrl;

	public $urlOk;

	public $urlKo;

	public $consumerLanguage;

	public $currency;

	public $Controller;

    public function __construct(ComponentCollection $collection, $settings = array()) {
        if (Configure::read('Sermepa')) {
            $settings = $settings + Configure::read('Sermepa');
        }
        parent::__construct($collection, $settings);
    }

    public function startup(Controller $controller) {
        $this->Controller = $controller;
    }

    public function createTransaction($order, $amount, $transactionType = '0') {
        $Sermepa = new Sermepa($this);
        $this->Controller->request->params['sermepaUrl'] = $Sermepa->getPostUrl();
        $this->Controller->request->params['sermepaData'] = $Sermepa->getPostData($order, $amount, $transactionType);
    }

    /**
     * @throws CakeException
     */
    public function getNotification() {
        if (!$this->Controller->request->is('post')) {
            throw new CakeException("Sermepa notification not using POST.");
        }
        $Sermepa = new Sermepa($this);
        $data = $Sermepa->getNotificationData($this->Controller->request->data);

        $data = json_decode(json_encode($data), true);
        $notification = new stdClass();
        foreach ($data as $key => $value) {
            $notification->{lcfirst(str_ireplace('Ds_', '', $key))} = $data[$key];
        }

        return $notification;
    }

}
