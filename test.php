<?php

use iggyvolz\BinaryData\Definitions\Definition;
use iggyvolz\BinaryData\TestCase;

require_once __DIR__ . "/vendor/autoload.php";

// Force inclusion of all PHP files
foreach(new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . "/src")), '/^.+\.php$/', RegexIterator::GET_MATCH) as $f => $_) {
    require_once $f;
}
$success = 0;
$fail = 0;
foreach (get_declared_classes() as $className) {
    $class = new ReflectionClass($className);
    if($class->isAbstract() || !$class->isSubclassOf(Definition::class)) continue;
    $testCases = array_map(fn(ReflectionAttribute $attr): TestCase => $attr->newInstance(), $class->getAttributes(TestCase::class));
    foreach($testCases as $i => $testCase) {
        echo "$className ".($i+1)."/" . count($testCases) . ": ";
        if($testCase->test($class->newInstance(...$testCase->constructorArgs))) {
            echo "PASS" . PHP_EOL;
            $success++;
        } else {
            echo "FAIL" . PHP_EOL;
            $fail++;
        }
    }
}
echo "Overall: $success/" . ($success + $fail) . PHP_EOL;
exit(($fail>0) ? 1 : 0);