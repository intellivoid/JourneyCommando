<?php


    namespace JourneyCommando\Tasks;


    use JourneyCommando\JourneyCommando;
    use function cli\out;

    class BotDatabaseCleanup
    {

        /**
         * @var string
         */
        private static string $name = "BOT DATABASE CLEANUP";

        /**
         * @var JourneyCommando
         */
        private JourneyCommando $journeyCommando;

        /**
         * CacheUpdate constructor.
         * @param JourneyCommando $journeyCommando
         */
        public function __construct(JourneyCommando $journeyCommando)
        {
            $this->journeyCommando = $journeyCommando;
        }

        /**
         * Executes the process of cleaning up the bot databases
         */
        public function execute()
        {
            out("[{:name}]: Selecting lydiachatbot" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->select_db("lydiachatbot");
            out("[{:name}]: Clearing out Database" . PHP_EOL, array("name" => self::$name));
            $this->clear_database();

            out("[{:name}]: Selecting spamprotectionbot" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->select_db("spamprotectionbot");
            out("[{:name}]: Clearing out Database" . PHP_EOL, array("name" => self::$name));
            $this->clear_database();

            out("[{:name}]: Bot database cleanup complete" . PHP_EOL, array("name" => self::$name));
        }

        /**
         * Clears out the database by truncating tables
         */
        public function clear_database()
        {
            out("[{:name}]: Disabling FOREIGN_KEY_CHECKS" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("SET FOREIGN_KEY_CHECKS=0");

            out("[{:name}]: Truncating conversation" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table conversation;");

            out("[{:name}]: Truncating request_limiter" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table request_limiter;");

            out("[{:name}]: Truncating telegram_update" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table telegram_update;");

            out("[{:name}]: Truncating callback_query" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table callback_query;");

            out("[{:name}]: Truncating chosen_inline_result" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table chosen_inline_result;");

            out("[{:name}]: Truncating edited_message" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table edited_message;");

            out("[{:name}]: Truncating inline_query" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table inline_query;");

            out("[{:name}]: Truncating message" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table message;");

            out("[{:name}]: Truncating poll_answer" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table poll_answer;");

            out("[{:name}]: Truncating poll" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table poll;");

            out("[{:name}]: Truncating pre_checkout_query" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table pre_checkout_query;");

            out("[{:name}]: Truncating shipping_query" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table shipping_query;");

            out("[{:name}]: Truncating user_chat" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table user_chat;");

            out("[{:name}]: Truncating chat" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table chat;");

            out("[{:name}]: Truncating user" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("truncate table user;");

            out("[{:name}]: Enabling FOREIGN_KEY_CHECKS" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->query("SET FOREIGN_KEY_CHECKS=1");

        }
    }