<?php

/**
 * (c) linshaowl <linshaowl@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lswl\Console;

use Illuminate\Support\Str;
use Lswl\Console\Ast\ModelUpdateVisitor;
use Lswl\Console\Traits\ReplaceModelTrait;
use Lswl\Support\Helper\DBHelper;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser\Php7;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成模型
 */
class MakeModelCommand extends MakeCommand
{
    use ReplaceModelTrait;

    /**
     * 命令名称
     * @var string
     */
    protected $name = 'lswl:make-model';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Model';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/model.stub';

    /**
     * 参数 name 模式
     * @var int
     */
    protected $argumentNameMode = InputArgument::OPTIONAL;

    /**
     * 导入对象
     * @var string
     */
    protected $uses;

    /**
     * 继承对象
     * @var string
     */
    protected $extends;

    /**
     * 选项 connection
     * @var string
     */
    protected $optionConnection;

    /**
     * 选项 table
     * @var array
     */
    protected $optionTable;

    /**
     * 数据库辅助类
     * @var DBHelper
     */
    protected $dbHelper;

    /**
     * 表前缀
     * @var string
     */
    protected $prefix;

    /**
     * 表名称
     * @var string
     */
    protected $table;

    public function __construct()
    {
        parent::__construct();

        $this->dbHelper = DBHelper::getInstance(
            [
                'name' => $this->optionConnection,
            ]
        );
        $this->prefix = $this->dbHelper->getPrefix();
    }

    /**
     * {@inheritdoc}
     */
    protected function mainHandle()
    {
        if (!empty($this->argumentName)) {
            return $this->buildModel($this->argumentName);
        }

        return $this->buildModels();
    }

    /**
     * 生成所有模型文件
     * @return bool
     */
    protected function buildModels(): bool
    {
        // 获取所有表
        $tables = $this->dbHelper->getAllTables(false);
        foreach ($tables as $table) {
            if (in_array($table, $this->optionTable)) {
                continue;
            }
            $this->buildModel($table);
        }

        return true;
    }

    /**
     * 生成模型文件
     * @param string $name
     * @return bool
     */
    protected function buildModel(string $name): bool
    {
        // 生成类名称
        $this->class = $this->getClass($name);
        // 表名称
        $this->table = $name;

        // 保存文件
        $this->saveFilePath = $this->dir . $this->class . '.php';

        // 存在且不覆盖
        if (file_exists($this->saveFilePath) && !$this->optionForce) {
            return false;
        }

        // 生成前操作
        $this->buildBeforeHandle();

        // 生成操作
        $this->buildHandle();

        // 执行额外命令
        $this->extraCommands();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBuildContent(): string
    {
        $table = Str::snake($this->table);
        [$annotation, $fillable, $dates, $casts] = $this->getReplaceData($this->dbHelper->getAllColumns($table));

        // 存在按节点替换
        if (file_exists($this->saveFilePath)) {
            $lexer = new Emulative(
                [
                    'usedAttributes' => [
                        'comments',
                        'startLine',
                        'endLine',
                        'startTokenPos',
                        'endTokenPos',
                    ],
                ]
            );
            $parser = new Php7($lexer);

            $oldStmts = $parser->parse(file_get_contents($this->saveFilePath));
            $oldTokens = $lexer->getTokens();

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new CloningVisitor());
            $traverser->addVisitor(
                new ModelUpdateVisitor(ltrim($annotation), $fillable, $dates, $casts, $this->optionModelCastsForce)
            );
            $newStmts = $traverser->traverse($oldStmts);

            return (new Standard())->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
        }

        // 替换
        $content = file_get_contents($this->stubPath);
        $this->replaceNamespace($content, $this->namespace)
            ->replaceClass($content, $this->class)
            ->replaceUses($content, $this->uses)
            ->replaceExtends($content, $this->extends)
            ->replaceAnnotations($content, $annotation)
            ->replaceTable($content, sprintf("'%s'", $table))
            ->replaceFillable($content, $this->array2str($fillable))
            ->replaceDates($content, $this->array2str($dates))
            ->replaceCasts($content, $this->array2str($casts));

        return $content;
    }

    /**
     * 获取替换数据
     * @param array $columns
     * @return array
     */
    protected function getReplaceData(array $columns): array
    {
        $annotation = '';
        $fillable = [];
        $dates = [];
        $casts = [];

        foreach ($columns as $column) {
            $dataType = $this->formatDataType($column['data_type']);

            // 注释
            $annotation .= sprintf(
                '%s * @property %s $%s%s',
                "\n",
                $this->formatPropertyType($dataType),
                $column['column_name'],
                !empty($column['column_comment']) ? ' ' . $column['column_comment'] : ''
            );

            // 批量赋值字段
            $fillable[] = $column['column_name'];

            // 时间字段
            if (
                !in_array($column['column_name'], ['created_at', 'updated_at'])
                && in_array($dataType, ['datetime', 'timestamp', 'time'])
            ) {
                $dates[] = $column['column_name'];
            }

            // 类型转换
            if ($dataType == 'json') {
                $casts[$column['column_name']] = $dataType;
            }
        }

        return [
            $this->formatAnnotation($annotation),
            $fillable,
            $dates,
            $casts
        ];
    }

    /**
     * 格式化注解
     * @param string $annotation
     * @return string
     */
    protected function formatAnnotation(string $annotation): string
    {
        if (empty($annotation)) {
            return '';
        }

        return "\n/**" . $annotation . "\n */";
    }

    /**
     * 格式化数据类型
     * @param string $type
     * @return string|null
     */
    protected function formatDataType(string $type): ?string
    {
        switch ($type) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                return 'integer';
            case 'decimal':
            case 'float':
            case 'double':
            case 'real':
                return 'float';
            case 'bool':
            case 'boolean':
                return 'boolean';
            case 'datetime':
            case 'timestamp':
            case 'time':
            case 'json':
                return $type;
            default:
                return 'string';
        }
    }

    /**
     * 格式化属性类型
     * @param string $type
     * @return string
     */
    protected function formatPropertyType(string $type): string
    {
        switch ($type) {
            case 'integer':
                return 'int';
            case 'datetime':
            case 'timestamp':
            case 'time':
                return '\Carbon\Carbon';
            case 'json':
                return 'array';
            default:
                return $type;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setParameters()
    {
        parent::setParameters();

        $this->optionConnection = $this->option('connection') ?? 'mysql';
        $this->optionTable = array_map(function ($v) {
            return str_replace($this->prefix, '', $v);
        }, $this->option('table'));

        // 引入、继承类
        if (!empty($this->optionModelExtends)) {
            $this->uses = "\n\nuse " . $this->optionModelExtends . ';';
            $this->extends = ' extends ' . class_basename($this->optionModelExtends);
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
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Generate the validate file with the same name');
        $this->addOption('dao', null, InputOption::VALUE_NONE, 'Generate the dao file with the same name');

        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'Specify database links', 'mysql');
        $this->addOption(
            'table',
            't',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Exclude table names'
        );
    }
}
