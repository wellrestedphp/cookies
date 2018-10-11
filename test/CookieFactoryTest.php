<?php

namespace WellRESTed\Test\Cookies;

use DateTime;
use PHPUnit\Framework\TestCase;
use WellRESTed\Cookies\CookieFactory;

class CookieFactoryTest extends TestCase
{
    private $domain;
    private $path;
    private $secure;
    private $httpOnly;

    public function setUp()
    {
        parent::setUp();
        $this->domain = 'localhost';
        $this->path = '/';
        $this->secure = true;
        $this->httpOnly = true;
    }

    private function createFactory()
    {
        return new CookieFactory(
            $this->domain,
            $this->path,
            $this->secure,
            $this->httpOnly);
    }

    // -------------------------------------------------------------------------
    // Name and value

    public function testCreatesCookieWithNameAndValue()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertContains('name=value', $cookie);
    }

    // -------------------------------------------------------------------------
    // Domain

    public function testCookieContainsDomain()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertContains('domain=localhost', $cookie);
    }

    public function testCookieDoesNotContainDomainWhenNotSet()
    {
        $this->domain = '';
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertNotContains('domain', $cookie);
    }

    // -------------------------------------------------------------------------
    // Path

    public function testCookieContainsPath()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertContains('path=/', $cookie);
    }

    public function testCookieDoesNotContainPathWhenNotSet()
    {
        $this->path = '';
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertNotContains('path', $cookie);
    }

    // -------------------------------------------------------------------------
    // Max-age

    public function testCookieContainsMaxAgeForNumericValue()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value', 3600);
        $this->assertContains('max-age=3600', $cookie);
    }

    public function testCookieDoesNotContainMaxAgeWhenNotProvided()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertNotContains('max-age', $cookie);
    }

    // -------------------------------------------------------------------------
    // Expires

    public function testCookieContainsExpiresForStringValue()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value', CookieFactory::EXPIRED);
        $this->assertContains('expires=Thu, 01 Jan 1970 00:00:00 GMT', $cookie);
    }

    public function testCookieContainsExpiresForDateTimeValue()
    {
        $factory = $this->createFactory();
        $dateTime = new DateTime('22-Dec-2015 11:43:59 EST');
        $cookie = $factory->create('name', 'value', $dateTime);
        $this->assertContains('expires=Tuesday, 22-Dec-2015 16:43:59 UTC', $cookie);
    }

    public function testCookieDoesNotContainExpiresNotProvided()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertNotContains('expires', $cookie);
    }

    // -------------------------------------------------------------------------
    // Secure

    public function testCookieContainsSecureWhenTrue()
    {
        $this->secure = true;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertContains('secure', $cookie);
    }

    public function testCookieDoesNotContainSecureWhenFalse()
    {
        $this->secure = false;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertNotContains('secure', $cookie);
    }

    // -------------------------------------------------------------------------
    // HttpOnly

    public function testCookieContainsHttpOnlyWhenSetWhenTrue()
    {
        $this->httpOnly = true;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertContains('httpOnly', $cookie);
    }

    public function testCookieDoesNotContainHttpOnlyWhenFalse()
    {
        $this->httpOnly = false;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $this->assertNotContains('httpOnly', $cookie);
    }
}
