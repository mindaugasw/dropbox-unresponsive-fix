<?php

declare(strict_types=1);

class Logger
{
    private static Logger $instance;

    private Configuration $configuration;
    private SplFileObject $logFile;
    /**
     * Is this first log entry during this script execution?
     */
    private bool $firstLog = true;

    public function __construct()
    {
        $this->configuration = Configuration::getInstance();
    }

    public static function getInstance(): Logger
    {
        if (!isset(self::$instance)) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    public function log(string $message): void
    {
        $timeString = (new DateTime())->format($this->configuration->getLogTimeFormat());

        $message = sprintf(
            "%s: %s\n",
            $timeString,
            $message
        );

        if ($this->firstLog) {
            $message = "\n" . $message;
            $this->firstLog = false;
        }

        $this->writeLogMessage($message);
    }

    private function writeLogMessage(string $message): void
    {
        if ($this->configuration->getLogToConsoleEnabled()) {
            echo $message;
        }

        if ($this->configuration->getLogToFileEnabled()) {
            $this->writeToLogFile($message);
        }
    }

    private function writeToLogFile(string $content): void
    {
        if (!isset($this->logFile)) {
            $this->logFile = new SplFileObject(
                $this->configuration->getLogFileLocation(),
                'a'
            );
        }

        $this->logFile->fwrite($content);
    }
}
