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
use App\Pipeline\Pipeline;
use App\Routing\MiddlewareDefinition;
use App\Routing\RouteDispatcher;
use App\Services\CsrfService;
use App\Support\Flash;
use App\View\View;
use JsonException;
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
    /** @var array<MiddlewareDefinition> */
    private array $middleware = [];

    public function __construct(
        private readonly Container $container,
        private readonly RouteDispatcher $dispatcher,
        private readonly Flash $flash,
        private readonly CsrfService $csrf,
        private readonly View $view,
        private readonly LoggerInterface $logger,
        private readonly Pipeline $pipeline,
    ) { }

    public function addMiddleware(string $middleware): void
    {
        $this->middleware[] = new MiddlewareDefinition($middleware);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RandomException
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function handle(): Response
    {
        $request = $this->container->make(Request::class);

        if (config('app.debug')) {
            $this->logger->debug(
                'Request received',
                [
                    'method' => $request->method(),
                    'uri' => $request->uri(),
                ]
            );
        }

        try {
            $result = $this->pipeline
                ->send($request)
                ->through($this->middleware)
                ->then(fn () => $this->dispatcher->dispatch());

            return $this->normalize($result, $request);
        } catch (Throwable $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Normalize response
     *
     * @param mixed $result
     * @param Request $request
     *
     * @return Response
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function normalize(mixed $result, Request $request): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        // API mode (always JSON)
        if ($request->expectsJson()) {
            if (is_array($result)) {
                return Response::json(['success' => true, 'data' => $result]);
            }

            if (is_string($result)) {
                return Response::json(['success' => true, 'message' => $result]);
            }

            return Response::json(['success' => true]);
        }

        // Web mode
        if (is_string($result)) {
            return Response::html($result);
        }

        return Response::html('');
    }

    /**
     * @throws RandomException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws JsonException
     */
    private function handleException(Throwable $e, Request $request): Response
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
            if ($request->expectsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            $old = array_diff_key($e->dto()->toArray(), $e->errors());

            $keysToRemove = ['password', 'password_confirm', 'password_current'];
            foreach ($keysToRemove as $key) {
                unset($old[$key]);
            }

            $this->flash->set('errors', $e->errors());
            $this->flash->set('old', $old);
            $this->csrf->regenerate();

            return Response::redirect($request->referer());
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