<?php

namespace WellRESTed\Cookies;

use DateTime;
use DateTimeZone;

class CookieFactory
{
    const EXPIRED = 'Thu, 01 Jan 1970 00:00:00 GMT';
    const HTTP_DATE = 'D, d M Y H:i:s T';

    /** @var string */
    private $domain;
    /** @var string */
    private $path;
    /** @var bool */
    private $secure;
    /** @var bool */
    private $httpOnly;

    /**
     * @param string $domain The (sub)domain the cookie is available to
     * @param string $path The path the cookie is available in
     * @param bool $secure The cookie should only be transmitted over a secure
     *    HTTPS connection from the client.
     * @param bool $httpOnly The cookie will be made accessible only through the
     *   HTTP protocol. This means that the cookie won't be accessible by
     *   scripting languages, such as JavaScript.
     */
    public function __construct(
        string $domain = '',
        string $path = '',
        bool $secure = false,
        bool $httpOnly = false
    ) {
        $this->domain = $domain;
        $this->path = $path;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * Create a value for a set-cookie header given a cookie name, value, and
     * optional expiration. When no expiration is passed, the cookie will be
     * created as a session cookie.
     *
     * $expiration may be pass in the following forms:
     *   - numeric: max-age in seconds
     *   - string: Expiration time (expires) as an HTTP-date
     *   - DateTime: Expiration time
     *   - null: Session cookie
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Date
     *
     * @param string $name
     * @param string $value
     * @param mixed $expiration
     * @return string
     */
    public function create(
        string $name,
        string $value,
        $expiration = null
    ): string {
        $cookie = urlencode($name) . '=' . urlencode($value);
        if ($this->domain) {
            $cookie .= '; Domain=' . $this->domain;
        }
        if ($this->path) {
            $cookie .= '; Path=' . $this->path;
        }
        $cookie .= $this->expirationAttribute($expiration);
        if ($this->secure) {
            $cookie .= '; Secure';
        }
        if ($this->httpOnly) {
            $cookie .= '; HttpOnly';
        }
        return $cookie;
    }

    private function expirationAttribute($expiration)
    {
        if (is_numeric($expiration)) {
            return '; Max-Age=' . $expiration;
        }
        if (is_string($expiration)) {
            return '; Expires=' . $expiration;
        }
        if ($expiration instanceof DateTime) {
            $expiration->setTimezone(new DateTimeZone('GMT'));
            return '; Expires=' . $expiration->format(self::HTTP_DATE);
        }
        return '';
    }

    /**
     * Create a value for a set-cookie header that will unset the cookie with
     * given name.
     *
     * @param string $name
     * @return string
     */
    public function remove(string $name): string
    {
        return $this->create($name, '', self::EXPIRED);
    }
}
