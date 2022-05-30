<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\RequestLog;

use Bugloos\FaultToleranceBundle\Contract\Command;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class RequestLog
{
    protected array $executedCommands;

    /**
     * Returns commands executed during the current request
     */
    public function getExecutedCommands(): array
    {
        return $this->executedCommands;
    }

    /**
     * Adds an executed command
     */
    public function addExecutedCommand(Command $command): void
    {
        $this->executedCommands[] = $command;
    }

    /**
     * Formats the log of executed commands into a string usable for logging purposes.
     *
     * Examples:
     *
     * TestCommand[SUCCESS][1ms]
     * TestCommand[SUCCESS][1ms], TestCommand[SUCCESS, RESPONSE_FROM_CACHE][1ms]x4
     * TestCommand[TIMEOUT][1ms]
     * TestCommand[FAILURE][1ms]
     *
     * If a command has a multiplier such as <code>x4</code>,
     * that means this command was executed 4 times with the same events.
     * The time in milliseconds is the sum of the 4 executions.
     *
     * For example, <code>TestCommand[SUCCESS][15ms]x4</code> represents
     * TestCommand being executed 4 times and the sum of those 4 executions was 15ms.
     * These 4 each executed the run() method since
     *
     * <code>RESPONSE_FROM_CACHE</code> was not present as an event.
     */
    public function getExecutedCommandsAsString(): string
    {
        $output = "";
        $executedCommands = $this->getExecutedCommands();
        $aggregatedCommandsExecuted = [];
        $aggregatedCommandExecutionTime = [];

        /** @var Command $executedCommand */
        foreach ($executedCommands as $executedCommand) {
            $outputForExecutedCommand = $this->getOutputForExecutedCommand($executedCommand);

            if (!isset($aggregatedCommandsExecuted[$outputForExecutedCommand])) {
                $aggregatedCommandsExecuted[$outputForExecutedCommand] = 0;
            }

            $aggregatedCommandsExecuted[$outputForExecutedCommand] += 1;

            $executionTime = $executedCommand->getExecutionTimeInMilliseconds();

            if ($executionTime < 0) {
                $executionTime = 0;
            }

            if (isset($aggregatedCommandExecutionTime[$outputForExecutedCommand]) && $executionTime > 0) {
                $aggregatedCommandExecutionTime[$outputForExecutedCommand] += $executionTime;
            } else {
                $aggregatedCommandExecutionTime[$outputForExecutedCommand] = $executionTime;
            }
        }

        foreach ($aggregatedCommandsExecuted as $outputForExecutedCommand => $count) {
            if (!empty($output)) {
                $output .= ", ";
            }

            $output .= "{$outputForExecutedCommand}";

            $output .= "[" . $aggregatedCommandExecutionTime[$outputForExecutedCommand] . "ms]";

            if ($count > 1) {
                $output .= "x{$count}";
            }
        }

        return $output;
    }

    protected function getOutputForExecutedCommand(Command $executedCommand): string
    {
        $display = $executedCommand->getCommandKey() . "[";
        $events = $executedCommand->getExecutionEvents();

        if (! empty($events)) {
            foreach ($events as $event) {
                $display .= "{$event}, ";
            }
            $display = substr($display, 0, -2);
        } else {
            $display .= "Executed";
        }

        $display .= "]";

        return $display;
    }
}
