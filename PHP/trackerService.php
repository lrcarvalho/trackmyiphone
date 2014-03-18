<?PHP
    
    include_once __DIR__ . '/../logger/logger.php';
    include_once __DIR__ . '/../sosomi/class.sosumi.php';

    class trackerService {
        private static $instance;

        public $logLocation;
        public $deb;
        public $defaultReloadLocationTime;
        private $username;
        private $password;
        private $inactiveTime;
        private $nextPollTime;
        private $lastStatus;
        private $lastURL;
        private $cookieFile;
        private $location;
        private $ssm;
        private $logPath;

        public function __construct() {}

        public function __destruct() {}

        public function setParams($username, $password, $defaultReloadLocationTime = 30, $debug = false) {
            // Enter your MobileMe username and password
            $this->username = $username;
            $this->password = $password;
            $this->defaultReloadLocationTime = $defaultReloadLocationTime;
            $this->ssm = new Sosumi($this->username, $this->password, $debug);
        }    
            
        public function storeLocation() {
            $serializedMsg = serialize($this->ssm->locate());
            return $this->_doLog($serializedMsg);
        }

        private function _doLog($message = null, $location = null) {
            if (is_null($message)) {
                return false;
            } else {
                try {
                    $this->logPath  = __DIR__ . '/../logs/' . $this->username . ' | ' .date('y-m-d');
                    $lastMessage    = $this->getLastLocationFromLog();
                    /*** Check if current loation is different from last location ***/
                    if (!$this->_compareMessagesLocation($lastMessage,$message)) {
                        /*** a new logger instance ***/
                        $log = logger::getInstance();
                        /*** the file to write to ***/
                        $log->logfile = $this->logPath;
                        /*** write message ***/
                        return $log->write($message);
                    }   
                    return true;
                } catch(Exception $e) {
                    echo $e->getMessage();
                }
            }

        }

        public function getLastLocationFromLog($logPath = null, $returnDataSerialized = true) {
            
            if (is_null($logPath)) {
                try {
                    $logPath = $this->logPath;
                } catch(Exception $e) {
                    echo $e->getMessage();
                }
            }

            if (is_file($logPath)) {

                $line = '';

                $f = fopen($logPath, 'r');
                $cursor = -1;

                fseek($f, $cursor, SEEK_END);
                $char = fgetc($f);

                /**
                 * Trim trailing newline chars of the file
                */
                while ($char === "\n" || $char === "\r") {
                    fseek($f, $cursor--, SEEK_END);
                    $char = fgetc($f);
                }

                /**
                * Read until the start of file or first newline char
                */
                while ($char !== false && $char !== "\n" && $char !== "\r") {
                    /**
                    * Prepend the new char
                    */
                    $line = $char . $line;
                    fseek($f, $cursor--, SEEK_END);
                    $char = fgetc($f);
                }

                return $line;
            }    
        }

        /**
        * Return logger instance or create new instance
        * @return object (PDO)
        * @access public
        */
        public static function getInstance() {
            if (!self::$instance) {
                self::$instance = new trackerService;
            }
            return self::$instance;
        }


        /**
        * Clone is set to private to stop cloning
        */
        private function __clone() {}

        /**
        * Compare location from two messages
        * @return bool (True if same location, false if not).
        **/
        private function _compareMessagesLocation($actualMessage = null, $lastMessage = null) {

            if (is_null($actualMessage) && is_null($lastMessage)) {
                throw new Exception("Actual and last message are null", 1);
            }

            $actual = unserialize($actualMessage);
            $last   = unserialize($lastMessage);

            if (count($actual) && count($last)) {
                return (($actual['latitude'] == $last['latitude']) && ($actual['longitude'] == $last['longitude'])) ? true : false;
            }

            return false;

        } 

        
    }    