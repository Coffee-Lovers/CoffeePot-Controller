<?php
/**
 * Created by PhpStorm.
 * User: nikolatrbojevic
 * Date: 27/09/2016
 * Time: 08:24
 */

namespace CL;

use CLLibs\Messaging\CoffeePotProgressMessage;
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

    /**
     * Process method.
     */
    public function process()
    {
        $this->logger->info("Booting up processing");
        $that = $this;
        $this->queue->consume('task_queue', function(string $serialized) use ($that) {
            $task = unserialize($serialized);
            $that->logger->info("New task received", ["task" => $task]);
            $this->messagingHub->publish(new CoffeePotProgressMessage($task->getId(), CoffeePotProgressMessage::STAGE_BOILING_WATTER));
            sleep(5);
            $this->messagingHub->publish(new CoffeePotProgressMessage($task->getId(), CoffeePotProgressMessage::STAGE_BREWING_COFFEE));
            sleep(5);
            $this->messagingHub->publish(new CoffeePotProgressMessage($task->getId(), CoffeePotProgressMessage::STAGE_ADDING_ADDITIONS));
            sleep(5);
            $this->messagingHub->publish(new CoffeePotProgressMessage($task->getId(), CoffeePotProgressMessage::STAGE_FINISHED));
            $that->logger->info("Processing task done");
        });
    }

}