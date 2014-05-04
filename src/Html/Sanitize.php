<?php namespace Crondex\Html;

class Sanitize implements SanitizeInterface
{
    public function userHtml($html)
    {
        $sanitizedHtml = htmlentities($html,ENT_QUOTES,"UTF-8");
	if (isset($sanitizedHtml)) {
            return $sanitizedHtml;
        } 
    }
}
