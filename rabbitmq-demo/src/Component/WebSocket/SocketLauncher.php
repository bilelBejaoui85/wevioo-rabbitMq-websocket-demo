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

namespace App\Component\WebSocket;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Class SocketLauncher
 * @abstract Parent class engine socket launcher for websocket command server
 * @category Dialog
 * @package Engine
 * @author Dev Team Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.2.0
 * @since Class available since Release 1.2.0
 */
class SocketLauncher implements MessageComponentInterface {

    /**
     * Declare Clients
     *
     * @var \SplObjectStorage $clients
     */
    protected $clients;

    /**
     * Service Container _container
     *
     * @var Container $_container
     */
    private $_container;

    /**
     * subscriptions Attribute
     *
     * @var Array $subscriptions
     */
    private $subscriptions;

    /**
     * users Attribute
     *
     * @var Array $users
     */
    private $users;

    /**
     * Declare suffix
     *
     * @var string $suffix
     */
    private static $suffix = 'Channel';

    /**
     * Construct function for the Counting Class
     *
     * @param Container $container
     */
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
    }

    /**
     * onMessage on message method event
     * @param \Ratchet\ConnectionInterface $from
     * @param String $msg
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        switch ($data['command']) {
            case "subscribe":
                $channel = $data['channel'];
                $this->subscriptions[$from->resourceId] = $channel;
                self::info("Subscribe to channel '{$channel}' done ");
                break;
            case "message":
                $data = $data['message'];
                if (isset($data['channel']) && count($this->subscriptions)) {

                    // Init subscribed channel
                    $subscribedChannels = array();

                    foreach ($this->subscriptions as $key => $subscription) {
                        if($subscription === $data['channel']) {
                            $subscribedChannels[$key] = $subscription;
                        }
                    }

                    // Send messages to correspendant channels
                    if(count($subscribedChannels)){
                        foreach($subscribedChannels as $key => $channel){
                            if(isset($this->users[$key]) && $this->users[$key]){
                                $this->users[$key]->send(json_encode($data));
                            }
                        }
                    }

                }
                break;
        }
    }

    /**
     * onOpen Open connection
     * @param \Ratchet\ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        self::info("Connection {$conn->resourceId} has opened");
    }

    /**
     * Close connection
     * @param \Ratchet\ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);
        self::info("Connection {$conn->resourceId} has disconnected");
    }

    /**
     * onError On error method event
     * @param \Ratchet\ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        self::error("An error has occurred: {$e->getMessage()}");
        $conn->close();
    }

    /**
     * Set error msg
     *
     * @param String $msg
     */
    public static function error($msg) {
        $output = new ConsoleOutput();
        $output->writeln("<error>".date('H:i:s') . ": " . $msg . ".</error>");
    }

    /**
     * Set info msg
     *
     * @param String $msg
     */
    public static function info($msg) {
        $output = new ConsoleOutput();
        $output->writeln("<info>".date('H:i:s') . ": " . $msg . "</info>");
    }

    /**
     * Set warn msg
     *
     * @param String $msg
     */
    public static function warn($msg) {
        $output = new ConsoleOutput();
        $output->writeln("<comment>".date('H:i:s') . ": " . $msg . "</comment>");
    }

    /**
     * Set log msg
     *
     * @param String $msg
     */
    public static function log($msg) {
        $output = new ConsoleOutput();
        $output->writeln("<log>".date('H:i:s') . ": " . $msg . "</log>");
    }
}
