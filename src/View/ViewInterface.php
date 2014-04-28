<?php namespace Crondex\View;

interface ViewInterface
{
   public function set($name,$value);
   public function render();
}

