<?php namespace Crondex\Security;

interface RandomInterface
{
    public function get_random_bytes($count);
}
