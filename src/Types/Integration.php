<?php

declare(strict_types=1);

namespace Danon910\blitzy\Types;

use Throwable;
use Faker\Factory;
use Faker\Generator;
use ReflectionMethod;
use Illuminate\Support\Str;
use Danon910\blitzy\ClassParser;
use Danon910\blitzy\BlitzyConfig;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Enums\TestHelper;
use Danon910\blitzy\Contracts\IResult;
use Danon910\blitzy\Contracts\ITestType;
use Danon910\blitzy\Entities\TestTypeCase;
use Danon910\blitzy\Entities\SuccessResult;
use Danon910\blitzy\Entities\TestTypeConfig;
use Danon910\blitzy\Components\Method\Method;
use Danon910\blitzy\Components\TestTrait\TestTrait;
use Danon910\blitzy\Components\TestClass\TestClass;
use Danon910\blitzy\Components\TestMethod\TestMethod;
use Danon910\blitzy\Components\VariableValue\VariableValue;

class Integration extends BaseTestService implements ITestType
{
    protected Generator $faker;
    protected TestTypeConfig $test_config;

    public function __construct(
        BlitzyConfig $blitzy_config,
        protected readonly ClassParser $class_parser,
        protected readonly string $path,
        protected readonly string $feature,
        protected readonly array $methods = [],
        protected readonly bool $force = false,
    )
    {
        parent::__construct($blitzy_config);

        $this->faker = Factory::create();
        $this->test_config = $this->blitzy_config->getType(TestType::INTEGRATION);
    }

    protected function generateTrait(
        string $namespace,
        string $class_path,
        string $class_name,
    ): string
    {
        $trait_methods = [];
        $trait_methods[] = Method::make(
            'getTestedClass',
            $class_name,
            "return app()->make({$class_name}::class, \$properties);",
            parameters: ['array' => 'properties = []']
        )->render();

        $test_trait = TestTrait::make($namespace, $class_name, $trait_methods);
        $test_trait->setImports([$class_path]);

        return $test_trait->render();
    }

    protected function generateTest(
        string $namespace,
        array $cases,
        string $parsed_class_name,
        string $method_name,
        array $traits,
        ReflectionMethod $class_method,
    ): string
    {
        $test_methods = [];

        /** @var TestTypeCase $case */
        foreach ($cases as $case) {
            $before_given = $this->mapEnums($case->getBeforeGiven());
            $given = $this->mapEnums($case->getGiven());
            $when = $this->mapEnums($case->getWhen());
            $then = $this->mapEnums($case->getThen());

            $given[] = VariableValue::make('properties', '[]')->render();

            $method_parameters_name = [];

            foreach ($class_method->getParameters() as $parameter) {
                $value = match($parameter->getType()->getName())
                {
                    'int' => 1,
                    'bool' => 'true',
                    'array' => '[]',
                    default => '"TODO"',
                };

                $method_parameters_name[] = '$' . $parameter->getName();
                $given[] = VariableValue::make($parameter->getName(), $value)->render();
            }

            $method_parameters_merged = implode(', ', $method_parameters_name);

            try {
                if ($class_method->getReturnType()) {
                    if ($class_method->getReturnType()->getName() === 'void') {
                        $when[] = "\$this->getTestedClass(\$properties)->{$method_name}({$method_parameters_merged});";
                    } else {
                        $when[] = VariableValue::make('result', "\$this->getTestedClass(\$properties)->{$method_name}({$method_parameters_merged})")->render();
                    }
                } else {
                    $when[] = TestHelper::TODO->message();
                }
            } catch (Throwable) {
                $when[] = TestHelper::TODO->message();
            }

            // Generate test method
            $test_method = TestMethod::make($method_name, $case->getCase(), $case->getExpectation());
            $test_method->setBeforeGiven($before_given);
            $test_method->setGiven($given);
            $test_method->setWhen($when);
            $test_method->setThen($then);

            if ($this->test_config->generateFsc()) {
                $test_method->addAnnotations([
                    'expectation' => $case->getExpectation(),
                ]);
                $test_method->addAnnotations([
                    'feature' => $this->feature,
                    'scenario' => Str::of($method_name)->camel()->snake(' ')->ucfirst(),
                    'case' => $case->getCase(),
                ]);
            }

            $test_methods[] = $test_method->render();
        }

        $test_class_content = TestClass::make($namespace, $parsed_class_name);
        $test_class_content->setImports($traits);
        $test_class_content->setTraits(array_merge($traits, ["{$parsed_class_name}Trait"]));
        $test_class_content->setMethods($test_methods);

        return $test_class_content->render();
    }

    public function build(): IResult
    {
        $cases = $this->test_config->getCases();
        $traits = $this->test_config->getTraits();
        $only_methods = $this->methods ?? $this->test_config->getOnlyMethods();

        $parsed_class = $this->class_parser->parse($this->path);
        $class_methods = $parsed_class->getMethods($only_methods);

        $generated_test_paths = [];

        /** @var ReflectionMethod $class_method */
        foreach ($class_methods as $class_method) {
            $namespace = sprintf("Integration\\%s\\%s", $parsed_class->getPath(), Str::studly($class_method->getName()));

            // Test
            $test_content = $this->generateTest($namespace, $cases, $parsed_class->getName(), $class_method->getName(), $traits, $class_method);
            $save_test_status = $this->saveFile($namespace, $parsed_class->getName() . 'Test', $test_content, $this->force);

            if ($save_test_status->isSuccess()) {
                $generated_test_paths = array_merge($generated_test_paths, $save_test_status->getPaths());
            } else {
                return $save_test_status;
            }

            // Trait
            $trait_content = $this->generateTrait($namespace, $parsed_class->getPath(), $parsed_class->getName());
            $save_trait_status = $this->saveFile($namespace, $parsed_class->getName() . 'Trait', $trait_content, $this->force);

            if ($save_trait_status->isSuccess()) {
                $generated_test_paths = array_merge($generated_test_paths, $save_trait_status->getPaths());
            } else {
                return $save_trait_status;
            }
        }

        return new SuccessResult('Integration tests generated successfully!', $generated_test_paths);
    }
}
