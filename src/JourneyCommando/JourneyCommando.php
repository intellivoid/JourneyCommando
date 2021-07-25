<?php


    namespace JourneyCommando;

    use acm\acm;
    use Exception;
    use JourneyCommando\Tasks\BotDatabaseCleanup;
    use JourneyCommando\Tasks\DatabaseCleanup;
    use JourneyCommando\Tasks\OpenBluSync;
    use JourneyCommando\Tasks\PurgeBinLogs;
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
         * @var bool
         */
        private bool $DatabaseConnected;

        /**
         * @var mysqli
         */
        private mysqli $WorkingDatabase;

        /**
         * @var bool
         */
        private bool $WorkingDatabaseConnected;

        /**
         * @var DatabaseCleanup
         */
        private DatabaseCleanup $DatabaseCleanup;

        /**
         * @var OpenBluSync
         */
        private OpenBluSync $OpenBluSync;

        /**
         * @var BotDatabaseCleanup
         */
        private BotDatabaseCleanup $BotDatabaseCleanup;

        /**
         * @var PurgeBinLogs
         */
        private PurgeBinLogs $PurgeBinLogs;

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
            $this->DatabaseConnected = false;
            $this->WorkingDatabaseConnected = false;
            $this->DatabaseCleanup = new DatabaseCleanup($this);
            $this->OpenBluSync = new OpenBluSync($this);
            $this->BotDatabaseCleanup = new BotDatabaseCleanup($this);
            $this->PurgeBinLogs = new PurgeBinLogs($this);
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

                    try
                    {
                        $this->getBotDatabaseCleanup()->execute();
                    }
                    catch(Exception $e)
                    {
                        out("Failed to execute BotDatabaseCleanup, " . $e->getMessage() . PHP_EOL);
                    }

                    try
                    {
                        $this->getPurgeBinLogs()->execute();
                    }
                    catch(Exception $e)
                    {
                        out("Failed to execute PurgeBinLogs, " . $e->getMessage() . PHP_EOL);
                    }
                }

                out("Disconnecting from working databases to save resources");
                $this->disconnectDatabase();;
                $this->disconnectWorkingDatabase();

                out("The commando will take a break for " . $this->Configuration["Interval"] . " seconds" . PHP_EOL);
                sleep($this->Configuration["Interval"]);
            }

        }

        /**
         * @return mysqli
         */
        public function getDatabase(): mysqli
        {
            if($this->DatabaseConnected == false)
            {
                $this->Database = new mysqli(
                    $this->DatabaseConfiguration['Host'],
                    $this->DatabaseConfiguration['Username'],
                    $this->DatabaseConfiguration['Password'],
                    $this->DatabaseConfiguration['Name'],
                    $this->DatabaseConfiguration['Port']
                );

                $this->DatabaseConnected = true;
            }

            return $this->Database;
        }

        /**
         * Disconnects from the main database
         */
        public function disconnectDatabase()
        {
            if($this->DatabaseConnected == false)
                return;

            $this->Database->close();
            $this->DatabaseConnected = false;
        }

        /**
         * @return mysqli
         */
        public function getWorkingDatabase(): mysqli
        {
            if($this->WorkingDatabaseConnected == false)
            {
                $this->WorkingDatabase = new mysqli(
                    $this->DatabaseConfiguration['Host'],
                    $this->DatabaseConfiguration['Username'],
                    $this->DatabaseConfiguration['Password'],
                    $this->DatabaseConfiguration['Name'],
                    $this->DatabaseConfiguration['Port']
                );

                $this->WorkingDatabaseConnected = true;
            }

            return $this->WorkingDatabase;
        }

        /**
         * Disconnects from the working database
         */
        public function disconnectWorkingDatabase()
        {
            if($this->WorkingDatabaseConnected == false)
                return;

            $this->WorkingDatabase->close();
            $this->WorkingDatabaseConnected = false;
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

        /**
         * @return BotDatabaseCleanup
         */
        public function getBotDatabaseCleanup(): BotDatabaseCleanup
        {
            return $this->BotDatabaseCleanup;
        }

        /**
         * @return PurgeBinLogs
         */
        public function getPurgeBinLogs(): PurgeBinLogs
        {
            return $this->PurgeBinLogs;
        }
    }