<?php

namespace iggyvolz\BinaryData;

use Attribute;
use iggyvolz\BinaryData\Definitions\Definition;
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
            $readValue = $definition->read(new ReflectionParameter([self::class, "test"], 0), $input);
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

}