<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成工厂
 */
class MakeFactoryCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-factory';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the factory file';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Factory';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/factory.stub';

    /**
     * 注释
     * @var string
     */
    protected $annotation;

    /**
     * 参数 name
     * @var string
     */
    protected $argumentName;

    /**
     * 选项 scan-dir
     * @var array
     */
    protected $optionScanDir;

    /**
     * 选项 factory-extends
     * @var string
     */
    protected $optionFactoryExtends;

    /**
     * {@inheritdoc}
     */
    protected function buildBeforeHandle()
    {
        parent::buildBeforeHandle();

        // 注解内容
        $this->annotation = $this->getAnnotation($this->getScans());
    }

    /**
     * {@inheritdoc}
     */
    protected function extraCommands()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function getBuildContent(): string
    {
        $content = file_get_contents($this->stubPath);

        // 替换
        $this->replaceNamespace($content, $this->namespace)
            ->replaceClass($content, $this->class)
            ->replaceUses($content, $this->uses)
            ->replaceAnnotations($content, $this->annotation)
            ->replaceExtends($content, $this->extends);

        return $content;
    }

    /**
     * 获取扫描结果
     * @return array
     */
    protected function getScans(): array
    {
        // app 目录
        $appPath = app_path();

        $scans = [];
        foreach ($this->optionScanDir as $dir) {
            foreach (glob($dir . '*.php') as $file) {
                if (strpos($file, $appPath) !== 0) {
                    continue;
                }

                $class = rtrim(basename($file), '\.php');
                $replace = str_replace([$appPath, sprintf('/%s.php', $class)], '', $file);
                $scans[] = [
                    'namespace' => sprintf(
                        '\\App%s\\%s',
                        str_replace('/', '\\', $replace),
                        $class
                    ),
                    'method' => lcfirst($class),
                ];
            }
        }

        return $scans;
    }

    /**
     * 获取注解
     * @param array $scans
     * @return string
     */
    protected function getAnnotation(array $scans): string
    {
        if (empty($scans)) {
            return '';
        }

        $str = '/**';
        foreach ($scans as $scan) {
            $str .= sprintf(
                '%s * @method static %s %s(bool $refresh = false, array $params = [])',
                "\n",
                $scan['namespace'],
                $scan['method']
            );
        }

        return "\n" . $str . "\n */";
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaveDir(): string
    {
        return $this->filterOptionDir($this->optionDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function setParameters()
    {
        $this->argumentName = $this->filterArgumentName(
            $this->argument('name'),
            $this->entityName
        );
        $this->optionScanDir = array_map(function ($v) {
            return app_path($this->filterOptionDir($v));
        }, $this->option('scan-dir'));
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionForce = $this->option('force');
        $this->optionSuffix = $this->option('suffix');
        $this->optionFactoryExtends = $this->getCommandClass($this->option('factory-extends'));

        // 引入、继承类
        if (!empty($this->optionFactoryExtends)) {
            $this->uses = "\n\nuse " . $this->optionFactoryExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionFactoryExtends);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, $this->entityName . ' name');

        $this->addOption(
            'scan-dir',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'File scanning path, relative to app directory'
        );
        $this->addOption(
            'dir',
            null,
            InputOption::VALUE_REQUIRED,
            'File saving path, relative to app directory',
            $this->makeFactoryOptionDirDefault()
        );
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName));
        $this->addOption(
            'factory-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define factory inheritance parent class',
            $this->optionFactoryExtendsDefault()
        );
    }
}
