<?php namespace Crondex\Html;

class Clean
{
    public function __construct()
    {
    }

    public function userHtml($html)
    {
        $cleanHtml = htmlentities($html,ENT_QUOTES,"UTF-8");
	if (isset($cleanHtml)) {
            return $cleanHtml;
        } 
    }

    public function __destruct()
    {
    }
}
