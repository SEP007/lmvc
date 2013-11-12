<?php

namespace Scandio\lmvc;

use Scandio\lmvc\utils\string\StringUtils;
use Scandio\lmvc\modules\rendering\Renderer;

/**
 * Static class for each action controller
 *
 * Controller is able to render HTML and
 * creates an JSON Output. Empty pre processor
 * and post processor methods for further usage
 * are implemented.
 *
 * @author ckoch
 * @abstract
 */
abstract class Controller
{
    /**
     * @var array associative array of values for rendering
     */
    private static $renderArgs  = array();

    /**
     * Shorthand to the request data of the http request
     * self::request()->myVar == $_REQUEST['myVar']
     *
     * @static
     * @return object simple PHP object with all request data
     */
    public static function request()
    {
        return LVC::get()->request;
    }

    public static function renderEngine($engine, $renderArgs = array(), $templates = null, $httpCode = 200)
    {
        http_response_code($httpCode);

        $engine     = $engine != null ? strtolower($engine) : 'php'; # Could be config value
        $app        = LVC::get();
        $renderer   = Renderer::get($engine);

        $renderer->setState([
            'controller' => StringUtils::camelCaseTo($app->controller),
            'action'     => StringUtils::camelCaseTo($app->actionName),
            'appPath'    => $app->config->appPath
        ]);

        $renderer->render($renderArgs);

        return true;
    }

    /**
     * redirect from a controller action to another URL. It's possible to
     * use variables as dynamic URL parts. call it like
     * self::redirect('Application::index', $var); which redirects to
     * /app/path/controller/action/{$var}
     *
     * @static
     * @param string $method name of class and action in static syntax like Application::index without brackets
     * @param string|array $params optional one value or an array of values to enhance the generated URL
     * @return bool
     */
    public static function redirect($method, $params = null)
    {
        header('Location: ' . LVC::get()->url($method, $params));
        return true;
    }

    /**
     * Redirects browser to last visited page using the http_referer.
     * Do not call render when using back!
     *
     * Note:
     *  Watch out. Calling it n-times will get you in a loop: A->B->A->B.
     *  As the referer always points to the last page and does not
     *  maintain a stack of pages.
     *
     * @return bool being false if referer for redirection is not set
     */
    public static function back()
    {
        # Get the referer only if set
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;

        # redirect only if possible
        if ($url !== false) { header('Location: ' . $url); }

        # will only return is redirect did not work
        return true;
    }

    /**
     * override it if your controller need a pre processor method
     *
     * @static
     * @return bool
     */
    public static function preProcess()
    {
        return true;
    }

    /**
     * override it if your controller needs a post processor method
     *
     * @static
     * @return bool
     */
    public static function postProcess()
    {
        return true;
    }
}
