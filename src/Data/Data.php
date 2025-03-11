<?php

namespace Weijiajia\HttpProxyManager\Data;

use Saloon\Contracts\DataObjects\WithResponse;
use Saloon\Traits\Responses\HasResponse;


abstract class Data implements WithResponse
{
    use HasResponse;

    public function toArrayFilterNull(): array
    {
        return array_filter($this->toArray(),static fn($value) => $value !== null);
    }

    abstract public function toArray(): array;


    public static function from(array $data): static
    {
        return new static(...$data);
    }

    protected function generateSessionId(int $length = 8): string
    {
        // 生成8位随机字符串作为会话ID
        return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }

}