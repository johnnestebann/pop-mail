<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Mail;

/**
 * Message class
 *
 * @category   Pop
 * @package    Pop\Mail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Message
{

    protected $parts = [];

    public function __construct()
    {

    }

    public function addPart(Message\PartInterface $part, $mimeType = 'text/plain')
    {
        $this->parts[] = [
            'part'     => $part,
            'mimeType' => $mimeType
        ];
    }

    public function attach(Message\Attachment $file, $mimeType = 'application/octet-stream')
    {
        $this->addPart($file, $mimeType);
    }

}
