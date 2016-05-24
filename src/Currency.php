<?php

namespace Audioteka\Money;

class Currency
{
    /**
     * @var string
     */
    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function equals(Currency $other)
    {
        return $this->code === $other->code;
    }
}