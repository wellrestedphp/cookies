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
        $fields = $this->parseFields($cookie);
        $this->assertEquals('value', $fields['name']);
    }

    // -------------------------------------------------------------------------
    // Domain

    public function testCookieContainsDomain()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertEquals('localhost', $fields['domain']);
    }

    public function testCookieDoesNotContainDomainWhenNotSet()
    {
        $this->domain = '';
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('domain', $fields);
    }

    // -------------------------------------------------------------------------
    // Path

    public function testCookieContainsPath()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertEquals('/', $fields['path']);
    }

    public function testCookieDoesNotContainPathWhenNotSet()
    {
        $this->path = '';
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('path', $fields);
    }

    // -------------------------------------------------------------------------
    // Max-age

    public function testCookieContainsMaxAgeForNumericValue()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value', 3600);
        $fields = $this->parseFields($cookie);
        $this->assertEquals('3600', $fields['max-age']);
    }

    public function testCookieDoesNotContainMaxAgeWhenNotProvided()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('max-age', $fields);
    }

    // -------------------------------------------------------------------------
    // Expires

    public function testCookieContainsExpiresForStringValue()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value', CookieFactory::EXPIRED);
        $fields = $this->parseFields($cookie);
        $this->assertEquals(CookieFactory::EXPIRED, $fields['expires']);
    }

    public function testCookieContainsExpiresForDateTimeValue()
    {
        $factory = $this->createFactory();
        $dateTime = new DateTime('22-Dec-2015 11:43:59 EST');
        $cookie = $factory->create('name', 'value', $dateTime);
        $fields = $this->parseFields($cookie);
        $this->assertEquals('Tuesday, 22-Dec-2015 16:43:59 UTC', $fields['expires']);
    }

    public function testCookieDoesNotContainExpiresNotProvided()
    {
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('expires', $fields);
    }

    // -------------------------------------------------------------------------
    // Secure

    public function testCookieContainsSecureWhenTrue()
    {
        $this->secure = true;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayHasKey('secure', $fields);
    }

    public function testCookieDoesNotContainSecureWhenFalse()
    {
        $this->secure = false;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('secure', $fields);
    }

    // -------------------------------------------------------------------------
    // HttpOnly

    public function testCookieContainsHttpOnlyWhenSetWhenTrue()
    {
        $this->httpOnly = true;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayHasKey('httpOnly', $fields);
    }

    public function testCookieDoesNotContainHttpOnlyWhenFalse()
    {
        $this->httpOnly = false;
        $factory = $this->createFactory();
        $cookie = $factory->create('name', 'value');
        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('httpOnly', $fields);
    }

    // -------------------------------------------------------------------------
    // Removing Cookies

    public function testRemoveCreatesCookieWithNameAndEmptyValue()
    {
        $factory = $this->createFactory();
        $cookie = $factory->remove('name');
        $fields = $this->parseFields($cookie);
        $this->assertEquals('', $fields['name']);
    }

    public function testRemoveCreatesCookieWithExpirationInPast()
    {
        $factory = $this->createFactory();
        $cookie = $factory->remove('name');
        $fields = $this->parseFields($cookie);

        $this->assertArrayHasKey('expires', $fields);

        $expires = $fields['expires'];
        $now = time();
        $this->assertLessThan($now, $expires);
    }

    // -------------------------------------------------------------------------

    private function parseFields(string $cookie): array
    {
        $cookie = str_replace('; ', '&', $cookie);
        parse_str($cookie, $fields);
        return $fields;
    }
}
