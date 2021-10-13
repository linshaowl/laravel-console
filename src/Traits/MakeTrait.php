<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console\Traits;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * 创建命令辅助
 */
trait MakeTrait
{
    /**
     * 过滤字符串
     * @param string $str
     * @return string
     */
    protected function filterStr(string $str): string
    {
        return str_replace(['/', '\\'], '', $str);
    }

    /**
     * 过滤路径
     * @param string $dir
     * @return array
     */
    protected function filterDir(string $dir): array
    {
        return array_filter(
            explode(
                '/',
                str_replace('\\', '', $dir)
            )
        );
    }

    /**
     * 获取路径字符串
     * @param array $dir
     * @return string
     */
    protected function getDirStr(array $dir): string
    {
        $res = '';
        foreach ($dir as $v) {
            $res .= ucfirst($v) . '/';
        }
        return $res;
    }

    /**
     * 过滤选项路径
     * @param string $dir
     * @return string
     */
    protected function filterOptionDir(string $dir): string
    {
        return $this->getDirStr($this->filterDir($dir));
    }


    /**
     * 创建文件夹
     * @param string $dir
     * @return bool
     */
    protected function createDir(string $dir): bool
    {
        return !is_dir($dir) && mkdir($dir, 0755, true);
    }

    /**
     * 获取命名空间
     * @param string $dir
     * @return string
     */
    protected function getNamespace(string $dir): string
    {
        return 'App\\' . str_replace('/', '\\', rtrim($dir, '/'));
    }

    /**
     * 过滤参数名称
     * @param string $name
     * @param string $suffix
     * @return string
     */
    protected function filterArgumentName(string $name, string $suffix): string
    {
        return Str::singular(
            preg_replace(
                sprintf('/%s$/i', $suffix),
                '',
                $this->filterStr($name)
            )
        );
    }

    /**
     * 获取命令行类名称
     * @param string $name
     * @return string
     */
    protected function getCommandClass(string $name): string
    {
        return preg_replace('/\/+/', '\\', trim($name, '/'));
    }

    /**
     * 获取类的命名空间
     * @param string $name
     * @return string
     */
    protected function classNamespace(string $name): string
    {
        $class = explode('\\', $name);
        array_pop($class);
        return implode('\\', $class);
    }

    /**
     * 数组转字符
     * @param array $arr
     * @return string
     */
    protected function array2str(array $arr): string
    {
        $str = '';
        foreach ($arr as $k => $v) {
            // 键非数值
            if (!is_int($k)) {
                $str .= sprintf("'%s' => ", $k);
            }

            $str .= sprintf("%s, ", $this->getStrValue($v));
        }

        return sprintf('[%s]', rtrim($str, ', '));
    }

    /**
     * 获取字符串值
     * @param $value
     * @return mixed|string
     */
    protected function getStrValue($value)
    {
        // 值是字符串
        if (is_string($value)) {
            return sprintf("'%s'", $value);
        }

        // 值是 null
        if (is_null($value)) {
            return 'null';
        }

        // 值是布尔
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // 值是数组
        if (is_array($value)) {
            return $this->array2str($value);
        }

        return $value;
    }

    /**
     * 运行完成
     */
    protected function runComplete()
    {
        $this->info(sprintf('Command %s run completed!', $this->name));
    }

    /**
     * 运行失败
     * @param string $msg
     */
    protected function runFail(string $msg)
    {
        $this->error(sprintf('Command %s run fail: %s', $this->name, "\n" . $msg));
    }

    /**
     * 抛出异常
     * @param string $msg
     * @throws InvalidArgumentException
     */
    protected function throwThrowable(string $msg)
    {
        throw new InvalidArgumentException($msg);
    }
}
