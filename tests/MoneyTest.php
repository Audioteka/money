<?php

namespace tests;

use Audioteka\Money\Money;

class MoneyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMultiplicationData
     *
     * @param Money $expected
     * @param string $multiplier
     * @param Money $money
     */
    public function testCanBeMultiplied(Money $expected, $multiplier, Money $money)
    {
        $multiplied = $money->multiply($multiplier);

        $this->assertMoneyEqual($expected, $multiplied);
    }

    public function getMultiplicationData()
    {
        return [
            [new Money(3, 'GBP'), '3', new Money(1, 'GBP')],
            [new Money(1499, 'GBP'), '1.5', new Money(999, 'GBP')]
        ];
    }

    /**
     * @dataProvider getDivisionData
     *
     * @param Money $expected
     * @param string $divisor
     * @param Money $money
     */
    public function testCanBeDivided(Money $expected, $divisor, Money $money)
    {
        $divided = $money->divide($divisor);

        $this->assertMoneyEqual($expected, $divided);
    }

    public function getDivisionData()
    {
        return [
            [new Money(1, 'GBP'), '3', new Money(3, 'GBP')],
            [new Money(999, 'GBP'), '1.5', new Money(1499, 'GBP')]
        ];
    }

    /**
     * @dataProvider getAdditionData
     */
    public function testCanBeAdded(Money $expected, Money $addendA, Money $addendB)
    {
        $result = $addendA->add($addendB);

        $this->assertMoneyEqual($expected, $result);
    }

    public function getAdditionData()
    {
        return [
            [new Money(1000, 'PLN'), new Money(500, 'PLN'), new Money(500, 'PLN')],
            [new Money(500, 'GBP'), new Money(1000, 'GBP'), new Money(-500, 'GBP')],
            [new Money(0, 'USD'), new Money(100, 'USD'), new Money(-100, 'USD')]
        ];
    }

    /**
     * @dataProvider getDifferentCurrencyAdditions
     * @expectedException \InvalidArgumentException
     *
     * @param Money $addendA
     * @param Money $addendB
     */
    public function testAdditionRequiresSameCurrencies(Money $addendA, Money $addendB)
    {
        $addendA->add($addendB);
    }

    public function getDifferentCurrencyAdditions()
    {
        return [
            [new Money(0, 'PLN'), new Money(0, 'USD')],
            [new Money(123, 'PHP'), new Money(211, 'GBP')],
        ];
    }

    /**
     * @dataProvider getConversionData
     *
     * @param Money $expected
     * @param Money $input
     * @param $rate
     */
    public function testCanBeConvertedToAnotherCurrency(Money $expected, Money $input, $rate)
    {
        $converted = $input->toCurrency($expected->getCurrency(), $rate);

        $this->assertMoneyEqual($expected, $converted);
    }

    public function getConversionData()
    {
        return [
            [new Money(1, 'USD'), new Money(1, 'PLN'), '1'],
            [new Money(0, 'USD'), new Money(0, 'PLN'), '500000000000'],
            [new Money(2, 'PLN'), new Money(1, 'USD'), '2'],
            [new Money('200000000000000000000000000000000000000', 'USD'), new Money('100000000000000000000000000000000000000', 'CHF'), '2.0'],
            [new Money('1178999966666555544432165498765443214566', 'USD'), new Money('1000000000000000000000000000000000000000', 'CHF'), '1.178999966666555544432165498765443214566']
        ];
    }

    public function assertMoneyEqual(Money $expected, Money $actual)
    {
        $this->assertTrue(
            $expected->equals($actual),
            sprintf(
                'E: %s %s, A: %s %s',
                $expected->getAmount(),
                $expected->getCurrency()->getCode(),
                $actual->getAmount(),
                $actual->getCurrency()->getCode()
            )
        );
    }
}