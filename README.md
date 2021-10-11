# MVC-Framework
Framework MVC básico e simples de utilizar

Este pacote é aderente com os [PSR-1], [PSR-2] e [PSR-4]. Se você observar negligências de conformidade, por favor envie um patch via pull request.

## Install

**Este pacote está listado no [Packgist](https://packagist.org/) foi desenvolvido para uso do [Composer](https://getcomposer.org/)**

E deve ser instalado com:
```bash
composer require mtakeshi/mvc-framework
```

Ou ainda alterando o composer.json do seu aplicativo inserindo:
```json
"require": {
        "mtakeshi/mvc-framework": "dev-main"
}
```

## Configurações

**No arquivo index.php que ficara na raiz do projeto, segue o código de inicialização**

Configuração de conexão com MySQL

```php
use criativa\lib\Config;

Config::setConfig((object) array(
    'prefix' => 'tab',
    'host' => 'localhost',
    'user' => 'root',
    'pwd' => '',
    'dbname' => 'teste',
    'charset' => 'utf8'
));
```

Definição de rotas

```php
use criativa\lib\Router;

//Lista de Rotas
Router::setRouters(array(
    'web' => 'web'
));

// Rota Padrão
Router::setRouterOnDefault('web');
```

Iniciar o sistema

```php
use criativa\lib\System;

$System = new System();
$System->run();
```

Arquivo index.php

```php
require 'vendor/autoload.php';

use criativa\lib\Config;
use criativa\lib\Router;
use criativa\lib\System;

Config::setConfig((object) array(
    'prefix' => 'tab',
    'host' => 'localhost',
    'user' => 'root',
    'pwd' => '',
    'dbname' => 'teste',
    'charset' => 'utf8'
));

Router::setRouters(array(
    'web' => 'web'
));

Router::setRouterOnDefault('web');

$System = new System();
$System->run();
```
