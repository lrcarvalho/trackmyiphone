<?PHP
    require 'logger.php';
    require 'class.sosumi.php';

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

        public function __construct() {}

        public function setParams($username, $password, $defaultReloadLocationTime = 30, $debug = false) {
            // Enter your MobileMe username and password
            $this->username = $username;
            $this->password = $password;
            $this->defaultReloadLocationTime = $defaultReloadLocationTime;
            $this->ssm = new Sosumi($this->username, $this->password, $debug);
            //$this->location = $this->ssm->locate();
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
                    $lastMessage = $this->getLastLocationFromLog();
                    echo "LastMessage: $lastMessage";
                    echo " Message: $message";
                    if ($lastMessage !== $message) {
                        /*** a new logger instance ***/
                        $log = logger::getInstance();
                        /*** the file to write to ***/
                        $log->logfile = '/home/lrcarvalho/Projects/Code/FIndIphone/logs/' . $this->username;
                        /*** write message ***/
                        return $log->write($message);
                    }    
                    return true;
                } catch(Exception $e) {
                    echo $e->getMessage();
                }
            }

        }

        public function getLastLocationFromLog($returnDataSerialized = true) {
            $log = logger::getInstance();
            $lofFile = $log->getlogfile();

            $line = '';

            $f = fopen($logfile, 'r');
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

        
    }    