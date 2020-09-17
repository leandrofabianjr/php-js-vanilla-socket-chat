<?php

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ChatServer implements MessageComponentInterface {
    protected SplObjectStorage $clients;

    public function __construct() {
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo PHP_EOL . "Connection {$from->resourceId} sent:"
            . PHP_EOL . $msg . PHP_EOL;

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
