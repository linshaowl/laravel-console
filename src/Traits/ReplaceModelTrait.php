<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console\Traits;

/**
 * 替换模型辅助
 */
trait ReplaceModelTrait
{
    /**
     * 替换表名
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceTable(string &$content, string $replace)
    {
        $content = str_replace('%TABLE%', $replace, $content);

        return $this;
    }

    /**
     * 替换批量赋值属性
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceFillable(string &$content, string $replace)
    {
        $content = str_replace('%FILLABLE%', $replace, $content);

        return $this;
    }

    /**
     * 替换时间字段属性
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceDates(string &$content, string $replace)
    {
        $content = str_replace('%DATES%', $replace, $content);

        return $this;
    }

    /**
     * 替换属性类型转换
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceCasts(string &$content, string $replace)
    {
        $content = str_replace('%CASTS%', $replace, $content);

        return $this;
    }
}
