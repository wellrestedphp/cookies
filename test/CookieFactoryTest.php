<?php

namespace WellRESTed\Test\Cookies;

use DateTime;
use PHPUnit\Framework\TestCase;
use WellRESTed\Cookies\CookieFactory;

class CookieFactoryTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Name and value

    public function testCreatesCookieWithNameAndValue()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertEquals('value', $fields['name']);
    }

    // -------------------------------------------------------------------------
    // Domain

    public function testCookieContainsDomain()
    {
        $factory = new CookieFactory('localhost');
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertEquals('localhost', $fields['Domain']);
    }

    public function testCookieDoesNotContainDomainWhenNotSet()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('Domain', $fields);
    }

    // -------------------------------------------------------------------------
    // Path

    public function testCookieContainsPath()
    {
        $factory = new CookieFactory('localhost', '/');
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertEquals('/', $fields['Path']);
    }

    public function testCookieDoesNotContainPathWhenNotSet()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('Path', $fields);
    }

    // -------------------------------------------------------------------------
    // Max-age

    public function testCookieContainsMaxAgeForNumericValue()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value', 3600);

        $fields = $this->parseFields($cookie);
        $this->assertEquals('3600', $fields['Max-Age']);
    }

    public function testCookieDoesNotContainMaxAgeWhenNotProvided()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('Max-Age', $fields);
    }

    // -------------------------------------------------------------------------
    // Expires

    public function testCookieContainsExpiresForStringValue()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value', CookieFactory::EXPIRED);

        $fields = $this->parseFields($cookie);
        $this->assertEquals(CookieFactory::EXPIRED, $fields['Expires']);
    }

    public function testCookieContainsExpiresForDateTimeValue()
    {
        $factory = new CookieFactory();
        $dateTime = new DateTime('22-Dec-2015 11:43:59 EST');
        $cookie = $factory->create('name', 'value', $dateTime);

        $fields = $this->parseFields($cookie);
        $this->assertEquals('Tue, 22 Dec 2015 16:43:59 GMT', $fields['Expires']);
    }

    public function testCookieDoesNotContainExpiresNotProvided()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('Expires', $fields);
    }

    // -------------------------------------------------------------------------
    // Secure

    public function testCookieContainsSecureWhenTrue()
    {
        $factory = new CookieFactory('localhost', '/', true);
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayHasKey('Secure', $fields);
    }

    public function testCookieDoesNotContainSecureWhenFalse()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('Secure', $fields);
    }

    // -------------------------------------------------------------------------
    // HttpOnly

    public function testCookieContainsHttpOnlyWhenSetWhenTrue()
    {
        $factory = new CookieFactory('localhost', '/', true, true);
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayHasKey('HttpOnly', $fields);
    }

    public function testCookieDoesNotContainHttpOnlyWhenFalse()
    {
        $factory = new CookieFactory();
        $cookie = $factory->create('name', 'value');

        $fields = $this->parseFields($cookie);
        $this->assertArrayNotHasKey('HttpOnly', $fields);
    }

    // -------------------------------------------------------------------------
    // Removing Cookies

    public function testRemoveCreatesCookieWithNameAndEmptyValue()
    {
        $factory = new CookieFactory();
        $cookie = $factory->remove('name');

        $fields = $this->parseFields($cookie);
        $this->assertEquals('', $fields['name']);
    }

    public function testRemoveCreatesCookieWithExpirationInPast()
    {
        $now = time();

        $factory = new CookieFactory();
        $cookie = $factory->remove('name');
        $fields = $this->parseFields($cookie);

        $this->assertArrayHasKey('Expires', $fields);

        $expires = $fields['Expires'];
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
