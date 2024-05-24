<?php

declare(strict_types=1);

namespace Danon910\blitzy\Types;

use Carbon\Carbon;
use Faker\Factory;
use ReflectionClass;
use Faker\Generator;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Danon910\blitzy\Contracts\IResult;
use Danon910\blitzy\Contracts\ITestType;
use Danon910\blitzy\Entities\SuccessResult;
use Danon910\blitzy\Components\Method\Method;
use Danon910\blitzy\Components\TestTrait\TestTrait;
use Danon910\blitzy\Components\TestClass\TestClass;
use Danon910\blitzy\Components\ArrayList\ArrayList;
use Danon910\blitzy\Components\TestMethod\TestMethod;
use Danon910\blitzy\Components\RequestJson\RequestJson;
use Danon910\blitzy\Components\VariableValue\VariableValue;

class Smoke extends BaseTestService implements ITestType
{
    protected Generator $faker;

    public function __construct(
        protected readonly string $path,
        protected readonly string $feature,
    )
    {
        $this->faker = Factory::create();
    }

    private function getRouteName($reflection_class, string $method_name): ?\Illuminate\Routing\Route
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $action = $route->getAction();

            if (str_contains($action['controller'] ?? '', sprintf("%s@%s", class_basename($reflection_class->getName()), $method_name))) {
                return $route;
            }
        }

        return null;
    }

    public function build(): IResult
    {
        $blitzie = config('blitzie');

        $cases = $blitzie['types']['smoke']['cases'] ?? [];
        $traits = $blitzie['types']['smoke']['traits'] ?? [];
        $generate_fsc = $blitzie['types']['smoke']['generate_fsc'] ?? false;
        $only_methods = $blitzie['types']['smoke']['only_methods'] ?? [];

        $reflection_class = new ReflectionClass($this->path);

        $class_methods = Collection::make($reflection_class->getMethods())
            ->filter(fn(ReflectionMethod $method) => $method->class === $this->path)
            ->filter(function (ReflectionMethod $method) use ($only_methods) {
                if (filled($only_methods)) {
                    return in_array($method->name, $only_methods);
                }

                return true;
            })
        ;

        $generated_test_paths = [];

        /** @var ReflectionMethod $class_method */
        foreach ($class_methods as $class_method) {
            $namespace = sprintf("Smoke\\%s\\%s", $reflection_class->getName(), Str::studly($class_method->getName()));

            $route = $this->getRouteName($reflection_class, $class_method->getName());

            // Trait
            $trait_methods = [];
            $data_test = 'return [];';
            $expected_json_structure = 'return [];';

            if ($route !== null) {
                $api_docs = storage_path('api-docs/api-docs.json');
                $api_docs_content = file_get_contents($api_docs);
                $api_docs_parsed = json_decode($api_docs_content);

                $request_schema_name = null;
                $response_schema_name = null;

                foreach ($api_docs_parsed->paths as $path) {
                    if ($path->post->operationId === $route->getName()) {
                        $request_schema_name = $path->post->requestBody->content->{"application/json"}->schema->{'$ref'};
                        $request_schema_name = str_replace('#/components/schemas/', '', $request_schema_name);

                        $response_schema_name = $path->post->responses->{201}->content->{"application/json"}->schema->properties->data->{'$ref'};
                        $response_schema_name = str_replace('#/components/schemas/', '', $response_schema_name);
                    }
                }

                // Request
                $request = null;

                foreach ($api_docs_parsed->components->schemas as $name => $schema) {
                    if ($name === $request_schema_name) {
                        $request = $schema;
                    }
                }

                if ($request) {
                    $entry_data = [];

                    foreach ($request->properties as $property_name => $property) {
                        if ($property->type === 'string' && isset($property->format) && $property->format === 'date-time') {
                            if (isset($property->example)) {
                                $entry_data[$property_name] = $property->example;
                                continue;
                            }

                            $entry_data[$property_name] = Carbon::now()->toDateTimeString();
                            continue;
                        }

                        if ($property->type === 'string') {
                            if (isset($property->example)) {
                                $entry_data[$property_name] = $property->example;
                                continue;
                            }

                            if (isset($property->maxLength)) {
                                $entry_data[$property_name] = $this->faker->text($property->maxLength);
                                continue;
                            }

                            $entry_data[$property_name] = 'Sample ' . $property_name;
                        }

                        if ($property->type === 'integer') {
                            $entry_data[$property_name] = $property->example ?? 777;
                        }

                        if ($property->type === 'datetime') {
                            if (isset($property->example)) {
                                $entry_data[$property_name] = $property->example;
                                continue;
                            }

                            $entry_data[$property_name] = Carbon::now()->toDateTimeString();
                        }
                    }

                    $data_test = ArrayList::make($entry_data)->render();
                }

                // Response
                $response = null;

                foreach ($api_docs_parsed->components->schemas as $name => $schema) {
                    if ($name === $response_schema_name) {
                        $response = $schema;
                    }
                }

                if ($response) {
                    $expected_json_structure = [];

                    foreach ($response->properties as $property_name => $property) {
                        $expected_json_structure[$property_name] = null;
                    }

                    $expected_json_structure = ArrayList::make($expected_json_structure)->render();
                }
            }

            $trait_methods[] = Method::make(
                'getEntryData',
                'array',
                $data_test,
            )->render();

            $trait_methods[] = Method::make(
                'getExpectedJsonStructure',
                'array',
                $expected_json_structure,
            )->render();

            $test_trait_content = TestTrait::make($namespace, $reflection_class->getShortName(), $trait_methods)->render();
            $this->saveFile($namespace, $reflection_class->getShortName() . 'Trait', $test_trait_content);

            // Test
            $test_methods = [];

            foreach ($cases as $case) {
                $before_given = $this->mapEnums($case['before_given'] ?? []);
                $given = $this->mapEnums($case['given'] ?? []);
                $when = $this->mapEnums($case['when'] ?? []);
                $then = $this->mapEnums($case['then'] ?? []);

                if ($route) {
                    $request_json = RequestJson::make($route->methods[0], $route->getName());
                } else {
                    $guessed_method = match ($class_method->getName()) {
                        'index' => 'get',
                        'store', 'create' => 'post',
                        'update' => 'put',
                        'destroy' => 'delete',
                        default => $class_method->getName()
                    };

                    $request_json = RequestJson::make($guessed_method, 'TODO');
                }

                if ($request_json->hasData()) {
                    $given[] = VariableValue::make('entry_data', '$this->getEntryData()')->render();
                }

                $when[] = $request_json->render();

                // Generate test method
                $test_method = TestMethod::make(
                    $class_method->getName(),
                    $case['case'],
                    $case['expectation'],
                    $before_given,
                    $given,
                    $when,
                    $then
                );

                if ($generate_fsc) {
                    $test_method->addAnnotations([
                        'feature' => 'TODO',
                        'scenario' => ucfirst($class_method->getName()),
                        'case' => $case['case'],
                        null,
                        'expectation' => $case['expectation'],
                        null,
                    ]);
                }

                $test_method->addAnnotations([
                    'test' => null,
                ]);

                $test_method = $test_method->render();
                $test_methods[] = $test_method;
            }

            $test_class_content = TestClass::make(
                $namespace,
                $reflection_class->getShortName(),
                $traits,
                array_merge(
                    $traits,
                    [
                        "{$reflection_class->getShortName()}Trait",
                    ]
                ),
                $test_methods
            )->render();

            $test_file_path = $this->saveFile($namespace, $reflection_class->getShortName() . 'Test', $test_class_content);
            $generated_test_paths[] = $test_file_path;
        }

        return new SuccessResult('Smoke tests generated successfully!', $generated_test_paths);
    }
}
