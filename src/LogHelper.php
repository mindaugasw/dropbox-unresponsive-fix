<?php

declare(strict_types=1);

class LogHelper
{
    private static LogHelper $instance;

    private Logger $logger;
    private Configuration $configuration;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->configuration = Configuration::getInstance();
    }

    public static function getInstance(): LogHelper
    {
        if (!isset(self::$instance)) {
            self::$instance = new LogHelper();
        }

        return self::$instance;
    }

    /**
     * Log all currently set configuration
     */
    public function logConfig(): void
    {
        $allConfigs = $this->configuration->getAllConfig();
        $configStrings = [];

        foreach ($allConfigs as $key => $value) {
            $configStrings[] = $key . '=' . $value;
        }

        $message = 'Using configs: ' . implode(', ', $configStrings);

        $this->logger->log($message);
    }

    /**
     * Log each line of given array
     *
     * @param string[] $array
     */
    public function logArray(array $array): void
    {
        foreach ($array as $line) {
            $this->logger->log($line);
        }
    }
}
