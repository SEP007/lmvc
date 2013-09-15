# LMVC (cool)

## Lean Model View Controller

This project contains a very small MVC framework written with simple PHP classes. It's developed with the following boundary conditions:

* Just PHP - no external PHP libraries
* Convention over configuration
* No annotations or other stuff like that
* PSR-0 standard

For further documentation you can checkout the source which is pretty lucid or the example application build with *lmvc* such as [LMVC-Patat](https://github.com/scandio/lmvc-patat).

To start a project from scratch you might want to look into [LMVC-Afresh](https://github.com/scandio/lmvc-afresh) which gives a decent set of useful boilerplate.

## A Project's Directory-tree

A lmvc project normally has a bunch of standard directories which you need to setup in the application's [config-file](https://github.com/scandio/lmvc-afresh/blob/master/config.json).

Generally a tree would look like this:

- app
    - views
        - Controller1
            - actionName.html
            - actionName2.html
        - Controller2
            - actionName.html
    - controllers
        - Controller1.php
        - Controller2.php
    - models
        - Model1.php
        - Model2.php
    - forms
        - Model1.php
        - AFormName.php
    - ui
        - Handler1.php
            - snippets
                - snippet1.html
                - snippet2.html
    - stylesheets
    - javascripts
    - img
    - ...

The main thing to notice and take away, is an intended nameing-relation between `/controllers`, `/models`, `/views` and maybe `/forms` tends to exist. All of which interoperate in fulfilling a request which is first mapped to a controller and goes from their depending on its logic.

The roles of *models*, *views* and *controllers* are fairly straight forward and follow the *MVC-paradigma* as a lmvc itself does not give you anything else.

Using the [module library](https://github.com/scandio/lmvc-modules) gives you, as noted at this readme's end, a lot more. Where *forms* should take over whenever a model needs validation. In addition, a *SnippetHandler* offers a unified way of accessing simple code-snippets (e.g. Html) - but first keep on reading an checkout the modules later!

## How To

You have to change the `.htaccess` file if you want to try it

```apache
RewriteRule ^(.*)$ /path/to/your/index.php?app-slug=$1 [L,QSA]
```

### Controllers and Actions

```
http://host/base-path/controller/action/param1/param2
```

The URL above shows the controller & action with theirs params

```
http://host/base-path/
```

is a special controller and a special action. In this case the controller is named Application and the action index()

```
http://host/base_path/xyz
```

Here the controller is Xyz and the action is index()

```
http://host/base_path/xyz/do
```

Again the controller is Xyz and the action is `do()`

To develop your own controller create a file with the same name like the class in the controllers directory e.g. `Accounts.php`

Create a class as a descendant of class Controller

```php
class Accounts extends \Scandio\lmvc\Controller { }
```

Create a public static method named like the action you want to call

```php
class Accounts extends \Scandio\lmvc\Controller {

    public static function index() {
        print_r('ok');
    }

}
```

Try to hit the url `http://host/base-path/accounts/`

### Rendering Views

Currently LMVC supports three rendering options. First is standard HTML rendering including a master template (falls back to `main.html`). Second is plain JSON output for e.g. handing ajax calls to the server. The last and third option is to render a template as plain html without its master template. This is useful whenever a client side requests want rendered html to inject it somewhere into his DOM.

The class Controller is having two static methods for that. All data that have to be passed to the template or to JSON must be set by setRenderArg().

#### Example for HTML rendering:

```php
class Accounts extends Controller {

    public static function index() {
        return self::render()
    }

}
```

This renders the view (template) registeredViewPaths/controller/action.html. In this case views/accounts/index.html. To pass some data to the template you can...

```php
class Accounts extends \Scandio\lmvc\Controller {

    public static function index() {
        self::setRenderArg('name', 'John Doe');

        return self::render()
    }

}
```

or

```php
class Accounts extends Controller {

    public static function index() {
        self::render(array('name' => 'John Doe'));
    }

}
```

or both. There is no specific template language. It's just PHP. Every render argument is accessible as a local variable.

```php
<h1>Hello <?= $name ?></h1>
```

#### Example for JSON rendering

```php
class Accounts extends \Scandio\lmvc\Controller {

    public static function index() {
        self::renderJson(array('name' => 'John Doe'));
    }

}
```

has the output: `{"name": "John Doe"}``

#### Example for HTML rendering

```php
class Accounts extends \Scandio\lmvc\Controller {

    public static function index() {
        self::renderHtml(
            "<h2>Sorry, you've been using from credentials!</h2>"
        );
    }

}
```

takes the html and pipes it as an output setting the httpCode and some http-headers for cache-control.

As a side not: if you want to render a plain template without layout with e.g. [pjax](https://github.com/defunkt/jquery-pjax) you can setup an empty master template and set that explicitly while calling the standard `render()`-function.

### Using Http-Verb Prefixes

Using the Http-Verb (GET, POST, DELETE, PUT and UPDATE) as a prefix for your apps' constroller can be greatly helpful.

This could result in a controller having the following methods:

```php
class Users extends \Scandio\lmvc\Controller {

    public static function getUser($id) {
        ...
    }

    public static function postUser($name, $email, $password) {
        ...
    }

    public static function deleteUser($id) {
        ...
    }

    public static function updateUser($id, $email, $password) {
        ...
    }

}
```

Anyhow, methods on the controller would not be invoked with a fully qualifying name such as `http://host/base-path/users/get-user/1`. Fireing a GET-Request to the url `http://host/base-path/users/user/1` would be sufficient as lmvc searches for a method by the name of `getUser` whenever it was unable to find the `user`-method.

## Integrating with lmvc-modules

LMVC comes with an own, not baked in, library of modules which you can use and extend at free will. Modules can integrate with view functionalities or hook themselves into an own controller namespace.
Feel free to check out the [existing set of modules](https://github.com/scandio/lmvc-modules).

**Now it's up to you, code along!**









