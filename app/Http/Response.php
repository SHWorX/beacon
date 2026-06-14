<?php
/*
 * Project:     Beacon
 * File:        Response.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Http;

final readonly class Response
{
    /**
     * Constructor
     * @param string $content Content
     * @param int $status HTTP Status
     * @param array $headers Headers
     */
    public function __construct(
        private string $content = '',
        private int    $status = 200,
        private array  $headers = []
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
        return new self($content, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * Send a JSON response
     *
     * @param array $data Data to send as a JSON response
     * @param int $status [optional] The HTTP status code (default: 200)
     *
     * @return Response
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function json(array $data, int $status = 200): self
    {
        $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return new self($data, $status, ['Content-Type' => 'application/json']);
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
        return new self('', $status, ['Location' => $url]);
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