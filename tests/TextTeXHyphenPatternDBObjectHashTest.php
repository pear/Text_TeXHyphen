<?php
/**
 * Testcase for Text_TeXHyphen_ObjectHash
 */

require_once 'Text/TeXHyphen/PatternDB/ObjectHash.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TextTeXHyphenPatternDBObjectHashTest extends PHPUnit_Framework_TestCase
{
    var $patternDB;


    // called before the test functions will be executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function setUp()
    {
        $this->patternDB = new Text_TeXHyphen_PatternDB_ObjectHash;
    }

    // called after the test functions are executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function tearDown()
    {
        // delete your instance
        unset($this->patternDB);
    }

    function testFactory()
    {
        $testArr = array(
            array('type' => 'foo',
                  'options' => array(),
                  'result' => false,
                  'msg' => 'Invalid type was set!'),

            array('type' => 'objecthash',
                  'options' => array(),
                  'result' => false,
                  'msg'=> 'No creation mode was set!'),

            array('type' => 'objecthash',
                  'options' => array('mode' => 'foo'),
                  'result' => false,
                  'msg'=> 'Invalid creation mode was set!'),

            array('type' => 'objecthash',
                  'options' => array('mode' => 'build'),
                  'result' => false,
                  'msg'=> 'Invalid creation data was set!'),

            array('type' => 'objecthash',
                  'options' => array('mode' => 'build',
                                     'data' => array('.ve5ra', '.wil5i',
                                                     '.ye4', '4ab.', 'a5bal',
                                                     'a5ban', 'abe2',
                                                     'ab5erd', 'abi5a',
                                                     'ab5it5ab', 'ab5lat')),
                  'result' => true,
                  'msg'=> ''),
        );

        

        foreach ($testArr as $test) {
            $err = array();

            $msg = sprintf('Type: %s, options: %s', $test['type'], serialize($test['options']));
            $oh = Text_TeXHyphen_PatternDB_ObjectHash::factory($test['type'], $test['options']);

            if (false === $test['result']) {
                $this->assertFalse($oh, $msg);

                $this->assertEquals($test['msg'], $err['message']);
            } else {
                $this->assertTrue(is_a($oh, 'Text_TeXHyphen_ObjectHash'));

            }
        }
    } // end of function testFactory

    function testInitialize()
    {


        // Test patterStrArr check and pattern creation
        $testArr1 = array(
            array('patternStrArr' => 'objecthash',
                  'onlyKeys' => false,
                  'sort'=> true,
                  'result' => false,
                  'errors' => 1,
                  'msg' => array('Invalid pattern string array')),

            array('patternStrArr' => array(
                      '.ve.5ra', '.wil5i', '.ye4', '4ab.', 'a5bal',
                      'a5ban', 'abe2', 'ab5erd', 'abi5a', 'ab5it5ab',
                      'ab.5lat', 'abe2'),
                  'onlyKeys' => false,
                  'sort'=> true,
                  'result' => true,
                  'errors' => 3,
                  'msg' => array('Duplicate pattern string found!',
                                 'Couldn\'t create Text_TeXHyphen_Pattern object!',
                                 'Couldn\'t create Text_TeXHyphen_Pattern object!'))
            );

        foreach ($testArr1 as $test) {
            $this->setUp();

            $result = $this->patternDB->initialize($test['patternStrArr'], $test['onlyKeys'], $test['sort']);
            $this->assertEquals($test['result'], $result);
/*
            $err = $errorStack->getErrors();
            for ($i = 0; $i < $test['errors']; $i++) {
                $this->assertEquals($test['msg'][$i], $err[$i]['message']);
            }
*/
            $this->tearDown();
        }

        // Test sort option
        $testArr2 = array(
            array('patternStrArr' => array(
                      '.ve5ra', '.wil5i', '.ye4', '4ab.', 'a5bal'),
                  'onlyKeys' => true,
                  'sort'=> true,
                  'result' => array(
                      'abal' => 'a5bal',
                      'ab.' => '4ab.',
                      '.vera' => '.ve5ra',
                      '.wili' => '.wil5i',
                      '.ye' => '.ye4')
                 ),
            array('patternStrArr' => array(
                      '.ve5ra', '.wil5i', '.ye4', '4ab.', 'a5bal'),
                  'onlyKeys' => true,
                  'sort'=> false,
                  'result' => array(
                      '.vera' => '.ve5ra',
                      '.wili' => '.wil5i',
                      '.ye' => '.ye4',
                      'ab.' => '4ab.',
                      'abal' => 'a5bal')
                ),
        );

        foreach ($testArr2 as $test) {
            $this->setUp();

            $this->patternDB->initialize($test['patternStrArr'], $test['onlyKeys'], $test['sort']);
            $result = $this->patternDB->_hash;
            $this->assertEquals($test['result'], $result);
            $this->tearDown();
        }

        // Test only keys option
        $testArr3 = array(
            array('patternStrArr' => array(
                      '.ve5ra', '.wil5i', '.ye4', '4ab.', 'a5bal'),
                  'onlyKeys' => false,
                  'sort'=> true,
                  'result' => array(
                      'abal' => 'a5bal',
                      'ab.' => '4ab.',
                      '.vera' => '.ve5ra',
                      '.wili' => '.wil5i',
                      '.ye' => '.ye4')
                 ),
            array('patternStrArr' => array(
                      '.ve5ra', '.wil5i', '.ye4', '4ab.', 'a5bal'),
                  'onlyKeys' => true,
                  'sort'=> true,
                  'result' => array(
                      '.vera' => '.ve5ra',
                      '.wili' => '.wil5i',
                      '.ye' => '.ye4',
                      'ab.' => '4ab.',
                      'abal' => 'a5bal')
                ),
        );

       foreach ($testArr3 as $test) {
            $this->setUp();

            $this->patternDB->initialize($test['patternStrArr'], $test['onlyKeys'], $test['sort']);
            $result = $this->patternDB->_hash;
            foreach ($result as $key => $pattern) {
                if ($test['onlyKeys']) {
                    $this->assertFalse(is_a($pattern, 'Text_TeXHyphen_Pattern'));
                    $this->assertEquals($test['result'][$key], $pattern);
                } else {
                    $this->assertTrue(is_a($pattern, 'Text_TeXHyphen_Pattern'));
                    $this->assertEquals($key, $pattern->getKey());
                    $this->assertEquals($test['result'][$key], $pattern->getPattern());
                }
            }
            $this->tearDown();
        }
    } // end of function testInitialize

    function testGetPattern()
    {
        $testArr = array(
            array('patternStrArr' => array(
                      '.ve5ra', '.wil5i', '.ye4', '4ab.', 'a5bal'),
                  'onlyKeys' => false,
                  'sort'=> true,
                  'result' => array(
                      'abal' => 'a5bal',
                      'ab.' => '4ab.',
                      '.vera' => '.ve5ra',
                      '.wili' => '.wil5i',
                      '.ye' => '.ye4',
                      '.f.g' => false)
                 ),
            array('patternStrArr' => array(
                      '.ve5ra', '.wil5i', '.ye4', '4ab.', 'a5bal'),
                  'onlyKeys' => true,
                  'sort'=> false,
                  'result' => array(
                      '.vera' => '.ve5ra',
                      '.wili' => '.wil5i',
                      '.ye' => '.ye4',
                      'ab.' => '4ab.',
                      'abal' => 'a5bal',
                      '.f.g' => false,
                      '1234' => false)
                ),
        );

       foreach ($testArr as $test) {
            $this->setUp();
            $this->patternDB->initialize($test['patternStrArr'], $test['onlyKeys'], $test['sort']);
            foreach ($test['result'] as $key => $patternStr) {
                $pattern = $this->patternDB->getPattern($key);
                if (false !== $pattern) {
                    $this->assertTrue(is_a($pattern, 'Text_TeXHyphen_Pattern'));
                    $this->assertEquals($key, $pattern->getKey());
                    $this->assertEquals($patternStr, $pattern->getPattern());
                } else {
                    $this->assertFalse($patternStr);
                }
            }
            $this->tearDown();
        }
    } // end of function testGetPattern

    function testSerialize()
    {

    } // end of function testSerialize

    function testUnserialize()
    {

    } // end of function testSerialize

} // end of class Text_TeXHyphen_ObjectHash_TestCase
?>
