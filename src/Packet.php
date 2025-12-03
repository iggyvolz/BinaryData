<?php

namespace iggyvolz\BinaryData;

use iggyvolz\BinaryData\Definitions\Definition;
use Iggyvolz\SimpleAttributeReflection\AttributeReflection;
use LogicException;
use ReflectionClass;

abstract class Packet
{
    public static function read(Reader $reader): static
    {
        $constr = ($cls = new ReflectionClass(static::class))->getConstructor();
        $args = [];
        foreach($constr->getParameters() as $parameter) {
            if($definition = AttributeReflection::getAttribute($parameter, Definition::class)) {
                $args[$parameter->name] = $definition->read($parameter, $reader, $args);
            } else {
                throw new LogicException("No definition for parameter " . $parameter->getName() . " in " . static::class . "::__construct");
            }
        }
        return $cls->newInstance(...$args);
    }
    public static function fromString(string $data): static
    {
        return static::read(new StringReader($data));
    }
    public function write(Writer $writer): void
    {
        $constr = new ReflectionClass(static::class)->getConstructor();
        foreach($constr->getParameters() as $parameter) {
            if($definition = AttributeReflection::getAttribute($parameter, Definition::class)) {
                $definition->write($parameter, $writer, $this->{$parameter->name});
            } else {
                throw new LogicException("No definition for parameter " . $parameter->getName() . " in " . static::class . "::__construct");
            }
        }
    }
    public function __toString(): string
    {
        $data = new StringWriter();
        $this->write($data);
        return $data->data;
    }
    public function __debugInfo(): ?array
    {
        $data = [];
        $class = new ReflectionClass($this);
        foreach($class->getProperties() as $property) {
            $data[$property->name] = $property->getValue($this);
            foreach(AttributeReflection::getAttributes($property, DebugPrinter::class) as $debugPrinter) {
                $data[$property->name] = $debugPrinter->handle($data[$property->name]);
            }
        }
        return $data;
    }
}