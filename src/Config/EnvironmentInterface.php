<?php namespace Crondex\Config;

interface EnvironmentInterface
{
    public function reporting($displayErrors, $errorLogPath);
    public function setHeaders($pageCachingState);
}
