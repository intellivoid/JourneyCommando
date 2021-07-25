<?php


    namespace JourneyCommando\Tasks;

    use JourneyCommando\Exceptions\DatabaseException;
    use JourneyCommando\JourneyCommando;
    use msqg\QueryBuilder;
    use function cli\out;

    /**
     * This class scans the database for unused data and deletes it, this will improve I/O performance and reduce the
     * amount of disk space. This will make backups less bloated.
     *
     * Class DatabaseCleanup
     * @package JourneyCommando\Tasks
     */
    class DatabaseCleanup
    {
        /**
         * @var JourneyCommando
         */
        private JourneyCommando $journeyCommando;

        /**
         * @var string
         */
        private static string $name = "DATABASE CLEANUP";

        /**
         * CacheUpdate constructor.
         * @param JourneyCommando $journeyCommando
         */
        public function __construct(JourneyCommando $journeyCommando)
        {
            $this->journeyCommando = $journeyCommando;
        }

        /**
         * Executes the cache update
         *
         * @throws DatabaseException
         */
        public function execute()
        {
            // CoffeeHouse cleanup
            out("[{:name}]: Selecting CoffeeHouse" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->select_db("coffeehouse");

            $this->dropRows("language_prediction_cache", $this->scanTable(
                "language_prediction_cache", "id", "last_updated", 172800), "id"
            );
            $this->dropRows("spam_prediction_cache", $this->scanTable(
                "spam_prediction_cache", "id", "last_updated", 172800), "id"
            );
            $this->dropRows("large_generalization", $this->scanTable(
                "large_generalization", "id", "last_updated_timestamp", 1209600), "id"
            );
            $this->dropRows("generalized_classification", $this->scanTable(
                "generalized_classification", "id", "last_updated", 1209600), "id"
            );
            $this->dropRows("cookies", $this->scanCookies("cookies"), "id");

            // Intellivoid Cache Update
            out("[{:name}]: Selecting intellivoid" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->select_db("intellivoid");

            $this->dropRows("authentication_requests", $this->scanTableRelation(
                "authentication_requests", "id", "expires_timestamp"), "id"
            );
            $this->dropRows("authentication_access", $this->scanTableRelation(
                "authentication_access", "id", "expires_timestamp"), "id"
            );
            $this->dropRows("cookies", $this->scanCookies("cookies"), "id");

            // Intellivoid API cleanup
            out("[{:name}]: Selecting intellivoid_api" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->select_db("intellivoid_api");

            $this->dropRows("request_records", $this->scanTable(
                "request_records", "id", "timestamp", 1209600), "id"
            );

            // OpenBlu cleanup
            out("[{:name}]: Selecting openblu" . PHP_EOL, array("name" => self::$name));
            $this->journeyCommando->getWorkingDatabase()->select_db("openblu");

            $this->dropRows("cookies", $this->scanCookies("cookies"), "id");
        }

        /**
         * Scans a table with a SWS structure for expired cookies
         *
         * @param string $table
         * @return array
         * @throws DatabaseException
         * @noinspection DuplicatedCode
         */
        private function scanCookies(string $table): array
        {
            out("[{:name}]: Querying '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table));

            // Prepare the query
            $Query = QueryBuilder::select($table, ["id", "expires"]);
            $QueryResults = $this->journeyCommando->getWorkingDatabase()->query($Query);

            // Execute and process the query
            if($QueryResults == false)
            {
                throw new DatabaseException($this->journeyCommando->getWorkingDatabase()->error, $Query);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }
            }

            $ReturnResults = array();

            if(count($ResultsArray) == 0)
            {
                out("[{:name}]: Skipping, no results found from '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table));
                return array();
            }

            // Scan and sort the results
            out("[{:name}]: Scanning {:count} cookie(s) from '{:table}'" . PHP_EOL, array("name" => self::$name, "count" => count($ResultsArray), "table" => $table));
            foreach($ResultsArray as $item)
            {
                if((int)time() > (int)$item["expires"])
                {
                    $ReturnResults[$item["id"]] = $item["expires"];
                }
            }

            out("[{:name}]: {:count} cookie(s) are expired from {:table}" . PHP_EOL, array("name" => self::$name, "count" => count($ReturnResults), "table" => $table));
            return $ReturnResults;
        }

        /**
         * Scans records by relation in order to determine if a record is expired
         *
         * @param string $table
         * @param string $id_column
         * @param string $expires_column
         * @param bool $addition_property
         * @return array
         * @throws DatabaseException
         */
        private function scanTableRelation(string $table, string $id_column, string $expires_column, bool $addition_property=false): array
        {
            out("[{:name}]: Querying '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table));

            // Prepare the query
            $Query = QueryBuilder::select($table, [$id_column, $expires_column]);
            $QueryResults = $this->journeyCommando->getWorkingDatabase()->query($Query);

            // Execute and process the query
            if($QueryResults == false)
            {
                throw new DatabaseException($this->journeyCommando->getWorkingDatabase()->error, $Query);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }
            }

            $ReturnResults = array();

            if(count($ResultsArray) == 0)
            {
                out("[{:name}]: Skipping, no results found from '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table));
                return array();
            }

            // Scan and sort the results
            out("[{:name}]: Scanning {:count} item(s) from '{:table}'" . PHP_EOL, array("name" => self::$name, "count" => count($ResultsArray), "table" => $table));
            foreach($ResultsArray as $item)
            {
                if($addition_property)
                {
                    if((int)time() > (time() + $item[$expires_column]))
                    {
                        $ReturnResults[$item[$id_column]] = $item[$expires_column];
                    }
                }
                else
                {
                    if((int)time() > $item[$expires_column])
                    {
                        $ReturnResults[$item[$id_column]] = $item[$expires_column];
                    }
                }
            }

            out("[{:name}]: {:count} item(s) are found expired in {:table}" . PHP_EOL, array("name" => self::$name, "count" => count($ReturnResults), "table" => $table));
            return $ReturnResults;
        }

        /**
         * Scans a table normally for records with a last updated timestamp exceeding the max age
         *
         * @param string $table
         * @param string $id_column
         * @param string $target_column
         * @param int $max_age
         * @param bool $addition_property
         * @return array
         * @throws DatabaseException
         * @noinspection DuplicatedCode
         */
        private function scanTable(string $table, string $id_column, string $target_column, int $max_age=172800, bool $addition_property=true): array
        {
            out("[{:name}]: Querying '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table));

            // Prepare the query
            $Query = QueryBuilder::select($table, [$id_column, $target_column]);
            $QueryResults = $this->journeyCommando->getWorkingDatabase()->query($Query);

            // Execute and process the query
            if($QueryResults == false)
            {
                throw new DatabaseException($this->journeyCommando->getWorkingDatabase()->error, $Query);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }
            }

            $ReturnResults = array();

            if(count($ResultsArray) == 0)
            {
                out("[{:name}]: Skipping, no results found from '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table));
                return array();
            }

            // Scan and sort the results
            out("[{:name}]: Scanning {:count} item(s) from '{:table}'" . PHP_EOL, array("name" => self::$name, "count" => count($ResultsArray), "table" => $table));
            foreach($ResultsArray as $item)
            {
                if($addition_property)
                {
                    if(((int)time() - $item[$target_column]) > $max_age)
                    {
                        $ReturnResults[$item[$id_column]] = $item[$target_column];
                    }
                }
                else
                {
                    if(((int)time() - (time() + $item[$target_column])) > $max_age)
                    {
                        $ReturnResults[$item[$id_column]] = $item[$target_column];
                    }
                }
            }

            out("[{:name}]: {:count} item(s) are older than {:max_age} seconds in {:table}" . PHP_EOL, array("name" => self::$name, "count" => count($ReturnResults), "max_age" => $max_age, "table" => $table));
            return $ReturnResults;
        }

        /**
         * Drops rows returned by scan results
         *
         * @param string $table
         * @param array $data
         * @param string $id_column
         * @return bool
         * @throws DatabaseException
         */
        private function dropRows(string $table, array $data, string $id_column)
        {
            if(count($data) == 0)
            {
                return false;
            }

            out("[{:name}]: Dropping {:count} item(s) from '{:table}'" . PHP_EOL, array("name" => self::$name, "table" => $table, "count" => count($data)));

            foreach($data as $key => $value)
            {
                /** @noinspection SqlResolve */
                /** @noinspection SqlNoDataSourceInspection */
                $Query = "DELETE FROM `$table` WHERE $id_column='" . (int)$key . "'";
                $QueryResults = $this->journeyCommando->getWorkingDatabase()->query($Query);

                // Execute and process the query
                if($QueryResults == false)
                {
                    throw new DatabaseException($this->journeyCommando->getWorkingDatabase()->error, $Query);
                }
            }

            return true;
        }
    }