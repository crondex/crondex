<?php namespace Crondex\Log;

interface MsgInterface
{
    public function fail($pubMsg, $pvtMsg = '');
    public function success($pubMsg);
    public function getMessage();
}
