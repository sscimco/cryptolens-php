# Cryptolens PHP

This repository contains functions for interacting with the Cryptolens
Web API from PHP. Currently most endpoints are supported and more are following.

For more information about the API and possible values and types, visit https://app.cryptolens.io/docs/api/v3, the official API documentation

To use the library, you can `require_once` the `loader.php` which loads all other classes automatically or use composer where you just have to `require` the composer `autoload.php`. Currently, this library is not safe to use for CLI.
Inside your script you need to `use` the classes, here is an example:

Needs PHP >7.4.0, works with 8.2

## Code example

```php
<?php
ini_set("display_errors", 1);
require_once "path/to/composer/autoload.php";
use Cryptolens_PHP_Client\Cryptolens;
use Cryptolens_PHP_Client\Key;
$c = new Cryptolens("YOUR_TOKEN", 12345, Cryptolens::CRYPTOLENS_OUTPUT_JSON);
$k = new Key($c);

$key = "XXXXX-XXXXX-XXXXX-XXXXX";
$machine_id = $k->getMachineId();
print_r("Key 'activate':" . var_dump($k->activate($key, $machine_id)));
?>
```

## Code Example: Check License for features

```php
<?php
ini_set("display_errors", 1);
require_once "./loader.php";
use Cryptolens_PHP_Client\Cryptolens;
use Cryptolens_PHP_Client\Key;
$c = new Cryptolens("YOUR_TOKEN", 12345, Cryptolens::CRYPTOLENS_OUTPUT_PHP);
$k = new Key($c);

# generate new key and activate Feature 3 for it
$key = $k->create_key(["F3" => true])["key"];

$license_data = json_decode($k->get_key($key), true);

if($license_data["F3"]){
  echo "Feature 3 enabled for license " . $key; 
} elseif($license_data["F4"]){
  echo "Feature 4 enabled for license " . $key;
}

?>
```

The code above uses our testing access token, product id, license key and machine code.
In a real values for you can be obtained as follows:

* Access tokens can be created at <https://app.cryptolens.io/User/AccessToken>
* The product id is found by selecting the relevant product from the list of products
   (<https://app.cryptolens.io/Product>), and then the product id is found above the list
   of keys.
* The license key would be obtained from the user in an application dependant way.
* You can generate a machine ID for the PHP instance with the builtin `Key::getMachineId()` funtion. Please read the function's documentation for more understanding of the calculation of the machine ID.
* In an upcoming release this library should be able to also validate license files

### Offline license validation

The API also allows you to validate license files via the `License.cryptolens.php` file.
To properly configure this function, please convert the XML-styled public key into PEM format (PKC#1) and save it into the `classes/*` directory as `key.pub` (if `License(<Cryptolens $cryptolens>, <string $pathToKey>)` $pathToKey is set, the path is overwritten by specified one).

You can convert your key on a site like this one [here](https://the-x.cn/en-US/certificate/XmlToPem.aspx) or [using this repository](https://github.com/MisterDaneel/PemToXml)

You can then validate a license key like this:

```php
<?php

require_once "/path/to/autoloader.php";
use Cryptolens_PHP_Client\Cryptolens;
use Cryptolens_PHP_Client\License;

$c = new Cryptolens("YOUR_TOKEN", 12345, Cryptolens::CRYPTOLENS_OUTPUT_PHP);
$l = new License($c); // to specify custom key file location other than /classes/key.pub specify the FULL path as License($c, "/var/www/my/public/key.pub")

$fromString = $l->validateLicense("BASE64-ENCODED-STRING", "BASE64-ENCODED-STRING") // licenseKey and signature
$fromFileContent = $l->validateLicenseFromFileContent(file_get_contents("license.skm")); // License file as string
$fromFile = $l->validateLicenseFromFile("license.skm")

if($fromFile){
  echo "Signature successfully validated";
} else {
  echo "Could not verify signature";
}

```

## Installation

You can either clone this repository and require the `loader.php` (which contains a autoloader) or use composer via console:

```bash
composer require ente/cryptolens-php

```

And

```php
<?php

require "./vendor/autoload.php";
use Cryptolens_PHP_Client\Cryptolens;
...

?>
```

to automatically load the required classes.


## Endpoints

* Key
  * [x] activate
  * [x] deactivate
  * [x] create_key
  * [x] create_trial_key
  * [x] create_key_from_template
  * [x] get_key
  * [x] add_feature
  * [x] block_key
  * [x] extend_license
  * [x] remove_feature
  * [x] unblock_key
  * [x] machine_lock_limit
  * [ ] change_notes\*
  * [ ] change_reseller\*
  * [ ] change_customer\*
  * [ ] Offline Verification
* Customer
  * [x] add_customer
  * [x] edit_customer
  * [x] remove_customer
  * [x] get_customer_licenses (does not support the GetCustomerLicensesBySecret Method, yet)
  * [x] get_customers
* Data Object
* Product
  * [x] get_keys
  * [x] get_products
* Auth
  * [x] key_lock **(Use with caution\*\*)**  
* Payment Form
  * [x] create_session
* Analytics
  * [x] register_event
  * [x] register_events
  * [x] get_events
  * [x] get_object_log
  * [x] get_web_api_log
* Message
  * [x] get_messages
  * [x] create_message
  * [x] remove_message
* Subscription
  * [x] record_usage
* Reseller
  * [x] add_reseller
  * [x] edit_reseller
  * [x] remove_reseller
  * [x] get_resellers
  * [x] get_reseller_customers

* = Considered with less priority, therefore this endpoint will not be implemented, yet.
* ** = This method creates, retrieves or contains sensitive information (e.g. Access Tokens)
