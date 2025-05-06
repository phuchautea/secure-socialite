# SecureSocialite

Secure, stateless OAuth state handling for Laravel Socialite with enhanced security features.

## Description

SecureSocialite provides a robust solution for handling OAuth authentication in Laravel applications using Socialite. It implements secure, encrypted state parameters with automatic expiration and domain whitelisting for callbacks, helping protect your application against CSRF and redirection attacks.

## Installation

You can install the package via Composer:

```bash
composer require tea-software/secure-socialite
```

The package will automatically register its service provider if you're using Laravel's package auto-discovery.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="SecureSocialite\SecureSocialiteServiceProvider" --tag="config"
```

This will create a `config/secure-socialite.php` file with the following contents:

```php
<?php

return [
    'whitelist' => [
        'localhost',
        'localhost:3000',
        'yourapp.com',
    ],
];
```

Update the `whitelist` array to include all domains that are allowed to receive OAuth callbacks. This helps prevent open redirect vulnerabilities.

## Usage

SecureSocialite provides a secure way to handle OAuth authentication flows with Laravel Socialite.

### Basic Example

```php
// Frontend code (JavaScript/Vue/React)
const redirect = encodeURIComponent('https://yourapp.com/auth/callback');
window.location.href = `/auth/social/redirect?provider=google&redirect=${redirect}&nonce=optional_nonce`;
```

The package handles the OAuth flow with the following security features:

1. State parameters are stored securely with encryption
2. Automatic expiration of state tokens (5 minutes)
3. Domain whitelisting for callbacks
4. Stateless implementation for better horizontal scaling

## Routes

The package automatically registers the following routes:

- `GET /auth/social/redirect` - Initiates the OAuth flow
- `GET /auth/social/callback` - Handles the OAuth callback

## Requirements

- PHP 8.0+
- Laravel 8.x or higher

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
