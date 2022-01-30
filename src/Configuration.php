<?php

declare(strict_types=1);

class Configuration
{
    private static Configuration $instance;

    private array $config;

    public function __construct()
    {
        $this->config = $GLOBALS['config'];
    }

    public static function getInstance(): Configuration
    {
        if (!isset(self::$instance)) {
            self::$instance = new Configuration();
        }

        return self::$instance;
    }

    public function getAllConfig(): array
    {
        return $this->config;
    }

    public function getLogToConsoleEnabled(): bool
    {
        return $this->config['log_console_enable'];
    }

    public function getLogToFileEnabled(): bool
    {
        return $this->config['log_file_enable'];
    }

    public function getLogFileLocation(): string
    {
        return $this->config['log_file_location'];
    }

    public function getLogTimeFormat(): string
    {
        return $this->config['log_time_format'];
    }

    public function getProcessListCommand(): string
    {
        return $this->config['process_list_command'];
    }

    public function getCpuUsageThreshold(): int
    {
        return $this->config['cpu_usage_threshold'];
    }

    public function getMeasurementCount(): int
    {
        return $this->config['measurement_count'];
    }

    public function getMeasurementInterval(): int
    {
        return $this->config['measurement_interval'];
    }

    public function getDropboxRestartCommand(): string
    {
        return $this->config['dropbox_restart_command'];
    }

    public function getSleepTimeBeforeRestart(): int
    {
        return $this->config['sleep_before_restart'];
    }
}
