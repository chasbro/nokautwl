<?php
namespace NokautWL\Template;

class HelperTest extends PHPUnit_Framework_TestCase
{
    public function testShortTitle()
    {
        $this->assertEquals("asdasdasdd", Helper::shortTitle('asdasdasdd', 10));
        $this->assertEquals("asdasdasdd...", Helper::shortTitle('asdasdasddd', 10));
        $this->assertEquals("asdasdasdd...", Helper::shortTitle('asdasdasdd12345', 10));
        $this->assertEquals("", Helper::shortTitle('', 10));
        $this->assertEquals("a", Helper::shortTitle('a', 10));
        $this->assertEquals("a", Helper::shortTitle('a', 1));
        $this->assertEquals("...", Helper::shortTitle('a', 0));
    }

    public function testPrice()
    {
        $this->assertEquals("12,01", Helper::price(12.01));
        $this->assertEquals("12,10", Helper::price(12.1));
        $this->assertEquals("12,10", Helper::price(12.10));
        $this->assertEquals("12", Helper::price(12));
        $this->assertEquals("12", Helper::price(12.));
    }
}
