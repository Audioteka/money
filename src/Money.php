<?php

namespace Audioteka\Money;

class Money
{
    const INTERNAL_PRECISION = 4;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    public function __construct($amount, $currency)
    {
        if(is_string($currency)) {
            $currency = new Currency($currency);
        }

        $this->amount = (string)$amount;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return (string)$this->round($this->amount);
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public function equals(Money $other)
    {
        return $this->currency->equals($other->currency)
            && $this->getAmount() == $other->getAmount();

    }

    public function multiply($rate)
    {
        return new Money((bcmul($this->amount, $rate, static::INTERNAL_PRECISION)), $this->currency);
    }

    public function divide($divisor)
    {
        return new Money((bcdiv($this->amount, $divisor, static::INTERNAL_PRECISION)), $this->currency);
    }

    private function floor($num)
    {
        return bcmul($num, 1, 0);
    }

    private function round($num)
    {
        return $this->floor(bcadd($num, 0.5));
    }

    public function add(Money $addend)
    {
        if(!$this->currency->equals($addend->getCurrency())) {
            $message = sprintf(
                'Currencies differ (%s and %s)',
                $this->currency->getCode(),
                $addend->currency->getCode()
            );

            throw new \InvalidArgumentException($message);
        }

        return new Money(bcadd($this->amount, $addend->amount, static::INTERNAL_PRECISION), $addend->getCurrency());
    }

    public function toCurrency($currency, $rate)
    {
        return new Money(bcmul($this->getAmount(), $rate, static::INTERNAL_PRECISION), $currency);
    }
}