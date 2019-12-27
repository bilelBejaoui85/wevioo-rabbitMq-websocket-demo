<?php

/**
 * This file defines the parent class engine for websocket command server
 *
 * @category Dialog
 * @package Engine
 * @author Dev Team Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.2.0
 * @since File available since Release 1.2.0
 */

namespace App\Component\RabbitMq;

use App\Component\WebSocket\WebsocketClient;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageConsumer implements ConsumerInterface
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var MyChat $myChat
     */
    protected $websocketClient;

    /**
     * @var MyChat $myChat
     */
    protected $channel;

    /**
     * MessageConsumer constructor.
     *
     * @param ContainerInterface $container
     * @param WebsocketClient $websocketClient
     */
    public function __construct(ContainerInterface $container, WebsocketClient $websocketClient, $channel = 'default')
    {
        $this->container = $container;
        $this->websocketClient = $websocketClient;
        $this->channel = $channel;
    }

    /**
     * Listen to the queue and execute treatment
     *
     * @param AMQPMessage $message
     */
    public function execute(AMQPMessage $message)
    {
        $message = json_decode($message->body, true);
        $message['channel'] = $this->channel;
        $messageToSend = json_encode(
            array(
                "command" => "message",
                "message" => $message
            )
        );

        // Make connexion if is not connected
        if (!$this->websocketClient || !$this->websocketClient->isConnected()) {
            if ($this->websocketClient->connect()) {
                $subscribeMsg = json_encode(array("command" => "subscribe", "channel" => $this->channel));
                $this->websocketClient->sendData($subscribeMsg);
                dump("Connection webSocket has opened ");
            } else {
                dump("An error has occurred when trying opening websocket connexion ");
            }
        }

        // Send message if is connected
        if ($this->websocketClient->isConnected()) {
            $this->websocketClient->sendData($messageToSend);
        }
    }
}
