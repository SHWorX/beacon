<?php
/*
 * Project:     Beacon
 * File:        Response.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Http;

use JsonException;

final readonly class Response
{
    /**
     * Constructor
     *
     * @param array $headers Headers
     */
    public function __construct(
        private array  $headers = [],
        private string $content = '',
        private int    $status = 200,
    ) { }

    /**
     * Send an HTML response
     *
     * @param string $content The HTML content
     * @param int $status     [optional] The HTTP status code (default: 200)
     *
     * @return Response
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function html(string $content, int $status = 200): self
    {
        return new self(['Content-Type' => 'text/html; charset=UTF-8'], $content, $status);
    }

    /**
     * Send a JSON response
     *
     * @param array $data Data to send as a JSON response
     * @param int $status [optional] The HTTP status code (default: 200)
     *
     * @return Response
     * @throws JsonException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function json(array $data, int $status = 200): self
    {
        $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        return new self(['Content-Type' => 'application/json'], $data, $status);
    }


    /**
     * Redirect to URL
     *
     * @param string $url The URL or route for the redirect
     * @param int $status [optional] The HTTP status code (default: 200)
     *
     * @return Response
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function redirect(string $url, int $status = 302): self
    {
        return new self(['Location' => $url], '', $status);
    }

    /**
     * Sends a response
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header(sprintf('%s: %s', $key, $value));
        }

        echo $this->content;
    }
}