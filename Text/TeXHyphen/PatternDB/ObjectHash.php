<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Text :: TeXHyphen                                              |
// +------------------------------------------------------------------------+
// | Copyright (c) 2004 Stefan Ohrmann <bshell@gmx.net>.                    |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//
// $Id$
//

/**
 * Implementation of the Text_TeXHyphen_PatternDB_ObjectHash
 *
 * @package Text_TeXHyphen
 */

require_once 'Text/TeXHyphen/PatternDB.php';
require_once 'Text/TeXHyphen/Pattern.php';

/**
 * A Text_TeXHyphen_Pattern object hash class for the TeX hyphenation
 * algorithm.
 *
 * @author Stefan Ohrmann <bshell@gmx.net>
 * @version $Id$
 * @package Text_TeXHyphen
 *
 */
class Text_TeXHyphen_PatternDB_ObjectHash extends Text_TeXHyphen_PatternDB
{
    /**
     * The hash contains the reference to all pattern objects.
     *
     * The key is the unique identifier of the pattern, which is
     * created from the TeX pattern string.
     *
     * @see Text_TeXHyphen_Pattern
     *
     * @var array Array references to Text_TeXHyphen_Pattern objects
     *
     * @access private
     */
    var $_hash = array();

    /**
     * Factory for creating a Text_TeXHyphen_Pattern object hash as
     * pattern database.
     *
     * The type have to be 'objecthash', otherwise false will
     * returned. For the creation extists two modes (options['mode']):
     * - build, creates a whole new hash from an array of TeX pattern
     *          strings. For the {@link initialize} function the option
     *          'onlyKeys' and 'sort' can be passed.
     * - load, loads a serialized hash from a file specified by the
     *         option 'fileName'. Additional the option 'compression'
     *         for the {@link unserialize()} can be passed.
     *
     * @param string $type Name of the pattern database implementation,
     * must 'objecthash'.
     * @param array $options Options for the pattern retrieving.
     *
     * @return Text_TeXHyphen_PatternDB_ObjectHash|false Reference to a
     * object of type Text_TeXHyphen_PatternDB_ObjectHash, a subclass
     * of Text_TeXHyphen_PatternDB, or false on error.
     *
     * @access public
     */
    public static function factory($type, $options = array())
    {
        $type = strtolower($type);
        if (0 !== strcasecmp($type, 'objecthash')) {
            throw new InvalidArgumentException('Invalid type was set!');
        }

        if (!isset($options['mode'])) {
            throw new InvalidArgumentException('No creation mode was set!');
        }

        $oh = new Text_TeXHyphen_PatternDB_ObjectHash;

        $mode = strtolower($options['mode']);
        switch ($mode) {
        case 'build':
            if (!isset($options['data'])) {
                throw new InvalidArgumentException('Invalid creation data was set!');
            }

            $onlyKeys = (isset($options['onlyKeys']) && is_bool($options['onlyKeys']))
                      ? $options['onlyKeys']
                      : false;

            $sort = (isset($options['sort']) && is_bool($options['sort']))
                  ? $options['sort']
                  : true;

            if (false === $oh->initialize($options['data'], $onlyKeys, $sort)) {

               throw new InvalidArgumentException('Couldn\'t initialize object hash!');

                return false;
            }
            break;

        case 'load':
            if (!isset($options['fileName'])) {
                throw new InvalidArgumentException('No file name was set!');
            }

            $compression = (isset($options['compression']) && is_bool($options['compression']))
                         ? $options['compression']
                         : true;

            $data = @file_get_contents($options['fileName']);

            if (false === $data) {
                throw new InvalidArgumentException('Couldn\'t open file!');
            }

            if (false === $oh->unserialize($data, $compression)) {
                throw new InvalidArgumentException('Couldn\'t load object hash data from file!');
            }
            break;

        default:
           throw new InvalidArgumentException('Invalid creation mode was set!');
        }

        return $oh;
    } // end of function factory

    /**
     * Gets the Text_TeXHyphen_Pattern object specified by the $key,
     * if it exists in the pattern database.
     *
     * @see Text_TeXHyphen_Pattern
     *
     * @param string $key String by which the pattern should be
     * identified in the hash.
     *
     * @return Text_TeXHyphen_Pattern|false Reference to a
     * Text_TeXHyphen_Pattern object if successful or false if the
     * pattern isn't found.
     *
     * @access public
     */
      function getPattern($key)
      {
        if (!isset($this->_hash[$key])) {
            return false;
        }

        $pattern = $this->_hash[$key];

        if (!is_a($pattern, 'Text_TeXHyphen_Pattern')) {
            $newpattern = Text_TeXHyphen_Pattern::factory($pattern);
            if (false === $newpattern) {
                // Logging Notice
                throw new InvalidArgumentException('Couldn\'t create Text_TeXHyphen_Pattern object!');
                return false;
            }

            $this->_hash[$key] = $newpattern;
            $pattern = $newpattern;
        }

        return $pattern;
    } // end of function getPattern

    /**
     * Initializes the Text_TeXHyphen_Pattern object hash.
     *
     * The $patternStrArr contains the TeX pattern strings from which
     * the Text_TeXHyphen_Pattern will created. If $onlyKey is true,
     * only the key of the pattern is created and the TeX pattern
     * string is included in the hash. The Text_TeXHyphen_Pattern will
     * created on demand by getPattern(). If $sort is true the hash
     * will after initilization sorted by ksort().
     *
     * @see Text_TeXHyphen_Pattern::createKey()
     * @see getPattern()
     *
     * @param array $patternStrArr Array of TeX pattern strings.
     * @param boolean $onlyKeys Decides, if only the patterns keys are
     * generated and the Text_TeXHyphen_Pattern on demand.
     * @param boolean $sort Decides, if the hash is sorted at the end
     * of the initialization process.
     *
     * @return boolean true, if successful otherwise false
     *
     * @access public
     */
    function initialize($patternStrArr, $onlyKeys = false, $sort = true)
    {
        if (!is_array($patternStrArr) || empty($patternStrArr)) {
            throw new InvalidArgumentException('Invalid pattern string array');
        }

        foreach ($patternStrArr as $patternStr) {

            if (!is_null($this->_validator)) {
                if (!$this->_validator->isValid($patternStr)) {
                    throw new InvalidArgumentException('Invalid pattern string!');
                }
            }

            if ($onlyKeys) {
                $p = new Text_TeXHyphen_Pattern();
                $key = $p->createKey($patternStr);
            } else {
                $pattern = Text_TeXHyphen_Pattern::factory($patternStr);
                if (false === $pattern) {
                    throw new InvalidArgumentException('Couldn\'t create Text_TeXHyphen_Pattern object!');
                }
                $key = $pattern->getKey();
            }

            if (!isset($this->_hash[$key])) {
                if ($onlyKeys) {
                    $this->_hash[$key] = $patternStr;
                } else {
                    $this->_hash[$key] = $pattern;
                }
            } else {
                throw new InvalidArgumentException('Duplicate pattern string found!');
            }
        }

        if ($sort) {
            ksort($this->_hash);
        }
        return true;
    } // end of function initialize

    /**
     * Serializes the current state of the pattern hash and returns
     * the data.
     *
     * Takes the current pattern hash and passes to serialize(). The
     * returned string, will compressed by gzcompress(), if
     * $compression is true.
     * If the $onlyKeys=true option of the inizialize function is used
     * the missing Text_TeXHyphen_Pattern object will not created
     * before serialization.
     *
     * @see initialize()
     *
     * @param boolean $compression Decides, if the serialized hash is
     * compressed.
     *
     * @return string|false the (compressed) serialized hash data or
     * false on error.
     *
     * @access public
     */
    function serialize($compression = true)
    {
         $data = serialize($this->_hash);

         if ($compression) {
             $data = gzcompress($data);
         }

        if (false === $data) {
            throw new InvalidArgumentException('Couldn\'t compress serialized pattern hash data');
        }

        return $data;
    } // end of function serialize

    /**
     * Unserializes the pattern hash from the data.
     *
     * Takes the $data and uncompresses the serialized pattern hash,
     * if $compression is true. The serialized pattern hash will
     * passed to unserialize() and the pattern hash set to the
     * returned array.
     *
     * @param string $data (compressed) serialzed hash data.
     * @param boolean $compression Decides, if the data is
     * uncompressed before unserialisation of the data.
     *
     * @return boolean true, if successful otherwise false.
     *
     * @access public
     */
    function unserialize($data, $compression = true)
    {
        if ($compression) {
            $data = gzuncompress($data);
        }

        if (false === $data) {
            throw new InvalidArgumentException('Couldn\'t uncompress serialized pattern hash data');
        }

        $this->_hash = unserialize($data);
        if (false === $this->_hash) {
            $this->_hash = array();
            throw new InvalidArgumentException('Couldn\'t unserialize pattern hash data');
        }

        return true;
    } // end of function unserialize
} // end of class Text_TeXHyphen_PatternDB_ObjectHash
