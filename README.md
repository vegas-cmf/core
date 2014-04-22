Vegas CMF Core lib
======================

# List of contents

* CRUD / Scaffolding
* Routing
* ServiceManager


## CRUD

### Overview ###

This controller extension allows you to perform all basic CRUD operations without writing single line of code. If you need to attach some stuff (like redirection or forwarding) to *create, edit, update or delete action*, use **Crud\Events**.

In all events you have access to current record and form state via *$this->scaffolding*.

### Usage ###

```
#!php
<?php
use \Vegas\Mvc\Controller;

class MyController extends Controller\Crud {
     protected $formName = 'My\Forms\My';    // default form used by CRUD
     protected $modelName = 'My\Models\My';  // default model used by CRUD

     public function initialize()
     {
         parent::initialize();

         // we can also add this event in the Module.php to the dispatcher
         $this->dispatcher->getEventsManager()->attach(
             Controller\Crud\Events::AFTER_CREATE, function() {
                 $this->response->redirect('user-admin/index');
             }
         );

         // attach more events
     }

     // other actions
}
```

## Scaffolding

Scaffolding class publish a lot of methods used in CRUD. You can create your own scaffolding by implementing **ScaffoldingInterface** or own adapter by implementing **AdapterInterface**. If you want to overload default scaffolding add following code to your Module.php file:

```
#!php
<?php
// ...
class Module extends \Vegas\Mvc\ModuleAbstract
{
    // ...

    protected function registerScaffolding($di)
    {
        $adapter = new \Vegas\DI\Scaffolding\Adapter\Mongo;
        $di->set('scaffolding', new \Vegas\DI\Scaffolding($adapter));
    }
}
```


## Routing

### Overview ###
The router component allows defining routes that are mapped to indicated resources. System provides three types of routing: default, rest, static.
Default and static route are defined in the same way. The only difference is the order in which rules are added to the router. Static routes are always added in the end, so they cannot be overwritten by another rules. It is useful for example for static pages, login, logout etc...

### Usage ###

In the following code, we define three routes : two static and one default (Note! that type **default** might be omitted). The URL **/page/info** will be mapped to route called **info**, not to **page** because it is static. Therefore URL **/page/test** will be mapped to route **page**.

```
#!php
<?php
return array(
    'login' => array(
        'route' => '/login',
        'paths' => array(
            'controller' => 'auth',
            'action' => 'login'
        ),
        'type' => 'static',
        'params' => array()
    ),
    'page' => array(
        'route' => '/page/{page}',
        'paths' => array(
            'controller' => 'page',
            'action' => 'show'
        ),
        'params' => array()
    ),
    'info' => array(
        'route' => '/page/info',
        'paths' => array(
            'controller' => 'articles',
            'action' => 'index'
        ),
        'type' => 'static',
        'params' => array()
    )
);
```

The third route type is useful for building the REST API. It allows to specify resources and actions with allowed HTTP Methods.

### Usage ###

In the following code, we define two **rest** routes. The first resource called **articles**, provides two methods: **index** accessible via GET method and **create** accessible via POST method. It means that the URL **/articles** is available only when **$_SERVER['REQUEST_METHOD']** is **POST** or **GET**.
The next resource called **products** provides one more URL which contains additional parameter **id**. In this case URL **/products/{id}** might be accessible via GET, PUT, DELETE methods.

```
#!php
<?php
use Vegas\Http\Method;

return array(
    'articles' => array(
        'route' => '/articles',
        'paths' => array(
            'controller' => 'articles'
        ),
        'type' => 'rest',
        'params' => array(
            'actions' => array(
                '/' => array(
                    'index' => Method::GET,
                    'create' => Method::POST
                )
            )
        )
    ),
    'products' => array(
        'route' => '/products',
        'paths' => array(
            'controller' => 'products'
        ),
        'type' => 'rest',
        'params' => array(
            'actions' => array(
                '/' =>  array(
                    'index' =>  Method::GET,
                    'create'  =>  Method::POST
                ),
                '/{id}' =>  array(
                    'show' => Method::GET,
                    'update' => Method::PUT,
                    'delete' => Method::DELETE
                ),
            )
        )
    )
);
```

## ServiceManager

### Overview ###

All files in each module *services* (and *models*) directory are autoloaded. They are shared and can be accessed from any other part of application. To do this dynamically, without registering by *$di->set()* method, you can use **Vegas\DI\ServiceManager**.

### Usage ###

In controller:

```
#!php
<?php
$this->serviceManager->getService('myModule:myService')->serviceMethod();
```

In view:

```
{{ serviceManager.getService('myModule:myService').serviceMethod() }}
```