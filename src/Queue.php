<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Mail;

/**
 * Mail queue class
 *
 * @category   Pop
 * @package    Pop_Mail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
class Queue extends \SplQueue
{

    /**
     * Constructor
     *
     * Instantiate the mail queue object
     *
     * @param  mixed  $email
     * @param  string $name
     * @return Queue
     */
    public function __construct($email = null, $name = null)
    {
        if (null !== $email) {
            if (is_array($email)) {
                $this->addRecipients($email);
            } else {
                $this->add($email, $name);
            }
        }
    }

    /**
     * Add a recipient
     *
     * @param  string $email
     * @param  string $name
     * @return Queue
     */
    public function add($email, $name = null)
    {
        $rcpt = [];
        if (null !== $name) {
            $rcpt['name'] = $name;
        }
        $rcpt['email'] = $email;

        return $this->addRecipients([$rcpt]);
    }

    /**
     * Add recipients
     *
     * @param  mixed $rcpts
     * @throws Exception
     * @return Queue
     */
    public function addRecipients($rcpts)
    {
        // If single, but not valid
        if (!is_array($rcpts) && !$this->isValid($rcpts)) {
            throw new Exception("Error: You must pass at least one valid email address.");
        // Else, if single and valid
        } else if (!is_array($rcpts)) {
            $this[] = ['email' => $rcpts];
        // Else if an associative array of scalar values
        } else if (is_array($rcpts) && isset($rcpts['email'])) {
            if (!$this->isValid($rcpts['email'])) {
                throw new Exception("Error: The email address '" . $rcpts['email'] . "' is not valid.");
            }
            $this[] = $rcpts;
        // Else if a numeric array of scalar values
        } else if (is_array($rcpts) && isset($rcpts[0]) && !is_array($rcpts[0])) {
            foreach ($rcpts as $email) {
                if (!$this->isValid($email)) {
                    throw new Exception("Error: The email address '" . $email . "' is not valid.");
                }
                $this[] = ['email' => $email];
            }
        // Else, if an array of arrays
        } else if (is_array($rcpts) && isset($rcpts[0]) && is_array($rcpts[0])) {
            foreach ($rcpts as $rcpt) {
                if (!isset($rcpt['email'])) {
                    throw new Exception("Error: At least one of the array keys must be 'email'.");
                } else if (!$this->isValid($rcpt['email'])) {
                    throw new Exception("Error: The email address '" . $rcpt['email'] . "' is not valid.");
                }
                $this[] = $rcpt;
            }
        } else {
            throw new Exception("Error: The recipients parameter passed was not valid.");
        }

        return $this;
    }

    /**
     * Validate the email
     *
     * @param  string $email
     * @return boolean
     */
    public function isValid($email)
    {
        return (preg_match('/[a-zA-Z0-9\.\-\_+%]+@[a-zA-Z0-9\-\_\.]+\.[a-zA-Z]{2,4}/', $email) ||
            preg_match('/[a-zA-Z0-9\.\-\_+%]+@localhost/', $email));
    }

    /**
     * Build the to string
     *
     * @return string
     */
    public function __toString()
    {
        $to = [];
        foreach ($this as $rcpt) {
            $to[] = (isset($rcpt['name'])) ? $rcpt['name'] . " <" . $rcpt['email'] . ">" : $rcpt['email'];
        }

        return implode(', ', $to);
    }

}
