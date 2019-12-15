<?php

if (!function_exists('waitingJob')) {
    /**
     * @param string $job
     * @param Closure $callback
     * @param array $params
     * @return mixed
     */
    function waitingJob(string $job, array $params = [], ?\Closure $callback = null)
    {
        return \LeMaX10\WaitingJob\QueueWaiting::setJob($job)->waiting($params, $callback);
    }
}