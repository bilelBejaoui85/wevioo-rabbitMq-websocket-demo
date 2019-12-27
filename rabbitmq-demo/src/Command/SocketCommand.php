<?php

/**
 * This file defines the websocket server
 *
 * @category DialogBundle
 * @package Command
 * @author Dev Team Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.2.0
 * @since File available since Release 1.2.0
 * 
 */
namespace App\Command;

use App\Component\WebSocket\SocketLauncher;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class SocketCommand extends Command {

    /**
     * Set commande name
     */
    protected function configure() {
        $this->setName('demo:socket:run');
    }

    /**
     * Run commande
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SocketLauncher()
                )
            ), 8080, '127.0.0.1'
        );
        $output->writeln("<info>".date('H:i:s') . ": Listning to webscoket's client...</info>");
        $server->run();
    }
}
