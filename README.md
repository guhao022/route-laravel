<a name="route-prefixing"></a>
Laravel5 Router
==========

laravel5的路由剥离出来用于其他框架

安装
------------

把golune/router加入composer.json文件的require里并运行 `composer update`.

        "require": "golune/router": "dev-master"

使用
-----

开始使用路由之前需要做的前要：

	require 'vendor/autoload.php';

	use Golune\Routing\Router;

	Router::bootstrap();

  和laravel路由一样使用：
	Route::get('/', function(){
		echo 'Hello world.';
	});


#### 基本的 get 路由

	Route::get('/', function(){
		return 'Hello World';
	});

#### 基本的 post 路由

	Route::post('foo/bar', function(){
		return 'Hello World';
	});

#### 其他路由基本和laravel一样了

	Route::match(array('GET', 'POST'), '/', function(){
		return 'Hello World';
	});


	Route::any('foo', function(){
		return 'Hello World';
	});


	Route::get('foo', array('https', function(){
		return 'Must be over HTTPS';
	}));


	Route::get('user/{id}', function($id){
		return 'User '.$id;
	});

	Route::get('user/{name?}', function($name = null){
		return $name;
	});


	Route::get('user/{name?}', function($name = 'John'){
		return $name;
	});


	Route::get('user/{name}', function($name){
		//
	})
	->where('name', '[A-Za-z]+');

	Route::get('user/{id}', function($id){
		//
	})
	->where('id', '[0-9]+');



	Route::get('user/{id}/{name}', function($id, $name){
		//
	})
	->where(array('id' => '[0-9]+', 'name' => '[a-z]+'))


	Route::pattern('id', '[0-9]+');

	Route::get('user/{id}', function($id){
		// Only called if {id} is numeric.
	});


	Route::filter('foo', function(){
		if (Route::input('id') == 1){
			//
		}
	});



	Route::filter('old', function(){
		if (Input::get('age') < 200){
			return Redirect::to('home');
		}
	});


	Route::get('user', array('before' => 'old', function(){
		return 'You are over 200 years old!';
	}));

	Route::get('user', array('before' => 'old', 'uses' => 'UserController@showProfile'));

	Route::get('user', array('before' => 'auth|old', function(){
		return 'You are authenticated and over 200 years old!';
	}));


	Route::get('user', array('before' => array('auth', 'old'), function(){
		return 'You are authenticated and over 200 years old!';
	}));

	Route::filter('age', function($route, $request, $value){
		//
	});

	Route::get('user', array('before' => 'age:200', function(){
		return 'Hello World';
	}));


	Route::filter('log', function($route, $request, $response){
		//
	});

	Route::filter('admin', function(){
		//
	});

	Route::when('admin/*', 'admin');


	Route::when('admin/*', 'admin', array('post'));



	Route::group(array('domain' => '{account}.myapp.com'), function(){

		Route::get('user/{id}', function($account, $id){
			//
		});

	});


	Route::group(array('prefix' => 'admin'), function(){

		Route::get('user', function(){
			//
		});

	});
