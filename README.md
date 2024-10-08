# easyCredit-Rechnung & Ratenkauf for Magento 2 - Installment Payment Plugin

easyCredit-Ratenkauf is the easiest and fastest installment payment solution of Germany. Join today to get the simplest way of partial payment for your POS and E-Commerce. easyCredit-Ratenkauf gives you the opportunity to offer installments as an additional payment method in your German WooCommerce store.

Traditional financing solutions are often connected with complicated application processes for customers. With easyCredit-Ratenkauf you can offer a simple, fully online and fast financing solution. Customers can use ‚easyCredit-Ratenkauf‘ in just a few steps: choose their purchases, calculate their preferred installments, enter their personal data, and pay. No paperwork, immediate approval, and complete flexibility throughout. Simple. Fair. Paying in installments with ‚easyCredit-Ratenkauf‘.

# Getting started
Are you interested in using easyCredit-Ratenkauf? Contact us now:
* [sales.ratenkauf@easycredit.de](https://store.shopware.com/en/easyc36021249341f/ratenkauf-by-easycredit.html#)
* +49 (0)911 5390 2726

or register at [easycredit-ratenkauf.de](https://www.easycredit-ratenkauf.de/registrierung.htm) and we will contact you.

**Please note that a valid contract is required to use the plug-in.**

# Installation

The easyCredit extension for Magento 2 can be installed using Composer or by manually copying files. If files are copied manually, the API library has to be installed separately using Composer.

## Composer Installation 

Go to your Magento 2 installation directory and run the following commands:

	composer require teambank/easycredit-plugin-magento-2
	php bin/magento setup:upgrade
	php bin/magento setup:di:compile
	php bin/magento cache:clean

Please also follow the guidelines in our [Documentation](https://netzkollektiv.com/docs/easycredit-magento2/)

# Compatibility

[![Test](https://github.com/teambank/easycredit-plugin-magento-2/actions/workflows/test.yml/badge.svg)](https://github.com/teambank/easycredit-plugin-magento-2/actions/workflows/test.yml)

This extension aims to be as compatible as possible with current, future versions of Magento 2. This version is tested with:

* Magento 2.4.x 

Earlier versions of Magento 2 may work, but are not actively tested anymore. For earlier versions of Magento or PHP < 7.4 based systems please try to use v1.3.10.

If you still have any problems, please open a ticket or contact [ratenkauf@easycredit.de](mailto:ratenkauf@easycredit.de).

# License

* [MIT](https://opensource.org/licenses/MIT)

# Security
If you have discovered a security vulnerability, please email [opensource@teambank.de](mailto:opensource@teambank.de).
