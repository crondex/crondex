<?php namespace Crondex\Config;

use Exception;

class Config implements ConfigInterface
{
    protected $configFilePath;
    protected $config = array();

    public function __construct($configFilePath)
    {
        $this->configFilePath = $configFilePath;
        $this->set();
    }

    protected function set()
    {
        try {
            if (file_exists($this->configFilePath)){
                $this->config = parse_ini_file($this->configFilePath, true); //'true' processes sections
            } else {
                throw new Exception('Configuration file not found');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get($configKey)
    {
       try {
            if ($this->config !== NULL) {
                if (array_key_exists($configKey, $this->config)) {
                    return $this->config[$configKey];
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
