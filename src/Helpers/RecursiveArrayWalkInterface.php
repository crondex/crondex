<?php namespace Crondex\Helpers;

interface RecursiveArrayWalkInterface
{
    public function setValue($value, $function);
    public function getValue();
}

