<?php
/**
 * @author NJ
 * 
 * @ctime 2016-02-04
 *
 * 
 */

	/**
	 * 连接函数
	 * 
	 * 有客户端连接过来的时候，会自动调用这个函数
	 * @param  object $ws      swoole websocket server对象
	 * @param  object $request 客户端请求的对象
	 * @return NULL          无
	 */
	
	function on_connected($ws, $request)
	{
		var_dump($request->fd, $request->get, $request->server);
		$ws->push($request->fd, "hello, welcome to ws world\n");
		global $all_frame_ids;
		array_push($all_frame_ids, $request->fd);
	}

	/**
	 * 发送消息给客户端的函数
	 * 客户端发送消息来的时候，服务器会自动调用这个函数
	 * 
	 * @author NJ <ningerjohn@163.com>
	 * @param  object $ws    swoole websocket server 对象
	 * @param  object $frame 每次客户端的通信对象，（包括常用的客户端id，发送来的数据）
	 * @return NULL        无
	 */
	
	function on_send_message($ws, $frame)
	{
		echo "Message: {$frame->data}\n";
		global $all_frame_ids;
		// var_dump($all_frame_ids);
		foreach ($all_frame_ids as $key => $val) {
			# code...
			$ws->push($val, "server: {$frame->data}");
		}
	}

	/**
	 * 当有客户端关闭连接的时候，会调用这个函数
	 * @author NJ <ningerjohn@163.com>
	 * @param  object $ws swoole websocket server 对象
	 * @param  object $fd 客户端最后的id
	 * @return NULL     无
	 */
	
	function on_closed($ws, $fd){
		//
		echo "Client-{$fd} is closed \n";
		global $all_frame_ids;
		array_splice($all_frame_ids, array_search($fd, $all_frame_ids), 1);
		// var_dump($all_frame_ids);
	}


	// 客户端通信id全局数组
	global $all_frame_ids;
	$all_frame_ids = [];
	// 实例化websocket_server类
	$ws = new swoole_websocket_server("127.0.0.1", 9501);
	// $ws->set(['worker_num' => 4]); 
	// 当有客户端连接建立的时候，推送消息到刚才连接过来的客户端
	$ws->on('open', 'on_connected');
	// 服务器收到消息的时候，会把同样的消息发送回去
	$ws->on('message', 'on_send_message');
	// 客户端关闭的时候，会调用on_closed函数
	$ws->on('close', 'on_closed');
	// 开始swoole websocket server 服务器
	$ws->start();

?>
