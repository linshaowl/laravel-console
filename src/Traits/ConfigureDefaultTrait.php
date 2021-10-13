<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console\Traits;

use Lswl\Console\Contracts\ConfigureDefaultInterface;

/**
 * 配置默认值辅助
 */
trait ConfigureDefaultTrait
{
    /**
     * make-controller 命令选项 dir 默认值
     * @return string
     */
    public function makeControllerOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('controller_dir', ConfigureDefaultInterface::CONTROLLER_DIR);
    }

    /**
     * 选项 controller-extends 默认值
     * @return string
     */
    public function optionControllerExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'controller_extends',
            ConfigureDefaultInterface::CONTROLLER_EXTENDS
        );
    }

    /**
     * make-model 命令选项 dir 默认值
     * @return string
     */
    public function makeModelOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('model_dir', ConfigureDefaultInterface::MODEL_DIR);
    }

    /**
     * 选项 model-extends 默认值
     * @return string
     */
    public function optionModelExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'model_extends',
            ConfigureDefaultInterface::MODEL_EXTENDS
        );
    }

    /**
     * make-service 命令选项 dir 默认值
     * @return string
     */
    public function makeServiceOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('service_dir', ConfigureDefaultInterface::SERVICE_DIR);
    }

    /**
     * 选项 service-extends 默认值
     * @return string
     */
    public function optionServiceExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'service_extends',
            ConfigureDefaultInterface::SERVICE_EXTENDS
        );
    }

    /**
     * make-validate 命令选项 dir 默认值
     * @return string
     */
    public function makeValidateOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('validate_dir', ConfigureDefaultInterface::VALIDATE_DIR);
    }

    /**
     * 选项 validate-extends 默认值
     * @return string
     */
    public function optionValidateExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'validate_extends',
            ConfigureDefaultInterface::VALIDATE_EXTENDS
        );
    }

    /**
     * make-dao 命令选项 dir 默认值
     * @return string
     */
    public function makeDaoOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('dao_dir', ConfigureDefaultInterface::DAO_DIR);
    }

    /**
     * 选项 dao-extends 默认值
     * @return string
     */
    public function optionDaoExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'dao_extends',
            ConfigureDefaultInterface::DAO_EXTENDS
        );
    }

    /**
     * make-factory 命令选项 dir 默认值
     * @return string
     */
    public function makeFactoryOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('factory_dir', ConfigureDefaultInterface::FACTORY_DIR);
    }

    /**
     * 选项 factory-extends 默认值
     * @return string
     */
    public function optionFactoryExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'factory_extends',
            ConfigureDefaultInterface::FACTORY_EXTENDS
        );
    }

    /**
     * make-facade 命令选项 dir 默认值
     * @return string
     */
    public function makeFacadeOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('facade_dir', ConfigureDefaultInterface::FACADE_DIR);
    }

    /**
     * 选项 facade-extends 默认值
     * @return string
     */
    public function optionFacadeExtendsDefault(): string
    {
        return $this->getOptionExtendsDefault(
            'facade_extends',
            ConfigureDefaultInterface::FACADE_EXTENDS
        );
    }

    /**
     * make-with-file 命令选项 dir 默认值
     * @return string
     */
    public function makeWithFileOptionDirDefault(): string
    {
        return $this->getOptionDirDefault('with_file_dir', ConfigureDefaultInterface::WITH_FILE_DIR);
    }

    /**
     * 获取选项 dir 默认值
     * @param string $key
     * @param string $default
     * @return string
     */
    private function getOptionDirDefault(string $key, string $default): string
    {
        $config = config('lswl-console.' . $key, '');
        return !empty($config) ? $config : $default;
    }

    /**
     * 获取选项 extends 默认值
     * @param string $key
     * @param string $default
     * @return string
     */
    private function getOptionExtendsDefault(string $key, string $default): string
    {
        $config = config('lswl-console.' . $key, '');
        return class_exists($config) ? $config : $default;
    }
}
