<?php

use Lswl\Console\Contracts\ConfigureDefaultInterface;

return [
    // 控制器路径
    'controller_dir' => env('LSWL_CONSOLE_CONTROLLER_DIR', ConfigureDefaultInterface::CONTROLLER_DIR),
    // 控制器继承
    'controller_extends' => env('LSWL_CONSOLE_CONTROLLER_EXTENDS', ConfigureDefaultInterface::CONTROLLER_EXTENDS),
    // 模型路径
    'model_dir' => env('LSWL_CONSOLE_MODEL_DIR', ConfigureDefaultInterface::MODEL_DIR),
    // 模型继承
    'model_extends' => env('LSWL_CONSOLE_MODEL_EXTENDS', ConfigureDefaultInterface::MODEL_EXTENDS),
    // 服务路径
    'service_dir' => env('LSWL_CONSOLE_SERVICE_DIR', ConfigureDefaultInterface::SERVICE_DIR),
    // 服务继承
    'service_extends' => env('LSWL_CONSOLE_SERVICE_EXTENDS', ConfigureDefaultInterface::SERVICE_EXTENDS),
    // 验证器路径
    'validate_dir' => env('LSWL_CONSOLE_VALIDATE_DIR', ConfigureDefaultInterface::VALIDATE_DIR),
    // 验证器继承
    'validate_extends' => env('LSWL_CONSOLE_VALIDATE_EXTENDS', ConfigureDefaultInterface::VALIDATE_EXTENDS),
    // 数据库访问对象路径
    'dao_dir' => env('LSWL_CONSOLE_DAO_DIR', ConfigureDefaultInterface::DAO_DIR),
    // 数据库访问对象继承
    'dao_extends' => env('LSWL_CONSOLE_DAO_EXTENDS', ConfigureDefaultInterface::DAO_EXTENDS),
    // 工厂路径
    'factory_dir' => env('LSWL_CONSOLE_FACTORY_DIR', ConfigureDefaultInterface::FACTORY_DIR),
    // 工厂继承
    'factory_extends' => env('LSWL_CONSOLE_FACTORY_EXTENDS', ConfigureDefaultInterface::FACTORY_EXTENDS),
    // 门面路径
    'facade_dir' => env('LSWL_CONSOLE_FACADE_DIR', ConfigureDefaultInterface::FACADE_DIR),
    // 门面继承
    'facade_extends' => env('LSWL_CONSOLE_FACADE_EXTENDS', ConfigureDefaultInterface::FACADE_EXTENDS),
    // 命令使用文件路径
    'with_file_dir' => env('LSWL_CONSOLE_WITH_FILE_DIR', ConfigureDefaultInterface::WITH_FILE_DIR),
    // 数据表名,可不带前缀
    'tables' => [],
];
