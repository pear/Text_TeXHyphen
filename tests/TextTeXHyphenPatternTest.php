<?php
/**
 * Testcase for TEXT_TeXHyphen_Pattern
 */

require_once 'Text/TeXHyphen/Pattern.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TextTeXHyphenPatternTest extends PHPUnit_Framework_TestCase
{
    var $testArr = array ('.ab3a4s' => array('values' => array(0,0,0,3,4,0,0,0), 'keyStr' => '.abas'),
                          '.abi2' => array('values' => array(0,0,0,0,2,0), 'keyStr' => '.abi'),
                          '2d1d' => array('values' => array(2,1,0,0,0), 'keyStr' => 'dd'),
                          '.ber6t5r' => array('values' => array(0,0,0,0,6,5,0,0,0), 'keyStr' => '.bertr'),
                          'dan6ce.' => array('values' => array(0,0,0,6,0,0,0,0), 'keyStr' => 'dance.'),
                          '2t1m8' => array('values' => array(2,1,8,0,0,0), 'keyStr' => 'tm'),
                          '12te123st1' => array('values' => array(12,0,123,0,1,0,0,0,0,0,0), 'keyStr' => 'test'),
                          '123' => array('values' => array(123,0,0,0), 'keyStr' => false),
                          ' ' => array('values' => false, 'keyStr' => false),
                          );

    var $pattern;

    // called before the test functions will be executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function setUp()
    {
        $this->pattern = new TEXT_TeXHyphen_Pattern();
    }

    // called after the test functions are executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function tearDown()
    {
        // delete your instance
        unset($this->pattern);
    }

    function testFactory()
    {
        foreach ($this->testArr as $test => $expectedArr) {
            $msg = sprintf('Tested string: "%s", ', $test);
            $pattern = Text_TeXHyphen_Pattern::factory($test);
            $result = is_object($pattern);
            if (true === $result) {
                $this->assertTrue($result, $msg);
                $this->assertEquals($expectedArr['values'], $pattern->getHyphenValues(), $msg);
                $this->assertEquals($expectedArr['keyStr'], $pattern->getKey(), $msg);
            }
        }
    }

    function testIsValid()
    {
        $testArr = array('adcd ' => true,
                         'a1b4'  => true,
                         '1234'  => false,
                         '..'    => false,
                         '.'     => false,
                         '...'   => false,
                         '.123.'  => false,
                         '.123'  => false,
                         '123.'  => false,
                         '.a1c'  => true,
                         'a b'   => false,
                         '1 3'   => false,
                         '.1.3.'   => false,
                         '. . .'   => false,
                         'a5.cd' => false,
                         '.a5.cd.' => false,
                         '.ab5c' => true,
                         'abd5g' => true);

        foreach ($testArr as $test => $expected) {
            $result = Text_TeXHyphen_Pattern::isValid($test);
            $msg = sprintf('Tested string: "%s", ', $test);
            if (true === $expected) {
                $this->assertTrue($result, $msg);
            } else {
                $this->assertFalse($result, $msg);
            }
        }

    }

    function testSetPattern()
    {
        $testArr = array('adcd ' => true,
                         'a1b4'  => true,
                         '1234'  => false,
                         '..'    => false,
                         '.'     => false,
                         '...'   => false,
                         '.123.'  => false,
                         '.123'  => false,
                         '123.'  => false,
                         '.a1c'  => true,
                         'a b'   => false,
                         '1 3'   => false,
                         '.1.3.'   => false,
                         '. . .'   => false,
                         'a5.cd' => false,
                         '.a5.cd.' => false,
                         '.ab5c' => true,
                         'abd5g' => true);

        foreach ($testArr as $test => $expected) {
            $result = $this->pattern->setPattern($test);
            $msg = sprintf('Tested string: "%s", ', $test);
            if (true === $expected) {
                $this->assertTrue($result, $msg);
            } else {
                $this->assertFalse($result, $msg);
            }
        }
    }

    function testGetPattern()
    {
        $result = $this->pattern->getPattern();
        $this->assertFalse($result);

        $str = 'dan6ce.';
        $this->pattern->setPattern($str);
        $result = $this->pattern->getPattern();
        $this->assertEquals($result, $str);
    }

    function testCreateKey()
    {
        foreach ($this->testArr as $test => $expectedArr) {
            $this->setUp();
            $msg = sprintf('Tested string: "%s", ', $test);
            $key = $this->pattern->createKey($test);
            $this->assertEquals($expectedArr['keyStr'], $key, $msg);
            $this->tearDown();
        }
    }

    function testGetKey()
    {
        $result = $this->pattern->getKey();
        $this->assertFalse($result);

        foreach ($this->testArr as $test => $expectedArr) {
            $this->setUp();
            $msg = sprintf('Tested string: "%s", ', $test);
            $this->pattern->setPattern($test);
            $this->assertEquals($expectedArr['keyStr'], $this->pattern->getKey(), $msg);
            $this->tearDown();
        }
    }

    function testCreateHyphenValues()
    {
        foreach ($this->testArr as $test => $expectedArr) {
            $this->setUp();
            $msg = sprintf('Tested string: "%s", ', $test);
            $values = $this->pattern->createHyphenValues($test);
            $this->assertEquals($expectedArr['values'], $values, $msg);
            $this->tearDown();
        }
    }

    function testGetHyphenValues()
    {
        $result = $this->pattern->getHyphenValues();
        $this->assertFalse($result);

        foreach ($this->testArr as $test => $expectedArr) {
            $this->setUp();
            $msg = sprintf('Tested string: "%s", ', $test);
            if ($this->pattern->setPattern($test)) {
                $this->assertEquals($expectedArr['values'], $this->pattern->getHyphenValues(), $msg);
            } else {
                $this->assertEquals(false, $this->pattern->getHyphenValues(), $msg);
            }
            $this->tearDown();
        }
    }

    function testGetHyphenValue()
    {
        $result = $this->pattern->getHyphenValue(0);
        $this->assertFalse($result);

        foreach ($this->testArr as $test => $expectedArr) {
            $this->setUp();
            $msg = sprintf('Tested string: "%s", ', $test);

            $setResult = $this->pattern->setPattern($test);
            for ($i = 0, $cnt = count($expectedArr['values']); $i < $cnt; $i++) {
                $result = $this->pattern->getHyphenValue($i);
                if ($setResult) {
                    $this->assertEquals($expectedArr['values'][$i], $result, $msg);
                } else {
                    $this->assertEquals(false, $result, $msg);
                }
            }
            $this->tearDown();
        }
    }

} // end of class Text_TeXHyphen_Pattern_TestCase
?>