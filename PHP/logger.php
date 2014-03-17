<?php

/**
 *
 * @Class logger
 *
 * @Purpose: Logs text to a file
 *
 * @Author: Leandro Carvalho
 *
 * @example usage
 * $log = logger::getInstance();
 * $log->logfile = '/tmp/errors.log';
 * $log->write('An error has occured', __FILE__, __LINE__);
 *
 */
class logger
{
    /*** Declare instance ***/
    private static $instance = NULL;

    /**
     * @Constructor is set to private to stop instantion
     */
    private function __construct() {}

    /**
     * @setter
     * @access public
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        switch($name)
        {
            case 'logfile':
            if(!file_exists($value)) {
                try {
                    $fp = fopen($value, "a+");
                    fclose($fp);
                } catch(Exception $e) {
                    echo $e->getMessage();
                }    

                if(!file_exists($value) || !is_writeable($value)) {
                    throw new Exception("$value is not a valid file path");
                }    
            }

            $this->logfile = $value;
            break;

            default:
            throw new Exception("$name cannot be set");
        }
    }

    /**
     * @getter
     * @access public
     * @param string $property
     */
    public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
    }

    /**
     * @write to the logfile
     * @access public
     * @param string $message
     * @param string $file The filename that caused the error
     * @param int $line The line that the error occurred on
     * @return number of bytes written, false other wise
     */
    public function write($message, $isSerialized = true) {
        $message .= "\n";
        return file_put_contents($this->logfile, $message, FILE_APPEND );
    }

    /**
    * Return logger instance or create new instance
    * @return object (PDO)
    * @access public
    */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new logger;
        }
        return self::$instance;
    }


    /**
     * Clone is set to private to stop cloning
     */
    private function __clone() {}
}

?>