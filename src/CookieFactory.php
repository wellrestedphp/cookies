<?php

namespace WellRESTed\Cookies;

use DateTime;
use DateTimeZone;

class CookieFactory
{
    const EXPIRED = 'Thu, 01 Jan 1970 00:00:00 GMT';

    private $domain;
    private $path;
    private $secure;
    private $httpOnly;

    public function __construct(
        string $domain,
        string $path,
        bool $secure,
        bool $httpOnly
    ) {
        $this->domain = $domain;
        $this->path = $path;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    public function create(
        string $name,
        string $value,
        $expiration = null
    ): string {
        $cookie = urlencode($name) . '=' . urlencode($value);
        if ($this->domain) {
            $cookie .= '; domain=' . $this->domain;
        }
        if ($this->path) {
            $cookie .= '; path=' . $this->path;
        }
        $cookie .= $this->expirationAttribute($expiration);
        if ($this->secure) {
            $cookie .= '; secure';
        }
        if ($this->httpOnly) {
            $cookie .= '; httpOnly';
        }
        return $cookie;
    }

    private function expirationAttribute($expiration)
    {
        if (is_numeric($expiration)) {
            return '; max-age=' . $expiration;
        }
        if (is_string($expiration)) {
            return '; expires=' . $expiration;
        }
        if ($expiration instanceof DateTime) {
            $expiration->setTimezone(new DateTimeZone('UTC'));
            return '; expires=' . $expiration->format(DateTime::COOKIE);
        }
        return '';
    }
}
