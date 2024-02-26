
<p align="center"><a href="https://laravel.com" target="_blank">
  <img src="https://stegback.com/root/storage/uploads/white-logo.png" width="400" alt="Stegback Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/stegback/ratepay"><img src="https://img.shields.io/packagist/dt/stegback/ratepay" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/stegback/ratepay"><img src="https://img.shields.io/packagist/v/stegback/ratepay" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/stegback/ratepay"><img src="https://img.shields.io/packagist/l/stegback/ratepay" alt="License"></a>
</p>


- [**Documentation**](https://stegback-ratepay.document360.io/docs).

**Step 1: Install the Package via Composer**
```cmd
composer require stegback/ratepay
```

**Step 2: Define secret code in your env file**

```env

SHOP_NAME="MyWebsite"
PROFILE_ID=""
SECURITY_CODE=""
```

**Step 3: Define Service provider in config/app.php in providers**

```php
\Stegback\Ratepay\RatepayServiceProvider::class,
```
