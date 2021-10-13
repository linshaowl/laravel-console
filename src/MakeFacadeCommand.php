<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

class MakeFacadeCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-facade';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the facade file';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Facade';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/facade.stub';

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
     * 参数 class
     * @var string
     */
    protected $argumentClass;

    /**
     * 选项 facade-extends
     * @var string
     */
    protected $optionFacadeExtends;

    /**
     * 选项 skip-methods
     * @var array
     */
    protected $optionSkipMethods;

    /**
     * {@inheritdoc}
     */
    protected function buildBeforeHandle()
    {
        parent::buildBeforeHandle();

        // 注解内容
        $this->annotation = $this->getAnnotation();
    }

    /**
     * 获取注解
     * @return string
     */
    protected function getAnnotation(): string
    {
        if (!class_exists($this->argumentClass)) {
            $this->throwThrowable('Class a does not exist');
        }

        // 反射处理
        $ref = new ReflectionClass($this->argumentClass);
        $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);

        $str = '/**';
        foreach ($methods as $method) {
            // 方法名称
            $name = $method->getName();
            // 跳过方法
            if ($this->skipMethod($name)) {
                continue;
            }

            // 参数组装
            $paramStr = '';
            $parameters = $method->getParameters();
            foreach ($parameters as $parameter) {
                if ($parameter->getType()) {
                    $paramStr .= sprintf('%s ', $this->getParameterType($parameter->getType()));
                }
                $paramStr .= sprintf('$%s%s', $parameter->getName(), $this->getParameterDefaultValue($parameter));
            }

            // 注解组装
            $str .= sprintf(
                '%s * @method static %s %s(%s)',
                "\n",
                $method->hasReturnType() ? $this->getParameterReturnType($method->getReturnType()) : 'void',
                $name,
                rtrim($paramStr, ', ')
            );
        }

        $str .= sprintf('%s%s * @see %s', "\n", $str !== '/**' ? " *\n" : '', $this->argumentClass);

        return "\n" . $str . "\n */";
    }

    /**
     * 跳过方法
     * @param string $name
     * @return bool
     */
    protected function skipMethod(string $name): bool
    {
        // 魔术方法跳过
        if (strpos($name, '__') === 0) {
            return true;
        }

        // 单例方法跳过
        if ($name === 'getInstance') {
            return true;
        }

        return in_array($name, $this->optionSkipMethods, true);
    }

    /**
     * 获取参数类型
     * @param ReflectionType $type
     * @return string
     */
    protected function getParameterType(ReflectionType $type): string
    {
        try {
            return $this->getClassParameter($type);
        } catch (Throwable $e) {
        }

        return ($type->allowsNull() ? '?' : '') . $this->getClassParameter($type->getName());
    }

    /**
     * 获取参数返回类型
     * @param ReflectionType $type
     * @return string
     */
    protected function getParameterReturnType(ReflectionType $type): string
    {
        if (method_exists($type, 'getTypes')) {
            return implode('|', array_map(function ($v) {
                return $this->getClassParameter($v->getName());
            }, $type->getTypes()));
        }

        return $this->getClassParameter($type->getName()) . ($type->allowsNull() ? '|null' : '');
    }

    /**
     * 获取类参数
     * @param string $parameter
     * @return string
     */
    protected function getClassParameter(string $parameter): string
    {
        $class = '\\' . $parameter;
        return class_exists($class) ? $class : $parameter;
    }

    /**
     * 获取参数默认值
     * @param ReflectionParameter $parameter
     * @return string
     */
    protected function getParameterDefaultValue(ReflectionParameter $parameter): string
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return ', ';
        }

        return sprintf(' = %s, ', $this->getStrValue($parameter->getDefaultValue()));
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
            ->replaceExtends($content, $this->extends)
            ->replaceClassName($content, $this->argumentClass);

        return $content;
    }

    /**
     * 替换类名
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceClassName(string &$content, string $replace)
    {
        $content = str_replace('%CLASSNAME%', $replace, $content);

        return $this;
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
        $this->argumentClass = $this->getCommandClass(
            $this->argument('class')
        );
        $this->argumentClass = '\\' . $this->argumentClass;

        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionForce = $this->option('force');
        $this->optionSuffix = $this->option('suffix');
        $this->optionFacadeExtends = $this->getCommandClass($this->option('facade-extends'));
        $this->optionSkipMethods = $this->option('skip-methods');

        // 引入、继承类
        if (!empty($this->optionFacadeExtends)) {
            $this->uses = "\n\nuse " . $this->optionFacadeExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionFacadeExtends);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, $this->entityName . ' name');
        $this->addArgument('class', InputArgument::REQUIRED, $this->entityName . ' class');

        $this->addOption(
            'dir',
            null,
            InputOption::VALUE_REQUIRED,
            'File saving path, relative to app directory',
            $this->makeFacadeOptionDirDefault()
        );
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName));
        $this->addOption(
            'facade-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define facade inheritance parent class',
            $this->optionFacadeExtendsDefault()
        );
        $this->addOption(
            'skip-methods',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Skip method names'
        );
    }
}
