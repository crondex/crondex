<?php namespace Crondex\Config;

use Exception;

class Config
{
    protected $_configFilePath;
    protected $_config = array();

    public function __construct($configFilePath)
    {
        $this->_configFilePath = $configFilePath;
        $this->set();
    }

    protected function set()
    {
        try {
            if (file_exists($this->_configFilePath)){
                $this->_config = parse_ini_file($this->_configFilePath, true); //'true' processes sections
            } else {
                throw new Exception('Configuration file not found');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get($configKey) {
       try {
            if ($this->_config !== NULL) {
                if (array_key_exists($configKey, $this->_config)) {
                    return $this->_config[$configKey];
                } else {
                    throw new Exception('Config Variable ' . $configKey . ' does not exist');
                }
            } else {
                throw new Exception('Configuration file was not loaded.');
            } 
        } catch (Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n"; //log this, don't echo it out
        }
        return false;
    }
}
