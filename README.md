# Beacon - The Foundation for Modern PHP Applications

![Logo with Brand and Slogan](./public/assets/img/logo-brand-and-slogan-800x237.png)

## About Beacon
Beacon is a modern PHP application starter framework designed for developers who value simplicity, maintainability, and rapid development.

It combines proven architectural concepts such as dependency injection, service providers, middleware, routing, validation, authentication, database migrations, and Eloquent ORM into a clean, lightweight, and developer-friendly foundation.

Unlike many frameworks that require extensive initial setup, Beacon is distributed as a complete project skeleton, including a fully integrated authentication system with user registration, login, password reset, remember-me functionality, and email verification. This allows developers to focus on building their applications rather than spending time on repetitive boilerplate configuration.

Beacon also includes a lightweight Neumorphism-based CSS starter design featuring commonly used UI components such as cards, buttons, form controls, alerts, and popovers. The design serves as a practical starting point and can be customized or replaced entirely to suit your project's requirements.

Whether you're building a personal website, business application, administration panel, or custom web platform, Beacon provides a solid and extensible foundation to get started quickly.
## Features
- Modern MVC architecture
- Dependency Injection Container
- Service Providers
- Middleware Pipeline
- Named Routes
- Twig Templating
- Eloquent ORM
- Database Migrations
- Authentication System
- Remember Me Authentication
- CSRF Protection
- DTO-based Validation
- Flash Messages
- Console Commands
- Environment Configuration
- PSR-4 Autoloading
- Docker Development Environment (with HTTPS support)

## Requirements
- PHP 8.5+
- Composer
- MariaDB / MySQL
- Docker (optional)

## Installation
1. Create a new Beacon project:
```shell
composer create-project shworx/beacon my-project
```
2. Enter the project directory:
```shell
cd my-project
```
 
3. Install dependencies:
```shell
composer install
```

4. Copy the environment configuration:
```shell
cp .env.example .env
```

5. Generate an application secret:
```shell
php console app:key
```

6. Copy the generated application secret to the `.env` file (`APP_SECRET`)

7. Run database migrations:
```shell
php console migrate
```

8. Start your web server and begin building.

## Project Structure
```plain
project-directory
├── app
│    ├── Console
│    │    └── Commands      // Contains all the Console commands
│    ├── Container
│    ├── Controllers        // Contains all Controllers
│    ├── Database
│    ├── DTO                // Contains all the DTO's
│    ├── Enums
│    ├── Exceptions
│    ├── Helpers
│    ├── Http
│    ├── Interfaces
│    ├── Middleware
│    ├── Models             // Contains all Models
│    ├── Providers
│    ├── Routing
│    ├── Services
│    ├── Support
│    └── View
├── bootstrap
├── config
├── console
├── database
│    └── migrations
├── docker
├── public
│    ├── assets
│    │    ├── css
│    │    ├── js
│    │    ├── favicons      
│    │    └── img
├── resources
│    ├── stubs
│    └── views              // Contains all the Twig templates
├── routes
│    └── web.php            // This is where the routes are defined
├── storage
└── tests
```

## Console Commands

List all available commands: `php console`<br>
Create Migration: `php console make:migration create_users_table`<br>
Run Migrations: `php console migrate`<br>
Rollback Migrations: `php console migrate:rollback`<br>
Generate Application Secret: `php console app:key`<br>

## Routing Examples
### Basic routes
```php
// routes/web.php
$router->get('/', [HomeController::class, 'index'], 'home');
$router->post('/', [MyController::class, 'submit'], 'home.submit');
```
### Routes with parameter
```php
// routes/web.php
$router->get('/my-route/{first}/{second}', [MyController::class, 'myMethod'], 'my-name');

// app/Controllers/MyController.php
public function myMethod($first, $second): Response {
    ...
}
```
### Route groups
```php
$router->group(
    prefix: '',
    callback: function (Router $router) {
        $router->get('/dashboard', [DashboardController::class, 'index'], 'dashboard');
    },
    middleware: [AuthMiddleware::class]
);
```

Generate URLs in Twig:
```html
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('my-name', {'first' : 'first_value', 'second' : 'second_value'}) }}"
```
## Validation
Beacon integrates Symfony Validator with DTO-based validation.

Example:
```php
// app/DTO/LoginDto.php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginDto
{
public function __construct(
#[Assert\NotBlank]
#[Assert\Email]
public string $email,

        #[Assert\NotBlank]
        public string $password,
    ) {}
}
```

Validate example:
```php
// app/Controllers/LoginController.php

$dto = LoginDto::fromArray($request->all());
$validator->validate($dto);
```

## Database
Beacon uses Laravel's Eloquent ORM.
```php
$user = User::query()
    ->where('email', $email)
    ->first();
```

With relation:
```php
$prt = PasswordResetToken::with('user')
    ->where('token_hash', $tokenHash)
    ->first();
```

Create migrations:
```shell
php console make:migration create_posts_table
```

Run migrations:
```php
php console migrate
```

## Philosophy
Beacon aims to provide:

- A clean architecture
- Modern PHP practices
- Minimal magic
- Maximum readability
- Fast project setup
- Full developer control

Beacon does not try to hide PHP. Instead, it embraces PHP and provides a solid foundation for building maintainable applications.

## License
Beacon is open-sourced software licensed under the [MIT License](./LICENSE.md).

## Local Docker environment setup
Beacon comes with a pre-configured Docker container setup, consisting of 3 containers:
- `beacon-webserver` (Nginx, PHP 8.5)
- `beacon-maria` (MariaDB 11.5)
- `beacon-mailpit` (Mailpit [latest])

#### Generate SSL cert & key for local Docker environment
To run the local Docker container setup, you should first generate a fresh SSL cert and key. To do so, follow the steps below.
##### Generating SSL cert
```shell
openssl req -x509 -nodes -days 365 -subj "/C=CA/ST=QC/O=SHWorX/CN=beacon.local" -addext "subjectAltName=DNS:beacon.local" -newkey rsa:2048 -keyout ./docker/config/ssl/beacon.local.key -out ./docker/config/ssl/beacon.local.cert;
```
> **Important notes:**
> 1. If you have changed the CN value `beacon.local` to any other local domain you want to use, then you also need to update the value for `server_name` (line 13), and the value in `add_header` (line 88) in `docker/config/conf.d/default.conf`.
> You also need to update `APP_URL` in `.env` to match your local domain. 
> 2. If you have changed the file names for the cert and the key, then you also need to update the entries in `docker/config/conf.d/self-signed.conf`.

##### Generating Diffie-Hellman params
```shell
openssl dhparam -out ./docker/config/ssl/dhparam.pem 4096
```

You can now start the local Docker environment on CLI via Docker compose.
```shell
docker compose up -d
```
### Helper aliases inside `beacon-webserver` container
The `beacon-webserver` container comes along with some helper aliases:
- `clearlogs` This alias clears the logs:  
`/var/log/nginx/access.log` (`docker/logs/access.log`),  
`/var/log/nginx/error.log` (`docker/logs/error.log`),

## Author

Beacon is created and maintained by

**Steffen Haase**<br>
**SHWorX** Development

Website: [https://shworx.com](https://shworx.com)<br>
GitHub: [https://github.com/SHWorX](https://github.com/SHWorX)
