<?php

use App\Utils\ValidatorUtil;

class ValidatorUtilTest extends TestCase
{
    public function testFormatterWithSameMessage()
    {
        $messages = [
            'email'           => ['missing_field'],
            'phone_number'    => ['missing_field'],
            'emergency_phone' => ['missing_field'],
        ];

        $actualResult = ValidatorUtil::formatter($messages);

        $this->assertCount(1, $actualResult);

        $actualValidatorError = $actualResult[0];

        $this->assertEquals('missing_field', $actualValidatorError->getCode());
        $this->assertContains('email', $actualValidatorError->getFields());
        $this->assertContains('phone_number', $actualValidatorError->getFields());
        $this->assertContains('emergency_phone', $actualValidatorError->getFields());
    }

    public function testFormatterWithDifferentMessage()
    {
        $messages = [
            'email'           => ['missing_field'],
            'phone_number'    => ['missing_field'],
            'emergency_phone' => ['missing_field'],
            'password'        => ['not_enough_password_strength', 'missing_field'],
        ];

        $actualResult = ValidatorUtil::formatter($messages);

        $this->assertCount(2, $actualResult);

        $actualMissingFieldValidatorError = $actualResult[0];
        $actualNEPSFieldValidatorError = $actualResult[1];

        $this->assertEquals('missing_field', $actualMissingFieldValidatorError->getCode());
        $this->assertContains('email', $actualMissingFieldValidatorError->getFields());
        $this->assertContains('phone_number', $actualMissingFieldValidatorError->getFields());
        $this->assertContains('emergency_phone', $actualMissingFieldValidatorError->getFields());
        $this->assertContains('password', $actualMissingFieldValidatorError->getFields());

        $this->assertEquals('not_enough_password_strength', $actualNEPSFieldValidatorError->getCode());
        $this->assertContains('password', $actualNEPSFieldValidatorError->getFields());
    }
}
