Async PHP Body Validator 
========================

[![CircleCI](https://circleci.com/gh/spacetab-io/body-validator-php/tree/master.svg?style=svg)](https://circleci.com/gh/spacetab-io/body-validator-php/tree/master)
[![codecov](https://codecov.io/gh/spacetab-io/body-validator-php/branch/master/graph/badge.svg)](https://codecov.io/gh/spacetab-io/body-validator-php)

## Installation

```bash
composer require spacetab-io/body-validator
```

## Usage

```php
use Amp\Loop;
use HarmonyIO\Validation\Rule\Combinator\All;
use HarmonyIO\Validation\Rule\Email\RfcEmailAddress;
use HarmonyIO\Validation\Rule\Network\Url\Url;
use HarmonyIO\Validation\Rule\Text\AlphaNumeric;
use HarmonyIO\Validation\Rule\Text\LengthRange;
use Spacetab\BodyValidator\BodyValidator;
use Spacetab\BodyValidator\BodyValidatorInterface;

Loop::run(static function () {
    $body = [
        'username' => '__roquie',
        'password' => '1',
        'contacts' => [
            'email' => 'mail@@example.com',
            'github' => 'https:/github.com/roquie',
        ],
    ];

    $validator = new BodyValidator($body, new class implements BodyValidatorInterface {
        public function validate(): iterable {
            yield 'username' => new All(new LengthRange(3, 15), new AlphaNumeric());
            yield 'password' => new LengthRange(12, 255);
            yield 'contacts.email' => new RfcEmailAddress();
            yield 'contacts.github' => new Url();
        }
    });

    /** @var \Spacetab\BodyValidator\ResultSet $result */
    $result = yield $validator->verify();

    $result->isValid();
    $result->getErrors();
});
```

## Depends

* \>= PHP 7.4
* Composer for install package

## License

The MIT License

Copyright © 2020 spacetab.io, Inc. https://spacetab.io

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

