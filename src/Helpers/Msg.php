<?php namespace Crondex\Helpers;

class Msg implements MsgInterface
{
    public $pubMsg;
    public $pvtMsg;
    public $successMessage;
    public $errorMessage;

    public function __construct()
    {
        $this->debug = true;
    }
        
    public function fail($pubMsg, $pvtMsg = '')
    {
        $this->message = $pubMsg;

        if ($this->debug && $pvtMsg !== '') {
            $this->message .= ": $pvtMsg";
        }
        $this->errorMessage = 'An error occured' . $this->message . '<br />';
    }

    public function success($pubMsg)
    {
        $this->successMessage = $pubMsg;
    }

    public function getMessage()
    {
        if (isset($this->successMessage)) {
            return $this->successMessage;
        } elseif (isset($this->errorMessage)) {
            return $this->errorMessage;
        }
	return false;
    }
}
