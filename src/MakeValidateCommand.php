<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Symfony\Component\Console\Input\InputOption;

class MakeValidateCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-validate';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Validate';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/validate.stub';

    /**
     * {@inheritdoc}
     */
    protected function setParameters()
    {
        parent::setParameters();

        // 引入、继承类
        if (!empty($this->optionValidateExtends)) {
            $this->uses = "\n\nuse " . $this->optionValidateExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionValidateExtends);
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
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('dao', null, InputOption::VALUE_NONE, 'Generate the dao file with the same name');
    }
}
