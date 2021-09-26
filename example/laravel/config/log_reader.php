<?php
/**
 * @see App\Providers\LogReaderServiceProvider
 * @see Kriss\LogReader\LogReader
 */
return [
    // 是否启用
    'enable' => true,
    // 是否允许删除
    'deleteEnable' => true,
    // 日志根路径
    'logPath' => '@runtime/logs',
    // tail 查看时默认读取的行大小
    'tailDefaultLine' => 200,
];
