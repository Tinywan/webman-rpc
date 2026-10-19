<?php

declare(strict_types=1);

it('response_rpc_json returns JSON string with default values', function () {
    $result = response_rpc_json();

    expect($result)->toBeString();
    $decoded = json_decode($result, true);

    expect($decoded)->toBe([
        'code' => 0,
        'msg' => 'success',
        'data' => [],
    ]);
});

it('response_rpc_json formats custom response data properly', function () {
    $result = response_rpc_json(200, '获取成功', ['user_id' => 1001, 'name' => '张三']);
    $decoded = json_decode($result, true);

    expect($decoded)->toBe([
        'code' => 200,
        'msg' => '获取成功',
        'data' => [
            'user_id' => 1001,
            'name' => '张三',
        ],
    ]);
});
