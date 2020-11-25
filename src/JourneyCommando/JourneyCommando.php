<?php


    namespace JourneyCommando;

    use acm\acm;
    use Exception;
    use JourneyCommando\Tasks\DatabaseCleanup;
    use JourneyCommando\Tasks\OpenBluSync;
    use mysqli;
    use function cli\out;

    require_once(__DIR__ . DIRECTORY_SEPARATOR . "AutoConfig.php");

    /**
     * Class JourneyCommando
     * @package JourneyCommando
     */
    class JourneyCommando
    {
        /**
         * @var acm
         */
        private $acm;

        /**
         * @var mixed
         */
        private $DatabaseConfiguration;
        /**
         * @var mixed
         */
        private $Configuration;

        /**
         * @var mysqli
         */
        private mysqli $Database;

        /**
         * @var mysqli
         */
        private mysqli $WorkingDatabase;

        /**
         * @var DatabaseCleanup
         */
        private DatabaseCleanup $DatabaseCleanup;

        /**
         * @var OpenBluSync
         */
        private OpenBluSync $OpenBluSync;

        /**
         * JourneyCommando constructor.
         * @throws Exception
         */
        public function __construct()
        {
            // Configurations
            $this->acm = new acm(__DIR__, 'JourneyCommando');

            $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            $this->Configuration = $this->acm->getConfiguration('Configuration');

            // Main database connection
            $this->Database = new mysqli(
                $this->DatabaseConfiguration['Host'],
                $this->DatabaseConfiguration['Username'],
                $this->DatabaseConfiguration['Password'],
                $this->DatabaseConfiguration['Name'],
                $this->DatabaseConfiguration['Port']
            );

            // Establish a second connection for scanning purposes
            $this->WorkingDatabase = new mysqli(
                $this->DatabaseConfiguration['Host'],
                $this->DatabaseConfiguration['Username'],
                $this->DatabaseConfiguration['Password'],
                $this->DatabaseConfiguration['Name'],
                $this->DatabaseConfiguration['Port']
            );

            $this->DatabaseCleanup = new DatabaseCleanup($this);
            $this->OpenBluSync = new OpenBluSync($this);
        }

        /**
         * Executes Journey Commando
         */
        public function execute()
        {
            if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . "intro.txt"))
            {
                out(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "intro.txt"));
                out(PHP_EOL);
            }

            sleep(5);
            out("JourneyCommando Initialized" . PHP_EOL);

            while(true)
            {
                out("Alert! Commando is returning on duty" . PHP_EOL);
                if($this->Configuration["DatabaseCleanupEnabled"] == true)
                {
                    try
                    {
                        $this->getDatabaseCleanup()->execute();
                    }
                    catch(Exception $e)
                    {
                        out("Failed to execute DatabaseCleanup, " . $e->getMessage() . PHP_EOL);
                    }

                    try
                    {
                        $this->getOpenBluSync()->execute();
                    }
                    catch(Exception $e)
                    {
                        out("Failed to execute OpenBluSync, " . $e->getMessage() . PHP_EOL);
                    }
                }

                out("The commando will take a break for " . $this->Configuration["Interval"] . " seconds" . PHP_EOL);
                sleep($this->Configuration["Interval"]);
            }

        }

        /**
         * @return mysqli
         */
        public function getDatabase(): mysqli
        {
            return $this->Database;
        }

        /**
         * @return mysqli
         */
        public function getWorkingDatabase(): mysqli
        {
            return $this->WorkingDatabase;
        }

        /**
         * @return DatabaseCleanup
         */
        public function getDatabaseCleanup(): DatabaseCleanup
        {
            return $this->DatabaseCleanup;
        }

        /**
         * @return OpenBluSync
         */
        public function getOpenBluSync(): OpenBluSync
        {
            return $this->OpenBluSync;
        }
    }