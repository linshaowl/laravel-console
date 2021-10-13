<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console\Ast;

use Lswl\Support\Utils\Helper;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeVisitorAbstract;

class ModelUpdateVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $annotation;
    /**
     * @var array
     */
    protected $fillable;

    /**
     * @var array
     */
    protected $dates;

    /**
     * @var array
     */
    protected $casts;

    /**
     * @var bool
     */
    protected $castsForce;

    public function __construct(string $annotation, array $fillable, array $dates, array $casts, bool $castsForce)
    {
        $this->annotation = $annotation;
        $this->fillable = $fillable;
        $this->dates = $dates;
        $this->casts = $casts;
        $this->castsForce = $castsForce;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        switch ($node) {
            case $node instanceof PropertyProperty:
                $name = (string)$node->name;
                if (in_array($name, ['fillable', 'dates', 'casts'])) {
                    $node = $this->rewritePropertyProperty(
                        $node,
                        $this->{$name},
                        $name == 'casts' ? $this->castsForce : true
                    );
                }
                break;
            case $node instanceof Class_:
                if (!empty($this->annotation)) {
                    $node->setDocComment(new Doc($this->annotation));
                }
                break;
        }

        return $node;
    }

    /**
     * 重写节点
     * @param PropertyProperty $node
     * @param array $data
     * @param bool $isForce
     * @return PropertyProperty
     */
    protected function rewritePropertyProperty(PropertyProperty $node, array $data, bool $isForce)
    {
        $items = [];
        $keys = [];

        // 不覆盖的情况下读取之前的
        if (!$isForce && $node->default instanceof Array_) {
            $items = $node->default->items;

            foreach ($items as $item) {
                if (is_object($item->key)) {
                    $keys[] = $item->key->value;
                }
            }
        }

        foreach ($data as $k => $v) {
            if (in_array($k, $keys)) {
                continue;
            }

            if (is_integer($k)) {
                $items[] = new ArrayItem(
                    new String_($v)
                );
            } else {
                $items[] = new ArrayItem(
                    new String_($v),
                    new String_($k)
                );
            }
        }

        $node->default = new Array_($items, [
            'kind' => Array_::KIND_SHORT,
        ]);
        return $node;
    }
}
