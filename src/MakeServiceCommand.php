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
 * 生成服务
 */
class MakeServiceCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-service';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Service';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/service.stub';

    /**
     * {@inheritdoc}
     */
    protected function setParameters()
    {
        parent::setParameters();

        // 引入、继承类
        if (!empty($this->optionServiceExtends)) {
            $this->uses = "\n\nuse " . $this->optionServiceExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionServiceExtends);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption(
            'controller',
            null,
            InputOption::VALUE_NONE,
            'Generate the controller file with the same name'
        );
        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');
        $this->addOption('dao', null, InputOption::VALUE_NONE, 'Generate the dao file with the same name');
    }
}
