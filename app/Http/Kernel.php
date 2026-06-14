<?php
/*
 * Project:     Beacon
 * File:        Kernel.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Http;

use App\Container\Container;
use App\Exceptions\ValidationException;
use App\Routing\RouteDispatcher;
use App\Services\CsrfService;
use App\Support\Flash;
use App\View\View;
use Psr\Log\LoggerInterface;
use Random\RandomException;
use ReflectionException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Kernel
{
    /** @var array<class-string> */
    private array $middleware = [];

    public function __construct(
        private readonly Container $container,
        private readonly RouteDispatcher $dispatcher,
        private readonly Flash $flash,
        private readonly CsrfService $csrf,
        private readonly View $view,
        private readonly LoggerInterface $logger,
    ) { }

    public function addMiddleware(string $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RandomException
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function handle(): Response
    {
        if (config('app.debug')) {
            $this->logger->debug(
                'Request received',
                [
                    'method' => $_SERVER['REQUEST_METHOD'],
                    'uri' => $_SERVER['REQUEST_URI'],
                ]
            );
        }

        $request = $this->container->make(Request::class);

        try {
            $pipeline = array_reduce(
                array_reverse($this->middleware),
                fn (
                    callable $next,
                    string $middleware,
                ) => function (Request $request) use ($next, $middleware) {
                    return $this->container->make($middleware)->handle($request, $next);
                },
                fn (Request $request) => $this->dispatcher->dispatch($request)
            );

            $result = $pipeline($request);

            return $this->normalize($result);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function normalize(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_string($result)) {
            return Response::html($result);
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return Response::html('');
    }

    /**
     * @throws RandomException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function handleException(Throwable $e): Response
    {
        $this->logger->error($e->getMessage() . "\nStacktrace:\n" .$e->getTraceAsString());

        $details = '';
        if (
            config('app.env') === 'local'
            && config('app.debug', false)
        ) {
            $details = sprintf(
                '<br><b>ERROR:</b> %s<br><br>Stack trace:<pre>%s</pre>',
                $e->getMessage(),
                $e->getTraceAsString()
            );
        }

        if ($e instanceof ValidationException) {
            $old = array_diff_key($e->dto()->toArray(),$e->errors());

            // Unset password field in old if exists
            if (array_key_exists('password', $old)) {
                unset($old['password']);
            }

            // Unset confirm_password field in old if exists
            if (array_key_exists('confirm_password', $old)) {
                unset($old['confirm_password']);
            }

            $this->flash->set('errors', $e->errors());
            $this->flash->set('old', $old);
            $this->csrf->regenerate();

            return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        if ($e instanceof ResourceNotFoundException) {
            return Response::html($this->view->render('errors/404.twig'), 404);
        }

        if ($e instanceof MethodNotAllowedException) {
            return Response::html($this->view->render('errors/405.twig'), 405);
        }

        return Response::html(
            $this->view->render(
                'errors/500.twig',
                [
                    'details' => $details
                ]
            ),
            500
        );
    }
}