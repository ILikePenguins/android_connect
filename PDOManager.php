<?php
require_once('db_config.php');

class PDOManager
{
	private static $instance;
	private static $dbh;

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	protected function __construct()
	{
		$options = array(
			PDO::ATTR_PERSISTENT => true, 
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		self::$dbh = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', DB_SERVER, DB_DATABASE), DB_USER, DB_PASSWORD, $options);
	}

	/**
	 * Private clone method to prevent cloning of the instance of the
	 * *Singleton* instance.
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Private unserialize method to prevent unserializing of the *Singleton*
	 * instance.
	 *
	 * @return void
	 */
	private function __wakeup()
	{
	}

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}

	/**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
	public static function getPDO()
	{
		self::getInstance();
		return self::$dbh;
	}

	/**
	 * Function to close db connection
	 */
	public static function close() {
		$self::$dhb = NULL;
		$self::$instance = NULL;
	}

}