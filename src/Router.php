<?php
namespace Golune\Routing;

use Illuminate\Container\Container;
use Illuminate\Support\ClassLoader;
use Illuminate\Support\Facades\Facade;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\AliasLoader;
class Router {

    protected static $container;

    protected static $bootstrapped = false;

    protected static $dispatched = false;

    protected static $aliases = array(
        'Router'          => 'Golune\Routing\Router',
        'App'             => 'Illuminate\Support\Facades\App',
        'Input'           => 'Illuminate\Support\Facades\Input',
        'Redirect'        => 'Illuminate\Support\Facades\Redirect',
        'Request'         => 'Illuminate\Support\Facades\Request',
        'Response'        => 'Illuminate\Support\Facades\Response',
        'Route'           => 'Illuminate\Support\Facades\Route',
        'URL'             => 'Illuminate\Support\Facades\URL'
    );
    /**
     * Create a new router instance.
     *
     * @return void
     */
    public function __construct(){
        $this->bootstrap();
    }
    public static function bootstrap(){
        if (static::$bootstrapped) return;
        require_once 'vendor/illuminate/support/Illuminate/Support/helpers.php';
        $basePath = str_finish(realpath(__DIR__ . '/..'), '/');
        $controllersDirectory = $basePath . 'Controllers';
        $modelsDirectory = $basePath . 'Models';
        ClassLoader::register();
        ClassLoader::addDirectories(array($controllersDirectory, $modelsDirectory));
        $app = new Container;
        static::$container = $app;
        Facade::setFacadeApplication($app);
        $app['app'] = $app;
        $app['env'] = 'production';
        Request::enableHttpMethodParameterOverride();
        $app['request'] = Request::createFromGlobals();
        with(new EventServiceProvider($app))->register();
        with(new RoutingServiceProvider($app))->register();
        foreach (static::$aliases as $alias => $class){
            class_alias($class, $alias);
        }
        if (file_exists($basePath . 'routes.php')){
            require_once $basePath . 'routes.php';
        }
        register_shutdown_function('Golune\Routing\Router::dispatch');
        static::$bootstrapped = true;
    }
    /**
     * 当前应用程序的请求调度
     *
     * @return \Illuminate\Http\Response
     */
    public static function dispatch(){
        if (static::$dispatched) return;
        
        $request = static::$container['request'];
        $response = static::$container['router']->dispatch($request);
        $response->send();
        static::$dispatched = true;
    }
    /**
     * 通过调用动态路由器实例
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters){
        return call_user_func_array(array(static::$container['router'], $method), $parameters);
    }
}