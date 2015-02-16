<pre>
<?php
require_once('common.php');
require_once('PDOManager.php');

// Verify our URLs follow the entity/action URL pattern
$path_arguments = explode('/', get_request_path());
if (count($path_arguments) > 2) {
	json_encode('Too many slashes in the URL.');
}

// Expand the requested path into a class name and method call
list($class, $method) = $path_arguments;
// We want to convert underscores to CamelCase:
// 1. Replace all underscores for a space
// 2. change strings like "title case" to "Title Case"
// 3. Then remove all spaces we added
$uppercaseClass = str_replace(' ', '', ucwords(str_replace('_', ' ', $class)));
require_once($uppercaseClass . '.php');
$handler = new $uppercaseClass;

#$error = $handler->validate($method);
$error = NULL;
if ($error != NULL)
{
	echo json_encode($error);
	die();
}
//return jason encoded queries
echo json_encode($handler->$method());