<?php

namespace JairForo\VATAutoComplete;

use PHPUnit\Framework\TestCase;

class VATAutoCompleteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldNotHaveAnInvalidCountryCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new VATAutoComplete('BR', '854502130B01');
    }

    public function testShouldReturnAValidEntity(): void
    {
        $vatInfo = (new VATAutoComplete('NL', '854502130B01'))->checkVAT();

        $this->assertArrayHasKey('country_code', $vatInfo);
        $this->assertArrayHasKey('vat_number', $vatInfo);
        $this->assertArrayHasKey('valid', $vatInfo);
        $this->assertArrayHasKey('company_name', $vatInfo);
        $this->assertArrayHasKey('address', $vatInfo);
    }

    public function testShouldReturnAGermanEntityWithoutNameAndAddressData(): void
    {
        $vatInfo = (new VATAutoComplete('DE', '811191002'))->checkVAT();

        $this->assertNull($vatInfo['company_name']);
        $this->assertNull($vatInfo['address']);
        $this->assertNull($vatInfo['postcode']);
        $this->assertNull($vatInfo['city']);
    }

    public function testShouldReturnAFalseVat(): void
    {
        $vatInfo = (new VATAutoComplete('NL', '4502130B01'))->checkVAT();

        $this->assertFalse($vatInfo['valid']);
    }
}
