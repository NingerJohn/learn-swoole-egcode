<?php
// create a server instance
$serv = new swoole_server("127.0.0.1", 9501); 

// attach handler for connect event, once client connected to server the registered handler will be executed
$serv->on('connect', function ($serv, $fd){  
    echo "Client:Connect.\n";
});

// attach handler for receive event, every piece of data received by server, the registered handler will be
// executed. And all custom protocol implementation should be located there.
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, $data);
});

$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

// start our server, listen on port and ready to accept connections
$serv->start();

?>
