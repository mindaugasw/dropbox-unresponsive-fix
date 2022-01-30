<?php

declare(strict_types=1);

class Processor
{
    private static Processor $instance;

    private Configuration $configuration;
    private Logger $logger;
    private LogHelper $logHelper;

    public function __construct()
    {
        $this->configuration = Configuration::getInstance();
        $this->logger = Logger::getInstance();
        $this->logHelper = LogHelper::getInstance();
    }

    public static function getInstance(): Processor
    {
        if (!isset(self::$instance)) {
            self::$instance = new Processor();
        }

        return self::$instance;
    }

    public function start(): void
    {
        $this->logger->log('Starting script');
        $this->logHelper->logConfig();

        $processList = $this->getDropboxProcesses();
        $unresponsive = $this->checkIfUnresponsive($processList);

        if ($unresponsive) {
            // TODO remove
            $this->showMessageBox("Dropbox is unresponsive.\nKilling all related processes");

            $this->logger->log('Dropbox is unresponsive');
            $this->killProcesses($processList);

            $sleepTime = $this->configuration->getSleepTimeBeforeRestart();
            $this->logger->log("Sleeping for {$sleepTime} seconds before restart");
            sleep($sleepTime);

            // TODO remove
            $this->showMessageBox("Restarting Dropbox");

            $this->restartDropbox();
        } else {
            $this->logger->log('Working okay');
        }

        $this->logger->log('Script completed');
    }

    /**
     * @return Process[]
     */
    private function getDropboxProcesses(): array
    {
        $command = $this->configuration->getProcessListCommand();
        $commandOutput = [];
        $resultCode = -1;

        $this->logger->log('Executing command: ' . $command);
        exec($command, $commandOutput, $resultCode);
        $this->logger->log("Command result code: {$resultCode}. Found processes:");
        $this->logHelper->logArray($commandOutput);

        // Remove header before converting to objects
        unset($commandOutput[0]);

        $processList = [];

        foreach ($commandOutput as $processData) {
            $process = new Process($processData);
            $processList[$process->getPid()] = $process;
        }

        return $processList;
    }

    /**
     * @param Process[] $processList
     *
     * @return bool
     */
    private function checkIfUnresponsive(array $processList): bool
    {
        if (!$this->checkIfAtLeastOneProcessOverThreshold($processList)) {
            return false;
        }

        // Take multiple measurements to ensure it's not just a single CPU usage spike
        $targetMeasurementsCount = $this->configuration->getMeasurementCount();
        $sleepInterval = $this->configuration->getMeasurementInterval();
        $this->logger->log("Found at least 1 process with high CPU usage. Starting {$targetMeasurementsCount} measurements cycle");

        for ($i = 1; $i < $targetMeasurementsCount; $i++) {
            $this->logger->log("Iteration ${i}. Sleeping for ${sleepInterval} seconds");
            sleep($sleepInterval);

            $newMeasurements = $this->getDropboxProcesses();
            $processList = $this->mergeProcessLists($processList, $newMeasurements);
        }

        return $this->checkIfAtLeastOneProcessOverThreshold($processList);
    }

    /**
     * @param Process[] $processList
     *
     * @return bool
     */
    private function checkIfAtLeastOneProcessOverThreshold(array $processList): bool
    {
        $maxUsage = $this->configuration->getCpuUsageThreshold();
        $foundOverThreshold = false;

        foreach ($processList as $process) {
            if ($process->getCpuPercentAverage() > $maxUsage) {
                $this->logger->log("Found process above CPU threshold (${maxUsage}):");
                $this->logger->log((string)$process);

                $foundOverThreshold = true;
            }
        }

        return $foundOverThreshold;
    }

    /**
     * Merge list B into list A. If there are process with same PID, measurements
     * will appended
     *
     * @param Process[] $listA
     * @param Process[] $listB

     * @return Process[]
     */
    private function mergeProcessLists(array $listA, array $listB): array
    {
        foreach ($listB as $pid => $process) {
            if (array_key_exists($pid, $listA)) {
                $listA[$pid]->appendProcess($process);
            } else {
                $listA[$pid] = $process;
            }
        }

        return $listA;
    }

    /**
     * @param Process[] $processList
     */
    private function killProcesses(array $processList): void
    {
        foreach ($processList as $process) {
            $success = posix_kill($process->getPid(), SIGKILL);
            $successString = $success ? 'true' : 'false';

            $this->logger->log("Killing process {$process->getPid()}, success: {$successString}");
        }
    }

    private function restartDropbox(): void
    {
        $command = $this->configuration->getDropboxRestartCommand();
        $this->logger->log('Executing command: ' . $command);
        $resultCode = -1;

        exec($command, result_code: $resultCode);
        $this->logger->log('Command result code: ' . $resultCode);
    }

    // TODO remove
    private function showMessageBox(string $body): void
    {
        $title = 'dropbox-unresponsive-fix-php';
        exec("osascript -e 'Tell application \"System Events\" to display dialog \"{$body}\" with title \"{$title}\"'");
    }
}
