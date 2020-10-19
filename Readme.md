[![Build Status](https://travis-ci.org/JohnyRicio/ares.svg?branch=master)](https://travis-ci.org/JohnyRicio/ares)
[![codecov.io](https://codecov.io/github/JohnyRicio/ares/coverage.svg?branch=master)](https://codecov.io/github/Johnyricio/ares?branch=master)

Ares
---
This package helps to read data from Czech govermant register named Ares and transfer it to easy readable VO.

How to install
---
```
composer require stepina/registry-ares
```

Example:
---
```php
    $ares = new \registryAres\src\Ares\Ares(new \GuzzleHttp\Client());
    $dataAres = $ares->getByCompanyId('123123123');
```

Or use Dependency injection

getByCompanyId will return \registryAres\src\Ares\AresVO
