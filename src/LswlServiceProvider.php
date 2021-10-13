<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Illuminate\Support\ServiceProvider;

class LswlServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        MakeControllerCommand::class,
        MakeModelCommand::class,
        MakeServiceCommand::class,
        MakeValidateCommand::class,
        MakeDaoCommand::class,
        MakeFactoryCommand::class,
        MakeFacadeCommand::class,
        MakeWithFileCommand::class,
    ];

    /**
     * @var string
     */
    protected $consoleConfigPath;

    public function boot()
    {
        $this->consoleConfigPath = __DIR__ . '/../config/lswl-console.php';

        // 注册命令
        $this->commands($this->commands);

        // 合并配置
        $this->mergeConfig();

        // 发布文件
        $this->publishFiles();
    }

    /**
     * 合并配置
     */
    protected function mergeConfig()
    {
        // 合并 console 配置
        $this->mergeConfigFrom(
            $this->consoleConfigPath,
            'lswl-console'
        );
    }

    /**
     * 发布文件
     */
    protected function publishFiles()
    {
        // 发布配置文件
        $this->publishes(
            [
                $this->consoleConfigPath => config_path('lswl-console.php'),
            ],
            'lswl-console'
        );
    }
}
