<?php

declare(strict_types=1);

namespace Spacetab\Tests\BodyValidator;

use Amp\PHPUnit\AsyncTestCase;
use HarmonyIO\Validation\Result\Error;
use Spacetab\BodyValidator\BodyValidator;
use Spacetab\BodyValidator\Combinator\Sometimes;
use Spacetab\BodyValidator\NullBody;
use HarmonyIO\Validation\Rule\Combinator\All;
use HarmonyIO\Validation\Rule\Email\RfcEmailAddress;
use HarmonyIO\Validation\Rule\Network\Url\Url;
use HarmonyIO\Validation\Rule\Text\AlphaNumeric;
use HarmonyIO\Validation\Rule\Text\LengthRange;
use Spacetab\BodyValidator\BodyValidatorInterface;

class BodyValidatorTest extends AsyncTestCase
{
    public function testConstructorInitialization()
    {
        $validator = new BodyValidator([], new NullBody());

        $this->assertInstanceOf(BodyValidator::class, $validator);
    }

    public function testUnsuccessfulValidation()
    {
        $body = [
            'username' => '__roquie',
            'password' => '1',
            'contacts' => [
                [
                    'email' => '1mail@@example.com',
                    'github' => 'https:/github.com/roquie1',
                ],
                [
                    'email' => '2mail@@example.com',
                    'github' => 'https:/github.com/roquie2',
                ],
            ],
        ];



        $validator = new BodyValidator($body, new class implements BodyValidatorInterface {
            public function validate(): iterable {
                yield 'username' => new All(new LengthRange(3, 15), new AlphaNumeric());
                yield 'password' => new LengthRange(12, 255);
                yield 'contacts.*.email' => new RfcEmailAddress();
                yield 'contacts.*.github' => new Url();
            }
        });

        /** @var \Spacetab\BodyValidator\ResultSet $result */
        $result = yield $validator->verify();
        $errors = $result->getErrors();
        $keys = array_keys($errors);

        $this->assertFalse($result->isValid());

        foreach ($errors as $values) {
            foreach ($values as $error) {
                $this->assertInstanceOf(Error::class, $error);
            }
        }

        $this->assertSame('username', $keys[0]);
        $this->assertSame('password', $keys[1]);
        $this->assertSame('contacts.0.email', $keys[2]);
        $this->assertSame('contacts.1.email', $keys[3]);
        $this->assertSame('contacts.0.github', $keys[4]);
        $this->assertSame('contacts.1.github', $keys[5]);
    }

    public function testSuccessfulValidation()
    {
        $body = [
            'username' => 'roquie'
        ];

        $validator = new BodyValidator($body, new class implements BodyValidatorInterface {
            public function validate(): iterable {
                yield 'username' => new All(new LengthRange(3, 15), new AlphaNumeric());
            }
        });

        /** @var \Spacetab\BodyValidator\ResultSet $result */
        $result = yield $validator->verify();

        $this->assertTrue($result->isValid());
    }

    public function testValidationWithEmptySometimesCombinator()
    {
        $validator = new BodyValidator([], new class implements BodyValidatorInterface {
            public function validate(): iterable {
                yield 'username' => new Sometimes(new LengthRange(3, 15), new AlphaNumeric());
            }
        });

        /** @var \Spacetab\BodyValidator\ResultSet $result */
        $result = yield $validator->verify();

        $this->assertTrue($result->isValid());
    }

    public function testValidationWithExistsValueForSometimesCombinator()
    {
        $body = [
            'username' => '__roquie'
        ];

        $validator = new BodyValidator($body, new class implements BodyValidatorInterface {
            public function validate(): iterable {
                yield 'username' => new Sometimes(new LengthRange(3, 15), new AlphaNumeric());
            }
        });

        /** @var \Spacetab\BodyValidator\ResultSet $result */
        $result = yield $validator->verify();

        $this->assertFalse($result->isValid());
    }

    public function testValidationWithDuplicatedChecksWhereErrorsMustBeUnique()
    {
        $body = [
            'username' => null
        ];

        $validator = new BodyValidator($body, new class implements BodyValidatorInterface {
            public function validate(): iterable {
                yield 'username' => new All(new LengthRange(3, 15), new AlphaNumeric());
            }
        });

        /** @var \Spacetab\BodyValidator\ResultSet $result */
        $result = yield $validator->verify();

        $this->assertFalse($result->isValid());

        foreach ($result->getErrors() as $errors) {
            $this->assertCount(1, $errors);
            break;
        }
    }
}
