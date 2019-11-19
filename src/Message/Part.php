<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Mail\Message;

/**
 * Message part object class
 *
 * @category   Pop
 * @package    Pop\Mail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */
class Part implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Part data array
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     *
     * Instantiate a part object
     *
     * @param  array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Method to get the count of items in the model
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Method to iterate over the data
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Return all model data as an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Magic get method to return the value of data[$name].
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return (array_key_exists($name, $this->data)) ? $this->data[$name] : null;
    }

    /**
     * Magic set method to set the property to the value of data[$name].
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Return the isset value of data[$name].
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Unset data[$name].
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * Parse message parts from string
     *
     * @param  mixed $body
     * @param  string $boundary
     * @return array
     */
    public static function parse($body, $boundary = null)
    {
        $partStrings = \Pop\Mime\Message::parseBody($body, $boundary);
        $parts       = [];

        foreach ($partStrings as $partString) {
            $parts[] = \Pop\Mime\Message::parsePart($partString);
        }

        return self::parseParts($parts);
    }

    /**
     * Parse message parts from array of parts
     *
     * @param  array $parts
     * @return array
     */
    public static function parseParts(array $parts)
    {
        $flattenedParts = [];

        foreach ($parts as $part) {
            if (is_array($part)) {
                $flattenedParts = array_merge($flattenedParts, self::parseParts($part));
            } else {
                $flattenedParts[] = new static([
                    'headers'    => $part->getHeadersAsArray(),
                    'type'       => ($part->hasHeader('Content-Type')) ? $part->getHeader('Content-Type')->getValue() : null,
                    'attachment' => (($part->hasBody()) && ($part->getBody()->isFile())),
                    'basename'   => $part->getFilename(),
                    'content'    => $part->getContents()
                ]);
            }
        }

        return $flattenedParts;
    }

}
