# Stancer Payment module for Magento 2

This official module allows you to accept credit card payments via the Stancer platform directly on Magento 2.x.

---

## Requirements

### Minimal versions

| Magento version   | PHP Version      |
|-------------------|------------------|
| 2.x               | 7.4 or greater   |

## API keys

In order to configure the Magento 2 module, you need Stancer API keys. You can find your keys in the <q>Developers</q>
tab on your [Stancer account](https://manage.stancer.com).

When creating your account, a private and public key is automatically generated for test mode. Live mode keys will be
created after account validation.

## Supported payment method

The module allows you to make payments by credit card.

Payments are 3D Secure compatible. The amount from which 3D Secure is triggered can be configured from
the module in the Magento Admin panel.

---

# Install

Creating an account in the Magento Marketplace is not typically required when manually installing extensions,
as manual installation does not involve Magento Marketplace authentication keys.

However, if you are using the Magento 2 Marketplace to download extensions,
you might need to set up your authentication keys.
[Here's](https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/prerequisites/authentication-keys.html)
how you can do it.

# Magento Marketplace

The recommended way of installing is through Magento Marketplace, where you can find
[The Official Stancer Payment Module](https://commercemarketplace.adobe.com/stancer-module-payments.html).

---

# Manual installation and configure

## 1. Backup Your Website

Before starting, back up your website, including the database and files.

## 2. Download

Download the extension package to your local machine, host, or server.

## 3. Unzip the Extension

Navigate to the Magento 2 installation directory and extract the contents of the ZIP file
to the `app/code/StancerIntegration/Payments` directory.

## 4. Enable maintenance mode

Enable the maintenance mode by running the following command:

```bash
php bin/magento maintenance:enable
```

## 5. Update `composer.json`:

Open the `composer.json` file in your Magento root directory and add a reference to the extension you just uploaded.
You can do this by adding a repositories section to your `composer.json` file.

```json
"repositories": [
    {
        "type": "path",
        "url": "app/code/StancerIntegration/Payments"
    }
]
```

## 6. Require the Extension:

In the command line, use Composer to require the extension.
Run the following command:

```bash
composer require stancer/cms-magento
```

## 7. Enable the Extension:

After the installation, you need to enable the extension.
Run the following commands:

```bash
php bin/magento module:enable StancerIntegration_Payments
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:clean
```

## 8. Deploy Static Content:

```bash
php bin/magento setup:static-content:deploy -f
```

## 9. Flush Cache:

Finally, flush the cache to ensure that your changes take effect:

```bash
php bin/magento cache:flush
```

## 10. Disable maintenance mode

```bash
php bin/magento maintenance:disable
```

## 11. Login to Magento 2 Admin panel.

Navigate to this page `Stores -> Configuration -> Sales -> Payment Methods`.

## 12. Configure the Stancer Payment module:

* Expand Stancer configuration by clicking on `Configure` button.

* Select `Environment` (default: test)

* Enter your `Public` and `Private` keys.

* Choose desired `Payment Flow`.

* Config `3D Secure` if you want to activate secure payments.

* Save Config.


## API Library

This module is using the [Stancer API Library for PHP](https://gitlab.com/wearestancer/library/lib-php).


## License

MIT license.

For more information, see the LICENSE file.
