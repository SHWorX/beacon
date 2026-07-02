<?php
/*
 * Project:     beacon
 * File:        ThrottleMiddleware.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Generators\RateLimitKeyGenerator;
use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;
use App\Security\RateLimiter;
use App\Security\RateLimitResult;
use App\Services\AuthService;
use App\View\View;
use JsonException;
use Random\RandomException;
use Throwable;

readonly class ThrottleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthService $auth,
        private View $view,
        private RateLimiter $limiter,
        private RateLimitKeyGenerator $generator,
    ) {}

    /**
     * @throws RandomException
     * @throws JsonException
     * @throws Throwable
     */
    public function handle(Request $request, callable $next, ...$parameters): Response
    {
        $maxAttempts = (int) ($parameters[0] ?? 60);
        $retryAfter = (int) ($parameters[1] ?? 60);

        $key = $this->generator->generate(
            $request->method(),
            $request->uri(),
            $this->auth->userId(),
            $request->ip(),
            current_route()
        );

        $result = $this->limiter->hit($key, $maxAttempts, $retryAfter);

        if (!$result->allowed) {
            return $this->tooManyRequestsResponse($request, $result);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @param RateLimitResult $result
     *
     * @return Response
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function tooManyRequestsResponse(Request $request, RateLimitResult $result): Response
    {
        $headers = [
            'X-RateLimit-Limit' => (string)$result->limit,
            'X-RateLimit-Remaining' => (string)$result->remaining,
            'X-RateLimit-Retry-After' => (string)$result->retry_after,
        ];

        if (!$result->allowed) {
            $headers['Retry-After'] = (string)$result->retry_after;
        }

        if ($request->expectsJson()) { // API request
            $headers['Content-Type'] = 'application/json';
            $content = json_encode([
                'message' => 'Too many requests.',
                'retry_after' => $this->formatRetryAfter($result->retry_after),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            return new Response(
                $headers,
                $content,
                429,
            );
        } else { // Web request
            $headers['Content-Type'] = 'text/html; charset=UTF-8';

            return new Response(
                $headers,
                $this->view->render('errors/429.twig', ['retry_after' => $this->formatRetryAfter($result->retry_after)]),
                429
            );
        }
    }

    /**
     * Format the retry after value
     *
     * @param int $seconds
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function formatRetryAfter(int $seconds): string
    {
        $minutes = intdiv($seconds, 60);
        $seconds = $seconds % 60;

        if ($minutes === 0) {
            return sprintf('%d second%s', $seconds, $seconds === 1 ? '' : 's');
        }

        if ($seconds === 0) {
            return sprintf('%d minute%s', $minutes, $minutes === 1 ? '' : 's');
        }

        return sprintf(
            '%d minute%s %d second%s',
            $minutes,
            $minutes === 1 ? '' : 's',
            $seconds,
            $seconds === 1 ? '' : 's'
        );
    }
}
