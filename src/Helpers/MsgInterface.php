<?php namespace Crondex\Helpers;

interface MsgInterface
{
    public function fail($pubMsg, $pvtMsg = '');
    public function success($pubMsg);
    public function getMessage();
}
