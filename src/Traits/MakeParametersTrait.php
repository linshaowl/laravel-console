<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console\Traits;

use Symfony\Component\Console\Input\InputOption;

trait MakeParametersTrait
{
    /**
     * 选项 model-casts-force
     * @var bool
     */
    protected $optionModelCastsForce;

    /**
     * 选项 controller-extends
     * @var string
     */
    protected $optionControllerExtends;

    /**
     * 选项 model-extends
     * @var string
     */
    protected $optionModelExtends;

    /**
     * 选项 service-extends
     * @var string
     */
    protected $optionServiceExtends;

    /**
     * 选项 validate-extends
     * @var string
     */
    protected $optionValidateExtends;

    /**
     * 选项 dao-extends
     * @var string
     */
    protected $optionDaoExtends;

    /**
     * 设置通用属性
     */
    protected function setCommonProperties()
    {
        $this->optionModelCastsForce = $this->option('model-casts-force');
        $this->optionControllerExtends = $this->getCommandClass($this->option('controller-extends'));
        $this->optionModelExtends = $this->getCommandClass($this->option('model-extends'));
        $this->optionServiceExtends = $this->getCommandClass($this->option('service-extends'));
        $this->optionValidateExtends = $this->getCommandClass($this->option('validate-extends'));
        $this->optionDaoExtends = $this->getCommandClass($this->option('dao-extends'));
    }

    /**
     * 设置通用参数
     */
    protected function setCommonParameters()
    {
        $this->addOption('migration', null, InputOption::VALUE_NONE, 'Generate the migration file with the same name');
        $this->addOption('seeder', null, InputOption::VALUE_NONE, 'Generate the seeder file with the same name');
        $this->addOption('model-casts-force', null, InputOption::VALUE_NONE, 'Whether to override the casts attribute');
        $this->addOption(
            'controller-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define controller inheritance parent class',
            $this->optionControllerExtendsDefault()
        );
        $this->addOption(
            'model-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define model inheritance parent class',
            $this->optionModelExtendsDefault()
        );
        $this->addOption(
            'service-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define service inheritance parent class',
            $this->optionServiceExtendsDefault()
        );
        $this->addOption(
            'validate-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define validate inheritance parent class',
            $this->optionValidateExtendsDefault()
        );
        $this->addOption(
            'dao-extends',
            null,
            InputOption::VALUE_REQUIRED,
            'Define dao inheritance parent class',
            $this->optionDaoExtendsDefault()
        );
    }
}
