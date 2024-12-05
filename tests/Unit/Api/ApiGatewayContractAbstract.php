<?php

namespace JairForo\VATChecker\Tests\Unit\Api;

use DateTime;
use JairForo\VATChecker\Api\ApiGateway;
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Objects\VATResponse;

/**
 * This file exists as a bridge, creating a best-of-both-worlds situation.
 *
 * - We cannot always query the real API (how would we get our tests to pass offline?)
 * - We cannot always trust the fake API (how would we know the real API still works?)
 */
trait ApiGatewayContractAbstract
{
    abstract protected function getApiGateway(): ApiGateway;

    #[Test]
    /** @test */
    public function should_throw_an_invalid_vat_exception_for_having_an_invalid_country_code()
    {
        $this->expectException(InvalidVATException::class);

        $this->getApiGateway()->check('BR', '11530967');
    }

    #[Test]
    /** @test */
    public function should_throw_an_invalid_vat_exception_for_having_an_invalid_vat_number()
    {
        $this->expectException(InvalidVATException::class);

        $this->getApiGateway()->check('DE', '854502130');
    }

    #[Test]
    /** @test */
    public function should_return_an_response_without_name_and_address_data()
    {
        $response = $this->getApiGateway()->check('DE', '811191002');

        $this->assertInstanceOf(VATResponse::class, $response);

        $this->assertNull($response->company_name);
        $this->assertNull($response->address);
        $this->assertNull($response->zipcode);
        $this->assertNull($response->city);
    }

    #[Test]
    /** @test */
    public function should_return_a_valid_vat_response()
    {
        $response = $this->getApiGateway()->check('RO', '11530967');
        // fwrite(STDERR, print_r($this->getApiGateway(), TRUE));
        $this->assertInstanceOf(VATResponse::class, $response);
        
        $this->assertEquals('RO', $response->country_code);
        $this->assertEquals('11530967', $response->vat_number);
        
        $this->assertEquals(DateTime::createFromFormat('Y-m-dP', date('Y-m-dP')), $response->requested_at);
        $this->assertNotNull($response->company_name);
        $this->assertNotNull($response->address);
        $this->assertNotNull($response->zipcode);
        $this->assertNotNull($response->city);
        $this->assertNotNull($response->original_address);
    }
}
