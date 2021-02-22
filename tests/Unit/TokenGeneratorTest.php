<?php

namespace App\Tests\Unit;

use App\Services\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    private TokenGenerator $tokenGenerator;

    protected function setUp() : void
    {
        $this->tokenGenerator = new TokenGenerator();
    }

    public function testSuccessLengthGeneratedToken()
    {
        $expectedLength = 30;
        $factLength = strlen($this->tokenGenerator->generateToken(30));
        $this->assertEquals(
            $expectedLength,
            $factLength,
            "Bad token length, expected {$expectedLength} got {$factLength}"
        );
    }

    public function testSuccessTokenType()
    {
        $token = $this->tokenGenerator->generateToken(20);
        $this->assertEquals(
            true,
            is_string($token),
            'Bad token type'
        );
    }
}
