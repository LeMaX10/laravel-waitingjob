<?php


namespace LeMaX10\WaitingJob\Traits;


trait WaitingJobReleaseFailed
{
    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $e)
    {
        $this->release();
        $this->response([
            'error' => $e->getMessage()
        ]);
    }
}