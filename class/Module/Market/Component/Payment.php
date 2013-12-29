<?php
/**
 * Payment calculation
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Market\Component;

abstract class Payment
{
    public abstract function getSum();
}
