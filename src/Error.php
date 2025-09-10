<?php
/**
 * @desc Error
 * @help https://www.php.net/manual/zh/class.jsonserializable.php
 *      实现 JsonSerializable 的类可以 在 json_encode() 时定制他们的 JSON 表示法。
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2023/2/10 23:19
 */

declare(strict_types=1);

namespace Tinywan\Rpc;


class Error implements \JsonSerializable
{
    /**
     * @var int
     */
    protected int $code = 0;

    /**
     * @var string
     */
    protected string $message = '';

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param int    $code
     * @param string $message
     * @param mixed  $data
     *
     * @return Error
     */
    public static function make(int $code, string $message, $data = null): self
    {
        $instance = new static();

        $instance->code    = $code;
        $instance->message = $message;
        $instance->data    = $data;

        return $instance;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'code'    => $this->code,
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }
}