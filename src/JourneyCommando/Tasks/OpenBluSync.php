<?php
    /** @noinspection PhpPropertyOnlyWrittenInspection */

    namespace JourneyCommando\Tasks;

    use JourneyCommando\JourneyCommando;
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
        }

        /**
         * Executes the OpenBlu sync method
         *
         */
        public function execute()
        {
            out("[{:name}]: Syncing OpenBlu" . PHP_EOL, array("name" => self::$name));
            $OpenBlu = new OpenBlu();
            $OpenBlu->getRecordManager()->sync("http://www.vpngate.net/api/iphone", true);
        }
    }