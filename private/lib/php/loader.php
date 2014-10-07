<?php

function loadFunc($funcName)
{
	if (!function_exists($funcName)) {
		require_once 'functions/' . $funcName . '.php';
	}

}

function loadClass($className)
{
	if (!class_exists($className, false)) {
		$classPath = str_replace('_', '/', $className);
		require_once 'classes/' . $classPath . '.php';
	}

}

spl_autoload_register('loadClass');

?>