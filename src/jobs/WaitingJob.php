<?php namespace LeMaX10\WaitingJob\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LeMaX10\WaitingJob\QueueWaiting;

/**
 * Abstract WaitingJob
 * @package  LeMaX10\WaitingJob\Jobs
 */
abstract class WaitingJob implements ShouldQueue
{
    use InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * @var string
     */
    protected $uuid;
    /**
     * @var array
     */
    protected $data;

    /**
     * CreateNewUserJob constructor.
     * @param string $uuid
     * @param array $data
     */
    public function __construct(string $uuid, array $data)
    {
        $this->uuid = $uuid;
        $this->data = $data;
    }

    /**
     * @param $response
     */
    public function response($response)
    {
        QueueWaiting::setQueueResult($this->uuid, $response);
    }
}