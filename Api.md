# API REST :

## JWT AUTH:
composer require lexik/jwt-authentication-bundle
composer require firebase/php-jwt

## INSTALLING SERIALIZER/DESERIALIZER:
composer require symfony/serializer

## Updating database :
php bin/console doctrine:schema:update --force

## Plan Controller :
php bin/console make:controller api\PlansController

## Product Controller :
php bin/console make:controller api\ProductsController

## Auth Controller :
php bin/console make:controller api\AuthController

## Order Controller :
php bin/console make:controller api\OrderController

## Stripe Controller :
php bin/console make:controller api\StripeController

composer create-project symfony/website-skeleton web-service
php bin/console make:controller ProductController
composer require api