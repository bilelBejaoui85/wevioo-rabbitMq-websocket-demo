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

class MessageEmailsConsumer extends MessageConsumer implements ConsumerInterface
{

    /**
     * Listen to the queue and execute treatment
     *
     * @param AMQPMessage $message
     */
    public function execute(AMQPMessage $amqpMessage)
    {
        $amqpMessage = json_decode($amqpMessage->body, true);

        // Create the Transport
        $transport = (new \Swift_SmtpTransport('127.0.0.1', 1025));

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        $lineValues = explode(';', $amqpMessage['line'][0]);

        // Create a message
        $message = (new \Swift_Message('Wonderful Subject'))
            ->setFrom([$lineValues[0] => $lineValues[0]])
            ->setTo(['receiver@domain.org', 'other@domain.org' => 'A name'])
            ->setBody('Nom ' . $lineValues[1] . ' Prénom ' . $lineValues[2]);

        print_r($lineValues[0] . "\n");

        // Send the message
        $mailer->send($message);

        if (!(((int)$amqpMessage['lineNumber']) % 5) || ((int)$amqpMessage['lineNumber'] == (int)$amqpMessage['fileLineCount']) ) {
            $this->container->get('old_sound_rabbit_mq.notification_producer')->publish(
                json_encode(array(
                        'fileName' => $amqpMessage['fileName'],
                        'fileLineCounter' => $amqpMessage['fileLineCount'],
                        'lineNumber' => $amqpMessage['lineNumber'],
                        'action' => 'sendMail'
                    )
                )
            );
        }
    }
}
