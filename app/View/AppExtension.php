<?php
/*
 * Project:     Beacon
 * File:        AppExtension.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\View;

use App\Routing\Router;
use App\Services\AuthService;
use App\Services\CsrfService;
use App\Support\Flash;
use Random\RandomException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly Router $router,
        private readonly CsrfService $csrf,
        private readonly Flash $flash,
        private readonly AuthService $auth,
    ) { }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route', [$this, 'route']),
            new TwigFunction('old', [$this, 'old']),
            new TwigFunction('error', [$this, 'error']),
            new TwigFunction('flash', [$this, 'flash']),
            new TwigFunction('guest', [$this, 'guest']),
            new TwigFunction('currentRoute', [$this, 'currentRoute']),
            new TwigFunction('isActiveRoute', [$this, 'isActiveRoute']),
            new TwigFunction('csrf', [$this, 'csrf'], ['is_safe' => ['html']]),
            new TwigFunction('asset', [$this, 'asset']),
            new TwigFunction('app_url', [$this, 'app_url']),
        ];
    }

    /**
     * Returns a route
     *
     * @param string $name
     * @param array $parameters
     *
     * @return string
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function route(
        string $name,
        array $parameters = [],
    ): string {
        return $this->router->route($name, $parameters);
    }


    /**
     * Returns the "old" form field values
     *
     * @param string $field
     *
     * @return mixed
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function old(string $field): mixed {
        return $this->flash->old()[$field] ?? null;
    }


    /**
     * Return errors
     *
     * @param string $field
     *
     * @return string|null
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function error(string $field): mixed
    {
        return $this->flash->errors()[$field] ?? null;
    }

    /**
     * Return flash messages
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function flash(string $key, mixed $default = null): mixed
    {
        return $this->flash->pull($key, $default);
    }

    /**
     * Checks if a user is a guest or not
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function guest(): bool
    {
        return $this->auth->guest();
    }

    /**
     * Returns the current route
     *
     * @return string|null
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function currentRoute(): ?string
    {
        return current_route();
    }

    /**
     * Checks if the route is the active route
     *
     * Note: This works only if the route is defined with a name.
     *
     * @param string $name
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function isActiveRoute(string $name): bool
    {
        return $this->currentRoute() === $name;
    }

    /**
     * Returns a form element with a CSRF token
     *
     * @return string
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function csrf(): string
    {
        return sprintf(
            '<input type="hidden" name="_csrf" value="%s">',
            htmlspecialchars($this->csrf->token(), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Returns an "asset" URL
     *
     * @param $path
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function asset($path): string
    {
        return asset($path);
    }

    /**
     * Returns an application URL
     *
     * @param string|null $path [optional] URL path
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function app_url(?string $path = null): string
    {
        return app_url($path);
    }
}