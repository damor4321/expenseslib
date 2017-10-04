<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ExpensesLib;

class Entity
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var int
     */
    public $date;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var integer
     */
    public $amount;

    /**
     * @var int
     */
    public $timestamp;
}
