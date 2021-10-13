## 目录

- [安装配置](#%E5%AE%89%E8%A3%85%E9%85%8D%E7%BD%AE)
- [使用说明](#%E4%BD%BF%E7%94%A8%E8%AF%B4%E6%98%8E)
    - [创建控制器](#%E5%88%9B%E5%BB%BA%E6%8E%A7%E5%88%B6%E5%99%A8)
    - [创建模型](#%E5%88%9B%E5%BB%BA%E6%A8%A1%E5%9E%8B)
    - [创建服务层](#%E5%88%9B%E5%BB%BA%E6%9C%8D%E5%8A%A1%E5%B1%82)
    - [创建验证器](#%E5%88%9B%E5%BB%BA%E9%AA%8C%E8%AF%81%E5%99%A8)
    - [创建数据访问层](#%E5%88%9B%E5%BB%BA%E6%95%B0%E6%8D%AE%E8%AE%BF%E9%97%AE%E5%B1%82)
    - [通过文件创建所需文件](#%E9%80%9A%E8%BF%87%E6%96%87%E4%BB%B6%E5%88%9B%E5%BB%BA%E6%89%80%E9%9C%80%E6%96%87%E4%BB%B6)
    - [生成工厂文件](#%E7%94%9F%E6%88%90%E5%B7%A5%E5%8E%82%E6%96%87%E4%BB%B6)
    - [生成门面文件](#%E7%94%9F%E6%88%90%E9%97%A8%E9%9D%A2%E6%96%87%E4%BB%B6)

## 使用说明

> 安装后可直接配置环境变量使用
>
> 环境变量值参考：[env](docs/ENV.md)
>
> 环境变量可直接控制命令的生成文件路径和继承父类

使用以下命令安装：
```
composer require lswl/laravel-console
```
发布文件[可选]：
```php
php artisan vendor:publish --tag=lswl-console
```

### 创建控制器

> 默认不继承控制器, `--controller-extends` 参数可修改继承控制器

```php
// 创建 Test 控制器位于 app/Http/Controllers/Test.php
php artisan lswl:make-controller test
// 创建 Test 控制器修改继承父类
php artisan lswl:make-controller test --controller-extends App/BaseController
// 创建 Test 控制器并添加后缀，位于 app/Http/Controllers/TestController.php
php artisan lswl:make-controller test -s
...
```

### 创建模型

> 不传 name 将会从数据库读取所有表创建
>
> 覆盖创建模型时使用抽象语法树保证模型代码不丢失
>
> 默认不继承模型, `--model-extends` 参数可修改继承模型

```php
// 创建公用模型位于 app/Common/Models 并排除 test，foos 表
php artisan lswl:make-model --dir Common/Models -t test -t foos
// 创建 Test 模型位于 app/Http/Models/Test.php
php artisan lswl:make-model test
// 创建 Test 模型修改继承父类
php artisan lswl:make-model test --model-extends App\BaseModel
// 创建 Test 模型并添加后缀，位于 app/Http/Models/TestModel.php
php artisan lswl:make-model test -s
...
```

### 创建服务层

> 默认不继承服务, `--service-extends` 参数可修改继承服务

```php
// 创建 Test 服务位于 app/Http/Services/Test.php
php artisan lswl:make-service test
// 创建 Test 服务修改继承父类
php artisan lswl:make-service test --service-extends App\BaseService
// 创建 Test 服务并添加后缀，位于 app/Http/Services/TestService.php
php artisan lswl:make-service test -s
...
```

### 创建验证器

> 默认不继承验证器, `--validate-extends` 参数可修改继承验证器

```php
// 创建 Test 验证器位于 app/Http/Validates/Test.php
php artisan lswl:make-validate test
// 创建 Test 验证器修改继承父类
php artisan lswl:make-validate test --validate-extends App/BaseValidate
// 创建 Test 验证器并添加后缀，位于 app/Http/Validates/TestValidate.php
php artisan lswl:make-validate test -s
...
```

### 创建数据访问层

> 默认不继承数据访问, `--dao-extends` 参数可修改继承数据访问

```php
// 创建 Test 数据访问位于 app/Http/Daos/Test.php
php artisan lswl:make-dao test
// 创建 Test 数据访问修改继承父类
php artisan lswl:make-dao test --dao-extends App/BaseDao
// 创建 Test 数据访问并添加后缀，位于 app/Http/Daos/TestDao.php
php artisan lswl:make-dao test -s
...
```

### 通过文件创建所需文件

> 此命令通过 `config('lswl-console.tables')` 获取需要创建的文件名称
>
> 使用 `*-extends` 修改对应继承父类

```php
// 生成控制器、模型、服务、验证器、数据访问、迁移、填充
php artisan lswl:make-with-file --controller --model --service --validate --dao --migration --seeder
// 覆盖生成所有文件
php artisan lswl:make-with-file -f
// 覆盖生成控制器
php artisan lswl:make-with-file --force-controller
...
```

### 生成工厂文件

> 扫描指定目录下 `php` 文件来生成工厂
>
> 默认不继承工厂类, `--factory-extends` 参数可修改继承工厂类

```php
// 通过指定目录创建工厂,位于 app/Common/Factory/Service.php
php artisan lswl:make-factory service --scan-dir Http/Services --scan-dir Http/Index/Services

// 通过指定目录创建工厂,并增加后缀、保存至其他路径,位于 app/Commons/Factory/ServiceFactory.php
php artisan lswl:make-factory service --scan-dir Http/Services --dir Commons/Factory -s
...
```

### 生成门面文件

> 默认继承 `Illuminate\Support\Facades\Facade` 类, `--facade-extends` 参数可修改继承门面类

```php
// 使用指定类创建门面,位于 app/Commons/Facades/Kernel.php
php artisan lswl:make-facade Kernel App/Http/Kernel

// 使用指定类创建门面,并增加后缀、保存至其他路径,位于 app/Commons/Facades/KernelFacade.php
php artisan lswl:make-facade Kernel App/Http/Kernel --dir Commons/Facades -s
...
```
