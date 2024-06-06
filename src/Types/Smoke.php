<?php

declare(strict_types=1);

namespace Danon910\blitzy\Types;

use Faker\Factory;
use Faker\Generator;
use ReflectionMethod;
use Illuminate\Support\Str;
use Danon910\blitzy\ClassParser;
use Danon910\blitzy\RouteFinder;
use Danon910\blitzy\BlitzyConfig;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Enums\HttpMethod;
use Danon910\blitzy\Contracts\IResult;
use Danon910\blitzy\Contracts\ITestType;
use Danon910\blitzy\Entities\TestTypeCase;
use Danon910\blitzy\Entities\SuccessResult;
use Danon910\blitzy\Entities\TestTypeConfig;
use Danon910\blitzy\Components\Method\Method;
use Danon910\blitzy\Components\TestTrait\TestTrait;
use Danon910\blitzy\Components\TestClass\TestClass;
use Danon910\blitzy\Components\TestMethod\TestMethod;
use Danon910\blitzy\Components\RequestJson\RequestJson;
use Danon910\blitzy\Components\VariableValue\VariableValue;

class Smoke extends BaseTestService implements ITestType
{
    protected Generator $faker;
    protected TestTypeConfig $smoke_test_config;

    public function __construct(
        protected readonly BlitzyConfig $blitzy_config,
        protected readonly ClassParser $class_parser,
        protected readonly RouteFinder $route_finder,
        protected readonly string $path,
        protected readonly string $feature,
    )
    {
        $this->faker = Factory::create();
        $this->smoke_test_config = $this->blitzy_config->getType(TestType::SMOKE);
    }

    protected function guessHttpMethod(string $method_name): HttpMethod
    {
        $method_name_parts = Str::of($method_name);

        if ($method_name_parts->contains(['store'])) {
            return HttpMethod::POST;
        }

        if ($method_name_parts->contains(['update'])) {
            return HttpMethod::PUT;
        }

        if ($method_name_parts->contains(['destroy'])) {
            return HttpMethod::DELETE;
        }

        return HttpMethod::GET;
    }

    protected function generateTrait(
        string $namespace,
        string $class_name,
    ): string
    {
        $trait_methods = [];
        $data_test = sprintf("return [%s            // TODO%s        ];", PHP_EOL, PHP_EOL);
        $expected_json_structure = sprintf("return [%s            // TODO%s        ];", PHP_EOL, PHP_EOL);

//        if ($route !== null) {
//            $api_docs = storage_path($this->blitzy_config->getDocsPath());
//            $api_docs_content = file_get_contents($api_docs);
//            $api_docs_parsed = json_decode($api_docs_content);
//
//            $request_schema_name = null;
//            $response_schema_name = null;
//
//            foreach ($api_docs_parsed->paths as $path) {
//                if ($path->post->operationId === $route->getName()) {
//                    $request_schema_name = $path->post->requestBody->content->{"application/json"}->schema->{'$ref'};
//                    $request_schema_name = str_replace('#/components/schemas/', '', $request_schema_name);
//
//                    $response_schema_name = $path->post->responses->{201}->content->{"application/json"}->schema->properties->data->{'$ref'};
//                    $response_schema_name = str_replace('#/components/schemas/', '', $response_schema_name);
//                }
//            }
//
//            // Request
//            $request = null;
//
//            foreach ($api_docs_parsed->components->schemas as $name => $schema) {
//                if ($name === $request_schema_name) {
//                    $request = $schema;
//                }
//            }
//
//            if ($request) {
//                $entry_data = [];
//
//                foreach ($request->properties as $property_name => $property) {
//                    if ($property->type === 'string' && isset($property->format) && $property->format === 'date-time') {
//                        if (isset($property->example)) {
//                            $entry_data[$property_name] = $property->example;
//                            continue;
//                        }
//
//                        $entry_data[$property_name] = Carbon::now()->toDateTimeString();
//                        continue;
//                    }
//
//                    if ($property->type === 'string') {
//                        if (isset($property->example)) {
//                            $entry_data[$property_name] = $property->example;
//                            continue;
//                        }
//
//                        if (isset($property->maxLength)) {
//                            $entry_data[$property_name] = $this->faker->text($property->maxLength);
//                            continue;
//                        }
//
//                        $entry_data[$property_name] = 'Sample ' . $property_name;
//                    }
//
//                    if ($property->type === 'integer') {
//                        $entry_data[$property_name] = $property->example ?? 777;
//                    }
//
//                    if ($property->type === 'datetime') {
//                        if (isset($property->example)) {
//                            $entry_data[$property_name] = $property->example;
//                            continue;
//                        }
//
//                        $entry_data[$property_name] = Carbon::now()->toDateTimeString();
//                    }
//                }
//
//                $data_test = ArrayList::make($entry_data)->render();
//            }
//
//            // Response
//            $response = null;
//
//            foreach ($api_docs_parsed->components->schemas as $name => $schema) {
//                if ($name === $response_schema_name) {
//                    $response = $schema;
//                }
//            }
//
//            if ($response) {
//                $expected_json_structure = [];
//
//                foreach ($response->properties as $property_name => $property) {
//                    $expected_json_structure[$property_name] = null;
//                }
//
//                $expected_json_structure = ArrayList::make($expected_json_structure)->render();
//            }
//        }

        $trait_methods[] = Method::make('getEntryData', 'array', $data_test)->render();
        $trait_methods[] = Method::make('getExpectedJsonStructure', 'array', $expected_json_structure)->render();

        return TestTrait::make($namespace, $class_name, $trait_methods)->render();
    }

    protected function generateTest(
        string $namespace,
        array $cases,
        string $parsed_class_path,
        string $parsed_class_name,
        string $method_name,
        array $traits,
    ): string
    {
        $route = $this->route_finder->getRoute($parsed_class_path, $method_name);

        $test_methods = [];

        /** @var TestTypeCase $case */
        foreach ($cases as $case) {
            $before_given = $this->mapEnums($case->getBeforeGiven());
            $given = $this->mapEnums($case->getGiven());
            $when = $this->mapEnums($case->getWhen());
            $then = $this->mapEnums($case->getThen());

            if ($route) {
                $request_json = RequestJson::make($route->methods[0], $route->getName());
            } else {
                $guessed_method = $this->guessHttpMethod($method_name);
                $request_json = RequestJson::make($guessed_method->value, 'TODO');
            }

            if ($request_json->hasData()) {
                $given[] = VariableValue::make('entry_data', '$this->getEntryData()')->render();
            }

            $when[] = $request_json->render();

            // Generate test method
            $test_method = TestMethod::make($method_name, $case->getCase(), $case->getExpectation());
            $test_method->setBeforeGiven($before_given);
            $test_method->setGiven($given);
            $test_method->setWhen($when);
            $test_method->setThen($then);

            if ($this->smoke_test_config->generateFsc()) {
                $test_method->addAnnotations([
                    'expectation' => $case->getExpectation(),
                ]);
                $test_method->addAnnotations([
                    'feature' => $this->feature,
                    'scenario' => ucfirst($method_name),
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
        $cases = $this->smoke_test_config->getCases();
        $traits = $this->smoke_test_config->getTraits();
        $only_methods = $this->smoke_test_config->getOnlyMethods();

        $parsed_class = $this->class_parser->parse($this->path);
        $class_methods = $parsed_class->getMethods($only_methods);

        $generated_test_paths = [];

        /** @var ReflectionMethod $class_method */
        foreach ($class_methods as $class_method) {
            $namespace = sprintf("Smoke\\%s\\%s", $parsed_class->getPath(), Str::studly($class_method->getName()));

            // Trait
            $trait_content = $this->generateTrait($namespace, $parsed_class->getName());
            $trait_file_path = $this->saveFile($namespace, $parsed_class->getName() . 'Trait', $trait_content);
            $generated_test_paths[] = $trait_file_path;

            // Test
            $test_content = $this->generateTest($namespace, $cases, $parsed_class->getPath(), $parsed_class->getName(), $class_method->getName(), $traits);
            $test_file_path = $this->saveFile($namespace, $parsed_class->getName() . 'Test', $test_content);
            $generated_test_paths[] = $test_file_path;
        }

        return new SuccessResult('Smoke tests generated successfully!', $generated_test_paths);
    }
}
