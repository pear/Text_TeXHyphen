<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Text_TeXHyphen_AllTests::main');
}

require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'TextTeXHyphenPatternDBObjectHashTest.php';
require_once 'TextTeXHyphenPatternTest.php';

class Text_TeXHyphen_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PEAR - Text_TeXHyphen');

        $suite->addTestSuite('TextTeXHyphenPatternDBObjectHashTest');
        $suite->addTestSuite('TextTeXHyphenPatternTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Text_TeXHyphen_AllTests::main') {
    Text_TeXHyphen_AllTests::main();
}
