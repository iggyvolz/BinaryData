<?php

namespace iggyvolz\BinaryData\Definitions\Number;

use iggyvolz\BinaryData\Definitions\Definition;
use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\Writer;
use InvalidArgumentException;
use ReflectionParameter;
use const false;
use const INF;
use const NAN;
use const true;

/**
 * Unfortunately, PHP's https://www.php.net/pack only gives "machine dependent size and representation" for floats
 * While it is *probably* IEEE-754 and single/double on most platforms, I wanted to be safe and implement IEEE-754 manually
 */
abstract class Ieee754 extends Definition
{
    public function __construct(
        public readonly int $significandBits,
        public readonly int $exponentBits,
        public readonly bool $bigEndian,
    )
    {
    }

    public function read(ReflectionParameter $refl, Reader $input, array $args): float
    {
        if ((1 + $this->significandBits + $this->exponentBits) % 8 !== 0) throw new InvalidArgumentException("Must use a multiple of 8 bits!");
        $bytes = (1 + $this->significandBits + $this->exponentBits) / 8;
        $int = AbstractInteger::readInteger($input, $bytes, $this->bigEndian, true);
        $signBit = 1 & ($int >> ($this->significandBits + $this->exponentBits));
        // echo "Sign bit: " . decbin($signBit) . PHP_EOL;
        $exponent = ((1 << $this->exponentBits) - 1) & ($int >> ($this->significandBits));
        // echo "Exponent: " . decbin($exponent) . PHP_EOL;
        $significand = ((1 << $this->significandBits) - 1) & $int;
        // echo "Significand: " . decbin($significand) . PHP_EOL;
        $decodedSignificand = 0;
        for ($i = 0; $i < $this->significandBits + 1; $i++) {
            if ($significand & (1 << $this->significandBits)) {
                $decodedSignificand += 2 ** (-$i);
            }
            $significand <<= 1;
        }
        // echo "Decoded Significand: $decodedSignificand\n";
        $bias = (2 ** ($this->exponentBits - 1)) - 1;
        if ($exponent === 0) {
            if ($decodedSignificand === 0) {
                $readFloat = 0;
            } else {
                $readFloat = ((-1) ** $signBit) * (2 ** -($bias - 1)) * $decodedSignificand;
            }
        } elseif ($exponent === ((1 << $this->exponentBits) - 1)) {
            if ($decodedSignificand === 0) {
                $readFloat = ((-1) ** $signBit) * INF;
            } else {
                $readFloat = NAN;
            }
        } else {
            $readFloat = ((-1) ** $signBit) * 2 ** ($exponent - $bias) * (1 + $decodedSignificand);
        }
        return $readFloat;
    }

    public function write(ReflectionParameter $refl, Writer $output, mixed $data): void
    {
        if(!is_float($data)) throw new \ValueError();
        if ((1 + $this->significandBits + $this->exponentBits) % 8 !== 0) throw new InvalidArgumentException("Must use a multiple of 8 bits!");
        $bytes = (1 + $this->significandBits + $this->exponentBits) / 8;
        if ($data === 0.0) {

            AbstractInteger::writeInteger($output, 0, $this->bigEndian, $bytes);
            return;
        } elseif ($data === INF) {
            // Set everything to 1 except sign bit and significand
            AbstractInteger::writeInteger($output, (-1 << $this->significandBits) & (~(1 << $this->significandBits + $this->exponentBits)), $this->bigEndian, $bytes);
            return;
        } elseif ($data === -INF) {
            // Set everything to 1 except significand
            AbstractInteger::writeInteger($output, -1 << $this->significandBits, $this->bigEndian, $bytes);
            return;
        } elseif ($data === NAN) {
            // Set everything to 1 except all but last bit of significand
            AbstractInteger::writeInteger($output, ((-1 << $this->significandBits) & (~(1 << $this->significandBits + $this->exponentBits))) | 1, $this->bigEndian, $bytes);
            return;
        } elseif ($data < 0) {
            $negative = true;
            $data = -$data;
        } else {
            $negative = false;
        }
        $exponent = -1;

        // TODO this can be optimized by doing logs rather than a loop
        while ($data >= 1) {
            $data /= 2;
            $exponent++;
        }
        while ($data < 0.5) {
            $data *= 2;
            $exponent--;
        }
        $fractionalPartBinary = 0;
        for ($i = 0; $i < $this->significandBits + 1; $i++) {
            $data *= 2;
            $fractionalPartBinary <<= 1;
            if ($data >= 1) {
                $fractionalPartBinary |= 1;
                $data -= 1;
            }
        }
        // echo "Fractional part: " . decbin($fractionalPartBinary) . PHP_EOL;
        // echo "Exponent:  " . $exponent . PHP_EOL;
        $bias = (2 ** ($this->exponentBits - 1)) - 1;
        $biasedExponent = $exponent + $bias;
        if ($biasedExponent <= 0) {
            // echo "Biased exponent:  " . $biasedExponent . PHP_EOL;
            // Subnormal number
            $fractionalPartBinary >>= ((-$biasedExponent) + 1);
            $finalNumber = (($negative ? 1 : 0) << $this->significandBits + $this->exponentBits) | $fractionalPartBinary & ((1 << $this->significandBits) - 1);
            // echo "Final result: " . str_pad(decbin($finalNumber), 32, '0', STR_PAD_LEFT) . PHP_EOL;
            AbstractInteger::writeInteger($output, $finalNumber, $this->bigEndian, $bytes);
        } else {
            // echo "Biased exponent:  " . decbin($biasedExponent) . PHP_EOL;
            $finalNumber = (($negative ? 1 : 0) << $this->significandBits + $this->exponentBits) | ($biasedExponent << $this->significandBits) | $fractionalPartBinary & ((1 << $this->significandBits) - 1);
            // echo "Final result: " . str_pad(decbin($finalNumber), 32, '0', STR_PAD_LEFT) . PHP_EOL;
            AbstractInteger::writeInteger($output, $finalNumber, $this->bigEndian, $bytes);
        }
    }

}