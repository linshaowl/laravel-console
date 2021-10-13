<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Symfony\Component\Console\Input\InputOption;

class MakeDaoCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-dao';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Dao';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/dao.stub';

    /**
     * {@inheritdoc}
     */
    protected function setParameters()
    {
        parent::setParameters();

        // 引入、继承类
        if (!empty($this->optionDaoExtends)) {
            $this->uses = "\n\nuse " . $this->optionDaoExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionDaoExtends);
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
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');
    }
}
