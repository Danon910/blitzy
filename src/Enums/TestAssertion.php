<?php

declare(strict_types=1);

namespace Danon910\blitzy\Enums;

enum TestAssertion: string
{
    case RESPONSE_OK = 'response_ok';
    case RESPONSE_UNPROCESSABLE = 'response_unprocessable';
    case RESPONSE_UNAUTHORIZED = 'response_unauthorized';
    case RESPONSE_NOT_FOUND = 'response_not_found';
    case RESPONSE_JSON_STRUCTURE = 'response_json_structure';

    public function content(): string
    {
        return match($this)
        {
            self::RESPONSE_OK => '$response->assertOk();',
            self::RESPONSE_UNPROCESSABLE => '$response->assertUnprocessable();',
            self::RESPONSE_UNAUTHORIZED => '$response->assertUnauthorized();',
            self::RESPONSE_NOT_FOUND => '$response->assertNotFound();',
            self::RESPONSE_JSON_STRUCTURE => '$response->assertJsonStructure($this->getExpectedJsonStructure());',
        };
    }
}
