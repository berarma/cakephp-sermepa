[![Build Status](https://travis-ci.org/berarma/cakephp-sermepa.svg?branch=master)](https://travis-ci.org/berarma/cakephp-sermepa) [![Coverage Status](https://coveralls.io/repos/berarma/cakephp-sermepa/badge.png?branch=master)](https://coveralls.io/r/berarma/cakephp-sermepa?branch=master)

# CakePHP Sermepa Plugin

NOTICE: This plugin has been discontinued. The new version is available at
https://github.com/berarma/cakephp-redsys.

With this plugin is possible to do online payments using the Sermepa/Redsýs TPV
service.

## Requirements

* CakePHP 2.x

## Installation

* Copy or clone the files to `app/Plugin/Sermepa`
* Load the plugin in `app/Config/bootstrap.php`, use `CakePlugin::loadAll();`
  or `CakePlugin::load('Sermepa');`

### Using Composer

Ensure `require` is present in `composer.json`, you can add it with the
following command:
```
php composer.phar require berarma/cakephp-sermepa
```

## Use

Create your configuration like this:

```php
$config = array(
    'Sermepa' => array(
      'serviceUrl' => 'https://sis-t.redsys.es:25443/sis/realizarPago', // Testing
      // Use 'https://sis.redsys.es/sis/realizarPago' for the real environment
      'extendedSignature' => false,
      'merchantName' => 'Merchant Name',
      'merchantCode' => '000000001',
      'secretKey' => 'QWERTYASDF0123456789',
      'terminal' => '001',
      'currency' => '978',
      'consumerLanguage' => '1',
      'merchantUrl' => 'http://example.com/get_notification',
    )
  );
```

Setting things up in the Controller:

```php
public $components = array('Sermepa');
public $helpers = array('Sermepa');
```

Initiating a transaction in the Controller:

```php
$this->Sermepa->createTransaction($orderId, $amount, '0');
```

Rendering the form that sends the user to the TPV in the View:

```php
<?php echo $this->Sermepa->renderForm(array('id' => 'sermepa_form', 'target' => '_blank')); ?>
<?php echo $this->Html->scriptBlock('$( "#sermepa_form" ).submit();'); ?>
```

Getting a notification in the Controller:

```php
$notification = $this->Sermepa->getNotification();
```

See the Test files to find more use examples.

## License

This plugin is licensed under the GPL v2 license. Most common uses of this
plugin won't constitute a derived work, thus, you may use it and include it in
your app independently of the license you've choosen for your code as long as
the plugin itself is still distributed with its original license. But using
this plugin from another plugin that provides modified or improved
functionality may constitute a derived work and thus require you to use the
same license for your plugin.

