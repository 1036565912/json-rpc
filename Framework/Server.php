<?php
declare(strict_types=1);
namespace Framework;

use Swoole\Server as TcpServer;
use Swoole\Http\Server as HttpServer;
use Framework\Processor\Processor;

class Server
{

    protected $server = null;

    /**
     * rpc服务启动构造函数
     * Server constructor.
     * @param string $host 监听的主机名
     * @param int $port 监听的端口
     * @param int $mode [SWOOLE_BASE, SWOOLE_PROCESS]两种模式
     * @param int $socketType socket类型 tcp ipv4 tcp6  udp udp6
     * @param string $type [tcp, http支持两种协议提供服务]
     *
     */
    public function __construct(string $host, int $port, int $mode = SWOOLE_PROCESS, int $socketType = SWOOLE_SOCK_TCP, string $type = 'tcp')
    {
        if ($type == 'tcp') {
            $this->server = new TcpServer($host, $port, $mode, $socketType);
            //添加配置 和 注册特定事件的回调
            $this->server->set([
                'reactor_num' => 1,
                'worker_num' => 4,
                'max_request' => 50,
                'backlog' => 128,
                //开启后台运行
                //'log_file' => ROOT_PATH.'/swoole_log.log',
                //'daemonize' => 1,
                //自定义包头包体
                'open_length_check' => true,
                'package_max_length' => 5 * 1024 * 1024,
                //这里不包含包头
                'package_length_type' => 'N',
                'package_length_offset' => 0,
                'package_body_offset' => 4,
                //开启应用层的心跳包检测
                'heartbeat_check_interval' => 60,
                'heartbeat_idle_time' => 600,

                //pid_file
                'pid_file' => ROOT_PATH.'/rpc.pid',
                //日志
                'log_level' => SWOOLE_LOG_TRACE,
                'trace_flags' => SWOOLE_TRACE_SERVER | SWOOLE_TRACE_HTTP2,
            ]);

            //绑定事件
            $this->server->on('Start',[$this,'start']);
            $this->server->on('WorkerStart', [$this, 'workerStart']);
            $this->server->on('Connect', [$this, 'connect']);
            $this->server->on('Receive', [$this, 'receive']);
            $this->server->on('Close', [$this, 'close']);
        } else {
            $this->server = new HttpServer($host, $port);
        }


        //启动server
        $this->server->start();

    }


    /**
     * master进程启动启动回调函数
     */
    public function start(TcpServer $server)
    {
        swoole_set_process_name('Rpc Server');
    }

    /**
     * worker进程启动回调
     * @param TcpServer $server
     * @param int $worker_id
     */
    public function workerStart(TcpServer $server, int $worker_id)
    {
        //worker进程启动的时候 可以初始化连接池 比如mysql, redis
        //MysqlPool::getInstance();
        //RedisPool::getInstance();
        swoole_set_process_name('Rpc Server Worker #'.$worker_id);
    }


    /**
     * 客户端连接成功回调
     * @param TcpServer $server
     * @param int $fd
     * @param int $reactorId
     */
    public function connect(TcpServer $server, int $fd, int $reactorId)
    {
        echo '[*] client connect success!'.PHP_EOL;
        echo '[*] client fd: '.$fd.PHP_EOL;
        echo '----------------------------'.PHP_EOL;
    }

    /**
     * 接受客户端发送的数据回调
     * @param TcpServer $server
     * @param int $fd
     * @param int $reactorId
     * @param string $data
     */
    public function receive(TcpServer $server, int $fd, int $reactorId, string $data)
    {
        //解析数据 然后调用特定的方法 执行然后返回结果给客户端
        echo '[*] accept client send data:'.PHP_EOL;
        echo $data.PHP_EOL;
        //解析调用并且返回
        $processor = new Processor();
        $res = $processor->deal($data);
        $server->send($fd, $res);
    }


    public function close(TcpServer $server, int $fd, int $reactorId)
    {
        echo '[*] client close'.PHP_EOL;
        echo '[*] client fd: '.$fd.PHP_EOL;
        echo '------------------------'.PHP_EOL;
    }
}