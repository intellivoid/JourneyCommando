<?php


    namespace JourneyCommando\Exceptions;

    use Exception;
    use Throwable;

    /**
     * Class DatabaseException
     * @package JourneyCommando\Exceptions
     */
    class DatabaseException extends Exception
    {
        /**
         * @var string
         */
        private $query;

        /**
         * @var Throwable|null
         */
        private ?Throwable $previous;

        /**
         * DatabaseException constructor.
         * @param string $message
         * @param $query
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $query, $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->message = $message;
            $this->query = $query;
            $this->code = $code;
            $this->previous = $previous;
        }
    }