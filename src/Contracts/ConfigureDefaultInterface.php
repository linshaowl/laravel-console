<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console\Contracts;

/**
 * 配置默认值
 */
interface ConfigureDefaultInterface
{
    // 控制器路径
    public const CONTROLLER_DIR = 'Http/Controllers/';
    // 控制器继承
    public const CONTROLLER_EXTENDS = '';
    // 模型路径
    public const MODEL_DIR = 'Http/Models/';
    // 模型继承
    public const MODEL_EXTENDS = '';
    // 服务路径
    public const SERVICE_DIR = 'Http/Services/';
    // 服务继承
    public const SERVICE_EXTENDS = '';
    // 验证器路径
    public const VALIDATE_DIR = 'Http/Validates/';
    // 验证器继承
    public const VALIDATE_EXTENDS = '';
    // 数据库访问对象路径
    public const DAO_DIR = 'Http/Daos/';
    // 数据库访问对象继承
    public const DAO_EXTENDS = '';
    // 工厂路径
    public const FACTORY_DIR = 'Common/Factory/';
    // 工厂继承
    public const FACTORY_EXTENDS = '';
    // 门面路径
    public const FACADE_DIR = 'Common/Facades/';
    // 门面继承
    public const FACADE_EXTENDS = 'Illuminate\Support\Facades\Facade';
    // 命令使用文件路径
    public const WITH_FILE_DIR = 'Http/';
}
