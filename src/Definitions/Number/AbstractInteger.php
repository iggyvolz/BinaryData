<?php

namespace iggyvolz\BinaryData\Definitions\Number;

use iggyvolz\BinaryData\Definitions\Definition;
use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\Writer;
use ReflectionParameter;
use TypeError;
use function chr;
use function ord;

abstract class AbstractInteger extends Definition
{
    public function __construct(public readonly int $bytes, public readonly bool $bigEndian, public readonly bool $signed)
    {
    }
    public function read(ReflectionParameter $refl, Reader $input, array $args): int
    {
        return self::readInteger($input, $this->bytes, $this->bigEndian, $this->signed);
    }

    public function write(ReflectionParameter $refl, Writer $output, mixed $data): void
    {
        if(!is_int($data)) {
            throw new TypeError();
        }
        self::writeInteger($output, $data, $this->bigEndian, $this->bytes);
    }

    public static function readInteger(Reader $stream, int $bytes, bool $bigEndian, bool $signed): int
    {
        $result = 0;
        $data = $stream->read($bytes);
        if(!$bigEndian) $data = strrev($data);
        if ($signed && (ord($data[0]) & 0x80)) {
            $result = -1;
        }
        for ($i = 0; $i < $bytes; $i++) {
            $result <<= 8;
            $result |= ord($data[$i]);
        }
        return $result;
    }

    private static function chr(int $char): string {
        $char %= 256;
        if($char < 0) $char += 256;
        return chr($char);
    }

    public static function writeInteger(Writer $stream, int $number, bool $bigEndian, int $bytes): void
    {
        $result = "";
        for ($i = 0; $i < $bytes; $i++) {
            $byte = self::chr($number);
            if ($bigEndian) {
                $result = "$byte$result";
            } else {
                $result .= $byte;
            }
            $number >>= 8;
        }
        $stream->write($result);
    }

}