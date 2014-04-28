<?php namespace Crondex\Bootstrap;

class RemoveMagicQuotes implements RemoveMagicQuotesInterface
{
    protected $_recursiveArrayWalk;

    public function __construct(RecursiveArrayWalkInterface $RecursiveArrayWalkObj)
    {
        $this->_recursiveArrayWalk = $RecursiveArrayWalkObj;
    }

    public function removeQuotes($value)
    {
        if (get_magic_quotes_gpc()) {
            $this->_recursiveArrayWalk->setValue($value,'stripslashes');
            return $this->_recursiveArrayWalk->getValue();
        }
        return $value;
    }
}

