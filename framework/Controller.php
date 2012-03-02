<?php

abstract class Controller {
	
	static function setRenderArg($name, $value) {
		App::get()->setRenderArg($name, $value);
	}
	
	static function request($requestMethod=null) {
		if (!empty($requestMethod)) {
			$result = App::get()->request[strtoupper($requestMethod)];
		} else {
			$result = array_merge(App::get()->request['POST'], App::get()->request['GET']);
		}
		return $result;
	}
	
	private static function prepareRenderArgs($renderArgs) {
		if (!is_null($renderArgs)) {
			if (!is_array($renderArgs)) {
				$renderArgs = array($renderArgs);
			}
			App::get()->renderArgs = array_merge(App::get()->renderArgs, $renderArgs);
		}
	}
	
	static function renderJson($renderArgs=null) {
		self::prepareRenderArgs($renderArgs);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1964 07:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode(App::get()->renderArgs);
	}
	
	static function render($renderArgs=null) {
		self::prepareRenderArgs($renderArgs);
		extract(App::get()->renderArgs);
		$view = 'views/' . strtolower(App::get()->controller) . '/' . strtolower(App::get()->action) . '.html';
		include('views/app.html');
	}
	
	static function redirect($method) {
		$method = explode('::', $method);
		if ($method[0] == 'Application') {
			$controller = '/';
		} else {
			$controller = '/' . strtolower($method[0]) . '/';
		}
		if ($method[1] == 'index') {
			$action = '';
		} else {
			$action = $method[1];
		}
		header('Location: http://' . App::get()->host . App::get()->uri . $controller . $action );
		exit;
	}
	
	static function beforeAction() {
		
	}
	
	static function afterAction() {
		
	}
	
}