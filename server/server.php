<?php
    set_time_limit(0);

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    require dirname(__DIR__) . '/vendor/autoload.php';

    class Chat implements MessageComponentInterface {
        protected $clients;
        protected $users;

        public function __construct() {
            $this->clients = new \SplObjectStorage;
        }

        public function onOpen(ConnectionInterface $conn){
            //用clients去存conn
            $this->clients->attach($conn);
        }

        public function onClose(ConnectionInterface $conn) {
            $this->clients->detach($conn);
        }

        public function onMessage(ConnectionInterface $from, $data){
            $data = json_decode($data);
            $type = $data->type;
            if($type == 'chat'){
                $newMeg = [
                    'type'=> $type,
                    'user_id' => $data->user_id,
                    'chat_msg' => $data->chat_msg
                ];
                //你送資料過去的情況
                $from->send(json_encode($newMeg));
                foreach ($this->clients as $client) {
                    //別人送資料過來的情況
                    if($from != $client) $client->send(json_encode($newMeg));
                }
            }
        }

        public function onError(ConnectionInterface $conn , \Exception $e){
            $conn->close();
        }
    }

    $server = IoServer::factory(
        new HttpServer(new WsServer(new Chat())),
        8080
    );

    $server->run();
?>