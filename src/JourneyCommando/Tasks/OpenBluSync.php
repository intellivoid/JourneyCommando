<?php


    namespace JourneyCommando\Tasks;

    use JourneyCommando\JourneyCommando;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidIPAddressException;
    use OpenBlu\Exceptions\InvalidSearchMethodException;
    use OpenBlu\Exceptions\SyncException;
    use OpenBlu\Exceptions\UpdateRecordNotFoundException;
    use OpenBlu\Exceptions\VPNNotFoundException;
    use OpenBlu\OpenBlu;
    use function cli\out;

    /**
     * Class OpenBluSync
     * @package JourneyCommando\Tasks
     */
    class OpenBluSync
    {
        /**
         * @var JourneyCommando
         */
        private JourneyCommando $journeyCommando;

        /**
         * @var OpenBlu
         */
        private OpenBlu $openBlu;

        /**
         * @var string
         */
        private static string $name = "OPENBLU SYNC";

        /**
         * CacheUpdate constructor.
         * @param JourneyCommando $journeyCommando
         */
        public function __construct(JourneyCommando $journeyCommando)
        {
            $this->journeyCommando = $journeyCommando;
            $this->openBlu = new OpenBlu();
        }

        /**
         * Executes the OpenBlu sync method
         *
         * @throws DatabaseException
         * @throws InvalidIPAddressException
         * @throws InvalidSearchMethodException
         * @throws SyncException
         * @throws UpdateRecordNotFoundException
         * @throws VPNNotFoundException
         */
        public function execute()
        {
            out("[{:name}]: Syncing OpenBlu" . PHP_EOL, array("name" => self::$name));
            $OpenBlu = new OpenBlu();
            $OpenBlu->getRecordManager()->sync("http://www.vpngate.net/api/iphone", true);
        }
    }