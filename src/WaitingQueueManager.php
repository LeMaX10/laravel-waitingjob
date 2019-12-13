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
    protected $timeout = 10;

    /**
     * @var int
     */
    protected $ttl = 1;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * WaitingQueueManager constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->timeout = $config['timeout'];
        $this->ttl     = $config['ttl'];
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
        $this->cache[$uuid] = Carbon::now()->addSeconds($this->timeout)->timestamp;

        Queue::push(new $this->job($uuid, $params));
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
                unset($this->cache[$uuid]);
            } else {
                sleep(1);
            }
        } while(isset($this->cache[$uuid]) && Carbon::now()->timestamp <= $this->cache[$uuid]);

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