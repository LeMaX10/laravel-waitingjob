<?php namespace LeMaX10\WaitingJob;

use Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

/**
 * Class WaitingQueueManager
 * @package App\Support
 */
class WaitingQueueManager
{
    /**
     * @var string
     */
    protected $job;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var array
     */
    protected $timers = [];

    /**
     * WaitingQueueManager constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setTimeout($config['timeout'])
             ->setTtl($config['ttl'])
             ->setQueue($config['queue']);
    }

    /**
     * @param string $class
     * @return $this|Job
     */
    public function setJob(string $class)
    {
        $this->job = $class;
        return $this;
    }

    /**
     * @param string $queue
     * @return $this
     */
    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param int $ttl
     * @return $this
     */
    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * @return string
     */
    private function getId(): string
    {
        return uniqid('queue-', true);
    }

    /**
     * @param \Closure $callback
     *
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function waiting(\Closure $callback, array $params = [])
    {
        $uuid = $this->getId();
        $this->timers[$uuid] = Carbon::now()->addSeconds($this->timeout)->timestamp;

        Queue::pushOn($this->queue, new $this->job($uuid, $params));
        return $this->waitingResult($uuid, $callback);
    }

    /**
     * @param string $uuid
     * @param \Closure $callback
     * @return mixed
     * @throws \Exception
     */
    protected function waitingResult(string $uuid, \Closure $callback)
    {
        $result  = null;
        do {
            $result = $this->getQueueResult($uuid);

            if($result) {
                unset($this->timers[$uuid]);
            } else {
                usleep(900);
            }
        } while(isset($this->timers[$uuid]) && Carbon::now()->timestamp <= $this->timers[$uuid]);

        if(is_array($result) && isset($result['error'])) {
            throw new \Exception($result['error']);
        }

        return $callback($result);
    }

    /**
     * Получить результат из кеша
     *
     * @param $uuid
     *
     * @return mixed
     */
    public function getQueueResult(string $uuid)
    {
        return Cache::get($uuid);
    }

    /**
     * Положить результат в кеш
     *
     * @param       $uuid
     * @param array $result
     *
     * @return mixed
     */
    public function setQueueResult(string $uuid, $result)
    {
        return Cache::put($uuid, $result, $this->ttl);
    }
}