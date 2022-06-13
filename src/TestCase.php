<?php

namespace iggyvolz\BinaryData;

use Attribute;
use iggyvolz\BinaryData\Definitions\Definition;
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
            $input = new class($this->input) implements Reader {
                public function __construct(private string $data)
                {
                }

                public function read(int $bytes): string
                {
                    if ($bytes > strlen($this->data)) throw new \OutOfBoundsException();
                    $ret = substr($this->data, 0, $bytes);
                    $this->data = substr($this->data, $bytes);
                    return $ret;
                }

                public function done(): bool
                {
                    return $this->data === "";
                }
            };
            $readValue = $definition->read($input);
            if (
                $this->output !== $readValue &&
                // NaN != NaN, not what we want here
                !(is_nan($this->output) && is_nan($readValue))
            ) {
                return false;
            }
            if (!$input->done()) return false; // Ensure that we read all of the input
            if (!$this->oneWay) {
                $output = new class implements Writer {
                    private string $data = "";

                    public function write(string $bytes): void
                    {
                        $this->data .= $bytes;
                    }

                    public function __toString(): string
                    {
                        return $this->data;
                    }
                };
                $definition->write($output, $this->output);
                $readValue = (string)$output;
                if ($readValue !== $this->input) return false;
            }
            return true;
        } catch(\Throwable) {
            return false;
        }
    }

}