<?php


    namespace JourneyCommando\Tasks;

    use JourneyCommando\Exceptions\DatabaseException;
    use JourneyCommando\JourneyCommando;
    use msqg\QueryBuilder;
    use function cli\out;

    /**
     * This class scans purges bin logs that are older than 3 days.
     *
     * Class PurgeBinLogs
     * @package JourneyCommando\Tasks
     */
    class PurgeBinLogs
    {
        /**
         * @var JourneyCommando
         */
        private JourneyCommando $journeyCommando;

        /**
         * @var string
         */
        private static string $name = "DATABASE PURGE BIN LOGS";

        /**
         * PurgeBinLogs constructor.
         * @param JourneyCommando $journeyCommando
         */
        public function __construct(JourneyCommando $journeyCommando)
        {
            $this->journeyCommando = $journeyCommando;
        }

        /**
         * Executes the purge bin logs update
         */
        public function execute()
        {
            // CoffeeHouse cleanup
            out("[{:name}]: Clearing BinLogs that are older than 3 days" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("PURGE BINARY LOGS BEFORE DATE(NOW() - INTERVAL 3 DAY) + INTERVAL 0 SECOND;");
            out("[{:name}]: Done!" . PHP_EOL, array("name" => self::$name));
        }
    }