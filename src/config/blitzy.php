<?php

use Danon910\blitzy\Enums\TestMarkAs;
use Danon910\blitzy\Enums\TestHelper;
use Danon910\blitzy\Enums\TestAssertion;
use Illuminate\Foundation\Testing\DatabaseTransactions;

return [
    'docs_path' => 'api-docs/api-docs.json',

    'types' => [
        'smoke' => [
            'traits' => [
                DatabaseTransactions::class,
            ],
            'generate_fsc' => true,
            'only_methods' => ['index'], // Leave it empty if you want any method
            'cases' => [
                [
                    'case' => 'Happy path',
                    'expectation' => 'Return valid json structure',
                    'before_given' => [
                        TestMarkAs::SKIPPED->message('Test generated automatically!'),
                        TestHelper::TODO->message('Check this test!'),
                    ],
                    'given' => [
                        //
                    ],
                    'when' => [
                        //
                    ],
                    'then' => [
                        TestAssertion::RESPONSE_OK,
                        TestAssertion::RESPONSE_JSON_STRUCTURE,
                    ],
                ],
                [
                    'case' => 'Provide invalid data',
                    'expectation' => 'Return form validation error',
                    'before_given' => [
                        TestMarkAs::SKIPPED->message('Test generated automatically!'),
                        TestHelper::TODO->message('Check this test!'),
                    ],
                    'given' => [
                        //
                    ],
                    'when' => [
                        //
                    ],
                    'then' => [
                        TestAssertion::RESPONSE_UNPROCESSABLE,
                    ],
                ],
                [
                    'case' => 'User is logged out',
                    'expectation' => 'Return unauthorized error',
                    'before_given' => [
                        TestMarkAs::SKIPPED->message('Test generated automatically!'),
                        TestHelper::TODO->message('Check this test!'),
                    ],
                    'given' => [
                        //
                    ],
                    'when' => [
                        //
                    ],
                    'then' => [
                        TestAssertion::RESPONSE_UNAUTHORIZED,
                    ],
                ],
            ],
        ],
    ],
];
