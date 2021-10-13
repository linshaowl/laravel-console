<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Lswl\Console\Traits\ConfigureDefaultTrait;
use Lswl\Console\Traits\MakeParametersTrait;
use Lswl\Console\Traits\MakeTrait;
use Symfony\Component\Console\Input\InputOption;

/**
 * 通过关联文件生成
 */
class MakeWithFileCommand extends Command
{
    use MakeTrait;
    use MakeParametersTrait;
    use ConfigureDefaultTrait;

    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-with-file';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate some file with file';

    /**
     * 选项 dir
     * @var string
     */
    protected $optionDir;

    /**
     * 选项 suffix
     * @var string
     */
    protected $optionSuffix;

    /**
     * 选项 controller
     * @var string
     */
    protected $optionController;

    /**
     * 是否覆盖控制器
     * @var bool
     */
    protected $isForceController;

    /**
     * 选项 model
     * @var string
     */
    protected $optionModel;

    /**
     * 是否覆盖模型
     * @var bool
     */
    protected $isForceModel;

    /**
     * 选项 service
     * @var string
     */
    protected $optionService;

    /**
     * 是否覆盖服务
     * @var bool
     */
    protected $isForceService;

    /**
     * 选项 validate
     * @var string
     */
    protected $optionValidate;

    /**
     * 是否覆盖验证器
     * @var bool
     */
    protected $isForceValidate;

    public function handle()
    {
        // 设置参数
        $this->setParameters();

        // 读取生成文件配置
        $tables = config('lswl-console.tables', []);

        // 数据表不存在
        if (empty($tables)) {
            // 运行完成
            $this->runComplete();
            return;
        }

        // 过滤名称
        $tables = $this->filterTables($tables);

        // 生成文件
        foreach ($tables as $table) {
            $this->buildFile($table);
        }

        // 运行完成
        $this->runComplete();
    }

    /**
     * 过滤表名
     * @param array $tables
     * @return array
     */
    protected function filterTables(array $tables): array
    {
        // 数据表前缀
        $prefix = app('db.connection')->getConfig('prefix');

        return array_values(
            array_filter(
                array_unique(
                    array_map(function ($table) use ($prefix) {
                        return str_replace($prefix, '', $table);
                    }, $tables)
                )
            )
        );
    }

    /**
     * 创建文件
     * @param string $name
     */
    protected function buildFile(string $name)
    {
        // 命令参数
        $arguments = [
            'name' => $name,
            '--suffix' => $this->optionSuffix,
            '--model-casts-force' => $this->optionModelCastsForce,
            '--controller-extends' => $this->optionControllerExtends,
            '--model-extends' => $this->optionModelExtends,
            '--service-extends' => $this->optionServiceExtends,
            '--validate-extends' => $this->optionValidateExtends,
            '--dao-extends' => $this->optionDaoExtends,
        ];

        // 创建控制器
        if ($this->optionController) {
            $arguments['--force'] = $this->isForceController;
            $arguments['--dir'] = $this->optionDir . 'Controllers/';
            $this->call('lswl:make-controller', $arguments);
        }

        // 创建模型
        if ($this->optionModel) {
            $arguments['--force'] = $this->isForceModel;
            $arguments['--dir'] = $this->optionDir . 'Models/';
            $this->call('lswl:make-model', $arguments);
        }

        // 创建服务
        if ($this->optionService) {
            $arguments['--force'] = $this->isForceService;
            $arguments['--dir'] = $this->optionDir . 'Services/';
            $this->call('lswl:make-service', $arguments);
        }

        // 创建验证器
        if ($this->optionValidate) {
            $arguments['--force'] = $this->isForceValidate;
            $arguments['--dir'] = $this->optionDir . 'Validates/';
            $this->call('lswl:make-validate', $arguments);
        }

        // 创建迁移
        if ($this->option('migration')) {
            try {
                $this->call('make:migration', [
                    'name' => sprintf(
                        'create_%s_table',
                        Str::plural(Str::snake($name))
                    )
                ]);
            } catch (InvalidArgumentException $e) {
            }
        }

        // 创建填充
        if ($this->option('seeder')) {
            $this->call('make:seeder', [
                'name' => sprintf(
                    '%sTableSeeder',
                    Str::plural(ucfirst($name))
                )
            ]);
        }
    }

    /**
     * 设置参数
     */
    protected function setParameters()
    {
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionSuffix = $this->option('suffix');
        $this->optionController = $this->option('controller');
        $this->isForceController = $this->option('force') || $this->option('force-controller');
        $this->optionModel = $this->option('model');
        $this->isForceModel = $this->option('force') || $this->option('force-model');
        $this->optionService = $this->option('service');
        $this->isForceService = $this->option('force') || $this->option('force-service');
        $this->optionValidate = $this->option('validate');
        $this->isForceValidate = $this->option('force') || $this->option('force-validate');

        // 设置通用属性
        $this->setCommonProperties();
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->addOption(
            'dir',
            null,
            InputOption::VALUE_REQUIRED,
            'File saving path, relative to app directory',
            $this->makeWithFileOptionDirDefault()
        );
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
        $this->addOption('force-controller', null, InputOption::VALUE_NONE, 'Overwrite existing controller file');
        $this->addOption('force-model', null, InputOption::VALUE_NONE, 'Overwrite existing model file');
        $this->addOption('force-service', null, InputOption::VALUE_NONE, 'Overwrite existing service file');
        $this->addOption('force-validate', null, InputOption::VALUE_NONE, 'Overwrite existing validate file');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add suffix'));
        $this->addOption(
            'controller',
            null,
            InputOption::VALUE_NONE,
            'Generate the controller file with the same name'
        );
        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');

        // 设置通用参数
        $this->setCommonParameters();
    }
}
