<?php
/**
 * @desc RpcResponseException
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2023/2/10 23:32
 */

declare(strict_types=1);

namespace Tinywan\Rpc\Exception;


use Tinywan\Rpc\Error;

class RpcResponseException extends \Exception
{
    protected $error;

    public function __construct(Error $error)
    {
        parent::__construct($error->getMessage(), $error->getCode());
        $this->error = $error;
    }

    public function getError(): Error
    {
        return $this->error;
    }
}