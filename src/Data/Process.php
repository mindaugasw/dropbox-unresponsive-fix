<?php

declare(strict_types=1);

class Process
{
    private int $pid;
    private string $state;
    private string $time;
    /** @var float[] Can contain multiple measurements */
    private array $cpuPercent;
    /** @var float[] Can contain multiple measurements */
    private array $memoryPercent;
    private string $command;

    /**
     * @param string $data Output from ps command
     */
    public function __construct(string $data)
    {
        $parts = preg_split('/\s+/', $data, 6);

        $this->pid = intval($parts[0]);
        $this->state = $parts[1];
        $this->time = $parts[2];
        $this->cpuPercent[] = floatval($parts[3]);
        $this->memoryPercent[] = floatval($parts[4]);
        $this->command = $parts[5];
    }

    /**
     * Append values that support multiple measurements from other Process instance
     *
     * @param Process $otherProcess
     */
    public function appendProcess(Process $otherProcess): void
    {
       $this->cpuPercent = array_merge($this->cpuPercent, $otherProcess->cpuPercent);
       $this->memoryPercent = array_merge($this->memoryPercent, $otherProcess->memoryPercent);
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getCpuPercentAverage(): float
    {
        return array_sum($this->cpuPercent) / count($this->cpuPercent);
    }

    public function getMemoryPercentAverage(): float
    {
        return array_sum($this->memoryPercent) / count($this->memoryPercent);
    }

    public function __toString(): string
    {
        return sprintf(
            'PID: %d  STATE: %s  TIME: %s  %%CPU: %.2f (%s)  %%MEM: %.2f (%s)  COMMAND: %s',
            $this->pid,
            $this->state,
            $this->time,
            $this->getCpuPercentAverage(),
            implode(', ', $this->cpuPercent),
            $this->getMemoryPercentAverage(),
            implode(', ', $this->memoryPercent),
            $this->command,
        );
    }
}
