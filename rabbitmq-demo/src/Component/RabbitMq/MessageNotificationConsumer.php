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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MessageListingConsumer
 * @abstract Class engine rabbit MQ Message notification consume
 * @category Dialog
 * @package Engine
 * @author Dev Team Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.2.0
 * @since Class available since Release 1.2.0
 */
class MessageNotificationConsumer extends MessageConsumer implements ConsumerInterface
{

    /**
     * MessageNotificationConsumer constructor.
     */
    public function __construct(ContainerInterface $container, WebsocketClient $websocketClient)
    {
        parent::__construct($container, $websocketClient, 'notification');
    }
}
