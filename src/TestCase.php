<?php

namespace iggyvolz\BinaryData;

use Attribute;
use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use iggyvolz\BinaryData\Definitions\Definition;
use Kcs\ClassFinder\Finder\ComposerFinder;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use Throwable;
use function is_nan;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class TestCase
{
    public function __construct(
        public readonly string $input,
        public readonly mixed $output,
        public readonly bool $oneWay = false, // Only test input => output
        public readonly array $constructorArgs = [],
    )
    {
    }

    public function test(Definition $definition): bool
    {
        try {
            $input = new StringReader($this->input);
            $readValue = $definition->read(new ReflectionParameter([self::class, "test"], 0), $input, []);
            if (
                $this->output !== $readValue &&
                // NaN != NaN, not what we want here
                !(is_nan($this->output) && is_nan($readValue))
            ) {
                return false;
            }
            if (!$input->done) return false; // Ensure that we read all of the input
            if (!$this->oneWay) {
                $output = new StringWriter();
                $definition->write(new ReflectionParameter([self::class, "test"], 0), $output, $this->output);
                $readValue = $output->data;
                if ($readValue !== $this->input) return false;
            }
            return true;
        } catch(Throwable $t) {
//            throw $t;
            return false;
        }
    }

    public static function runTests(string|ReflectionClass ...$classes): int
    {
        if(empty($classes)) {
            if(InstalledVersions::satisfies(new VersionParser(), "kcs/class-finder", "^0.6.0")) {
                $classes = new ComposerFinder()->subclassOf(Definition::class)->skipNonInstantiable()
                        |> iterator_to_array(...)
                        |> array_values(...);
            } else {
                $classes = array_filter(get_declared_classes(), fn(string $className): bool => class_exists($className) && is_subclass_of($className, Definition::class) && new ReflectionClass($className)->isInstantiable());
            }
            return self::runTests(...$classes);
        }
        $failures = 0;
        foreach ($classes as $className) {
            $class = is_string($className) ? new ReflectionClass($className) : $className;
            if($class->isAbstract() || !$class->isSubclassOf(Definition::class)) continue;
            $testCases = array_map(fn(ReflectionAttribute $attr): TestCase => $attr->newInstance(), $class->getAttributes(TestCase::class));
            foreach($testCases as $i => $testCase) {
                echo "$class->name ".($i+1)."/" . count($testCases) . ": ";
                if($testCase->test($class->newInstance(...$testCase->constructorArgs))) {
                    echo "PASS" . PHP_EOL;
                } else {
                    $failures++;
                    echo "FAIL" . PHP_EOL;
                }
            }
        }
        return $failures;
    }

}