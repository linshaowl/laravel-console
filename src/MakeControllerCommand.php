<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Symfony\Component\Console\Input\InputOption;

/**
 * 生成控制器
 */
class MakeControllerCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-controller';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Controller';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/controller.stub';

    /**
     * {@inheritdoc}
     */
    protected function setParameters()
    {
        parent::setParameters();

        // 引入、继承类
        if (!empty($this->optionControllerExtends)) {
            $this->uses = "\n\nuse " . $this->optionControllerExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionControllerExtends);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');
        $this->addOption('dao', null, InputOption::VALUE_NONE, 'Generate the dao file with the same name');
    }
}
