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
use Lswl\Console\Traits\ReplaceTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

abstract class MakeCommand extends Command
{
    use MakeTrait;
    use MakeParametersTrait;
    use ReplaceTrait;
    use ConfigureDefaultTrait;

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the %s file';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName;

    /**
     * 默认保存路径
     * @var string
     */
    protected $defaultDir = 'Http/';

    /**
     * 参数 name 模式
     * @var int
     */
    protected $argumentNameMode = InputArgument::REQUIRED;

    /**
     * 文件保存路径
     * @var string
     */
    protected $dir;

    /**
     * 命名空间
     * @var string
     */
    protected $namespace;

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath;

    /**
     * 生成类名称
     * @var string
     */
    protected $class;

    /**
     * 导入对象
     * @var string
     */
    protected $uses = '';

    /**
     * 继承对象
     * @var string
     */
    protected $extends = '';

    /**
     * 保存文件路径
     * @var string
     */
    protected $saveFilePath;

    /**
     * 参数 name
     * @var string
     */
    protected $argumentName;

    /**
     * 选项 dir
     * @var string
     */
    protected $optionDir;

    /**
     * 选项 force
     * @var bool
     */
    protected $optionForce;

    /**
     * 选项 suffix
     * @var bool
     */
    protected $optionSuffix;

    public function __construct()
    {
        $this->description = sprintf(
            $this->description,
            strtolower($this->entityName)
        );

        // 设置默认路径
        $method = sprintf('make%sOptionDirDefault', $this->entityName);
        $this->defaultDir = call_user_func([$this, $method]);

        parent::__construct();
    }

    public function handle()
    {
        // 设置参数
        $this->setParameters();

        // 获取保存文件夹
        $dir = $this->getSaveDir();
        // 保存文件夹
        $this->dir = app_path($dir);
        // 命名空间
        $this->namespace = $this->getNamespace($dir);

        // 创建文件夹
        $this->createDir($this->dir);

        try {
            // 运行
            $this->mainHandle();

            // 运行完成
            $this->runComplete();
        } catch (Throwable $e) {
            $this->runFail(sprintf('[%s:%d] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
        }
    }

    /**
     * 主要操作
     */
    protected function mainHandle()
    {
        // 生成类名称
        $this->class = $this->getClass($this->argumentName);

        // 保存文件
        $this->saveFilePath = $this->dir . $this->class . '.php';

        // 存在且不覆盖
        if (file_exists($this->saveFilePath) && !$this->optionForce) {
            return;
        }

        // 生成前操作
        $this->buildBeforeHandle();

        // 生成操作
        $this->buildHandle();

        // 执行额外命令
        $this->extraCommands();
    }

    /**
     * 获取保存文件夹
     * @return string
     */
    protected function getSaveDir(): string
    {
        $dir = $this->filterOptionDir($this->optionDir);
        // 路径不存在实体后缀
        if (!preg_match(sprintf('/[(%ss\/)(%s)]$/i', $this->entityName, $this->entityName), $dir)) {
            $dir .= $this->entityName . 's/';
        }

        return $dir;
    }

    /**
     * 生成前操作
     */
    protected function buildBeforeHandle()
    {
        // 命令继承参数是否存在
        $prop = sprintf('option%sExtends', $this->entityName);
        if (!property_exists($this, $prop)) {
            return;
        }

        // 是否名称相同
        $isNameSame = $this->class == class_basename($this->{$prop});
        // 是否命名空间相同
        $isNamespaceSame = $this->classNamespace($this->{$prop}) === $this->namespace;

        // 名称和命名空间相同
        if ($isNameSame && $isNamespaceSame) {
            $this->throwThrowable('Cannot inherit oneself.');
        }

        // 名称相同
        if ($isNameSame) {
            $this->uses = '';
            $this->extends = ' extends \\' . $this->{$prop};
        }

        // 命名空间相同
        if ($isNamespaceSame) {
            $this->uses = '';
        }
    }

    /**
     * 生成操作
     */
    protected function buildHandle()
    {
        file_put_contents($this->saveFilePath, $this->getBuildContent());
        $this->info($this->saveFilePath . ' create Succeed!');
    }

    /**
     * 获取生成类名称
     * @param string $name
     * @return string
     */
    protected function getClass(string $name): string
    {
        $name = Str::singular($this->filterStr($name));
        // 判断是否添加后缀
        if (!preg_match(sprintf('/%s$/i', $this->entityName), $name) && $this->optionSuffix) {
            $name .= '_' . $this->entityName;
        }
        return Str::studly($name);
    }

    /**
     * 获取生成内容
     * @return string
     */
    protected function getBuildContent(): string
    {
        $content = file_get_contents($this->stubPath);

        // 替换
        $this->replaceNamespace($content, $this->namespace)
            ->replaceClass($content, $this->class)
            ->replaceUses($content, $this->uses)
            ->replaceExtends($content, $this->extends);

        return $content;
    }

    /**
     * 执行额外命令
     */
    protected function extraCommands()
    {
        // 名称
        $name = $this->filterArgumentName($this->argumentName, $this->entityName);

        // 命令参数
        $arguments = [
            'name' => $name,
            '--force' => $this->optionForce,
            '--suffix' => $this->optionSuffix,
            '--model-casts-force' => $this->optionModelCastsForce,
            '--controller-extends' => $this->optionControllerExtends,
            '--model-extends' => $this->optionModelExtends,
            '--service-extends' => $this->optionServiceExtends,
            '--validate-extends' => $this->optionValidateExtends,
            '--dao-extends' => $this->optionDaoExtends,
        ];
        // 保存路径
        $saveDir = $this->getSaveDir();
        // 路径格式
        $dirFormat = str_replace(
            $this->entityName,
            '%',
            $saveDir
        );

        // 创建控制器
        if ($this->hasOption('controller') && $this->option('controller')) {
            $dir = sprintf($dirFormat, 'Controllers');
            $arguments['--dir'] = $dir;
            $this->call('lswl:make-controller', $arguments);
        }

        // 创建模型
        if ($this->hasOption('model') && $this->option('model')) {
            $dir = sprintf($dirFormat, 'Models');
            $arguments['--dir'] = $dir;
            $this->call('lswl:make-model', $arguments);
        }

        // 创建服务
        if ($this->hasOption('service') && $this->option('service')) {
            $dir = sprintf($dirFormat, 'Services');
            $arguments['--dir'] = $dir;
            $this->call('lswl:make-service', $arguments);
        }

        // 创建验证器
        if ($this->hasOption('validate') && $this->option('validate')) {
            $dir = sprintf($dirFormat, 'Validates');
            $arguments['--dir'] = $dir;
            $this->call('lswl:make-validate', $arguments);
        }

        // 创建 DAO
        if ($this->hasOption('dao') && $this->option('dao')) {
            $dir = sprintf($dirFormat, 'Daos');
            $arguments['--dir'] = $dir;
            $this->call('lswl:make-dao', $arguments);
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
        // 命令参数
        $this->argumentName = $this->argument('name') ?? '';

        // 命令选项
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionForce = $this->option('force');
        $this->optionSuffix = $this->option('suffix');

        // 设置通用属性
        $this->setCommonProperties();
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->addArgument('name', $this->argumentNameMode, $this->entityName . ' name');

        $this->addOption(
            'dir',
            null,
            InputOption::VALUE_REQUIRED,
            'File saving path, relative to app directory',
            $this->defaultDir
        );
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName));

        // 设置通用参数
        $this->setCommonParameters();
    }
}
