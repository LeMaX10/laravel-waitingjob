<?php namespace LeMaX10\WaitingJob;

use Illuminate\Support\Facades\Facade;

/**
 * Class QueueWaiting
 * @package App\Facades
 * @see WaitingQueueManager
 */
class QueueWaiting extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return WaitingQueueManager::class;
    }
}