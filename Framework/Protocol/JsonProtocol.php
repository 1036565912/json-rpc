<?php
declare(strict_types=1);
namespace Framework\Protocol;

/** json-rpc 协议组装解析类 */
class JsonProtocol
{
    //默认数据长度类型
    protected $lengthType = 'N';

    //数据起始下标
    protected $bodyOffset = 4;

    //包长度数据起始下标
    protected $lengthOffset = 0;

    /**
     * JsonProtocol constructor.
     * @param string $lengthType
     * @param int $lengthOffset
     * @param int $bodyOffset
     */
    public function __construct(string $lengthType = 'N', int $lengthOffset = 0, int $bodyOffset = 4)
    {
        $this->lengthOffset = $lengthOffset;
        $this->bodyOffset = $bodyOffset;
        $this->lengthType = $lengthType;
    }

    /**
     * 包解析
     * @param string $data
     * @return string
     * @author chenlin
     * @date  2020/10/19
     */
    public function decode(string $data): string
    {
        return substr($data, $this->bodyOffset);
    }


    /**
     * 包封装
     * @param string $data
     * @return string
     * @author chenlin
     * @date 2020/10/19
     */
    public function encode(string $data): string
    {
        return pack($this->lengthType, strlen($data)).$data;
    }
}