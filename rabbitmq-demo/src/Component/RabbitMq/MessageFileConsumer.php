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

class MessageFileConsumer extends MessageConsumer implements ConsumerInterface
{
    /**
     * Listen to the queue and execute treatment
     *
     * @param AMQPMessage $message
     */
    public function execute(AMQPMessage $message)
    {
        $message = json_decode($message->body, true);

        list($linecount, $name) = explode(" ", exec('wc -l ' . escapeshellarg($message['filePath'])));

        $dateNow = date("Y_m_d_H_i");
        $directoryDist = __DIR__ . '/../../../public/' . $dateNow;

        try {
            if (!file_exists($directoryDist)) {
                mkdir($directoryDist, 0777, true);
            }
        } catch (\Exception $e) {
            // File already exist
        }

        print_r($message['fileName'] . "\n");

        if (copy($message['filePath'], $directoryDist . '/' . $message['fileName'])) {
            $this->container->get('old_sound_rabbit_mq.notification_producer')->publish(
                json_encode(array(
                        'fileName' => $message['fileName'],
                        'filePath' => $dateNow . '/' . $message['fileName'],
                        'action' => 'uploadFile'
                    )
                )
            );
        }

        $counter = 0;
        $file = fopen($message['filePath'], 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $counter++;

            $this->container->get('old_sound_rabbit_mq.send_emails_producer')->publish(
                json_encode(array(
                    'fileName' => $message['fileName'],
                    'fileLineCount' => $linecount,
                    'lineNumber' => $counter,
                    'line' => $line))
            );
        }
        fclose($file);
    }
}
