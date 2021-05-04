# Installation

## Requirements

These instructions assume you already have an existing [Symfony 5 application](https://symfony.com/doc/current/setup.html). 

* PHP 7.4+
* Symfony 5.2+ 

## Composer

```
composer require strata/symfony-frontend:^0.8
```

## Twig helpers

Register these with your `config/services.yaml`:

```yaml
# Register Frontend Twig helpers
Strata\Symfony\TwigExtension:
    tags: ['twig.extension']
```

## Full page caching

Set default cache control response headers for full page caching. 

File: `src/EventListener/ResponseListener.php`:

```php
<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Strata\Data\Cache\CacheLifetime;

class ResponseListener
{
    /**
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        // Add caching layer for Production (1 hour cache on all pages)
        if (getenv('APP_ENV') === 'prod') {
            $response = $event->getResponse();
            $response->setSharedMaxAge(CacheLifetime::HOUR);
            $response->headers->addCacheControlDirective('must-revalidate', true);
        }
    }

}
```

### Varnish
To use HTTP caching either use an HTTP cache proxy such as [Varnish Cache](https://varnish-cache.org/) or you can 
make use of Symfony HTTP Cache with the setup below.

With Varnish, it is still important to output the correct cache control headers to control how long pages are cached for.

### Symfony HTTP Cache
_Note: HTTP cache will be updated to use foshttpcache and tag-based invalidation_

To use HTTP cache for frontend responses update your `public/index.php` bootstrap file. 

After the line:

```php
$kernel = new Kernel($env, $debug);
```

Add:

```php
// Enable HTTP Cache for prod
// @see https://symfony.com/doc/current/http_cache.html
if ('prod' === $env) {
    $kernel = new CacheKernel($kernel);
}
```
