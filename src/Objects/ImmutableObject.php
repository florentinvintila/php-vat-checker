<?php

namespace JairForo\VATChecker\Objects;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JairForo\VATChecker\Exceptions\JsonEncodingException;
use JsonSerializable;
use LogicException;

class ImmutableObject implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    /** @var array */
    private $data;

    protected function __construct(array $data)
    {
        $this->data = $data;
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new LogicException('This object is immutable.');
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('This object is immutable.');
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __isset($name)
    {
        $this->offsetExists($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->toArray(), $options);

        if (json_last_error() !== 0) {
            throw new JsonEncodingException(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toJson();
    }

    public function __toString()
    {
        return $this->jsonSerialize();
    }
}
