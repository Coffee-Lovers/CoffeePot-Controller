<?php
/**
 * Created by PhpStorm.
 * User: nikolatrbojevic
 * Date: 27/09/2016
 * Time: 08:24
 */

namespace CL;


use CLLibs\Queue\Queue;
use CLLibs\Messaging\Hub;
use Psr\Log\LoggerInterface;

class Controller
{
    /**
     * @var Hub
     */
    private $messagingHub;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Queue
     */
    private $queue;

    /**
     * Controller constructor.
     * @param Queue $queue
     * @param Hub $messagingHub
     * @param LoggerInterface $logger
     */
    public function __construct(Queue $queue, Hub $messagingHub, LoggerInterface $logger)
    {
        $this->messagingHub = $messagingHub;
        $this->logger       = $logger;
        $this->queue        = $queue;
    }

    public function process()
    {
        $this->logger->info("Booting up processing");
        $this->queue->consume('task_queue', [$this, 'consume']);
    }

    /**
     * @param string $serialized
     */
    public function consume(string $serialized)
    {
        $this->logger->info("New task received", ["task" => $serialized]);
        $task = unserialize($serialized);
        sleep(5);
        $this->logger->info("Processing task done");
    }
}