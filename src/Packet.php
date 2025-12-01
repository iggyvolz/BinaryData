<?php

namespace iggyvolz\BinaryData;

use iggyvolz\BinaryData\Definitions\Definition;
use Iggyvolz\SimpleAttributeReflection\AttributeReflection;
use LogicException;
use ReflectionClass;

abstract class Packet
{
    public static function fromString(string $data): static
    {
        $data = new StringReader($data);
        $constr = ($cls = new ReflectionClass(static::class))->getConstructor();
        $args = [];
        foreach($constr->getParameters() as $parameter) {
            if($definition = AttributeReflection::getAttribute($parameter, Definition::class)) {
                $args[] = $definition->read($parameter, $data, $args);
            } else {
                throw new LogicException("No definition for parameter " . $parameter->getName() . " in " . static::class . "::__construct");
            }
        }
        return $cls->newInstance(...$args);
    }
    public function __toString(): string
    {
        $data = new StringWriter();
        $constr = new ReflectionClass(static::class)->getConstructor();
        foreach($constr->getParameters() as $parameter) {
            if($definition = AttributeReflection::getAttribute($parameter, Definition::class)) {
                $definition->write($parameter, $data, $this->{$parameter->name});
            } else {
                throw new LogicException("No definition for parameter " . $parameter->getName() . " in " . static::class . "::__construct");
            }
        }
        return $data->data;
    }
}