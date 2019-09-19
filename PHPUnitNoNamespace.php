<?php

class PHPUnit_Framework_TestCase extends PHPUnit\Framework\TestCase
{}

include 'DbUnit/autoload.php';

abstract class PHPUnit_DbUnit_TestCase extends PHPUnit\DbUnit\TestCase
{
    protected static $connection;
    protected static $pdo;

    public function getConnection()
    {
        if (self::$connection == null) {
            $config = static::getPDOConfig();
            self::$pdo = new PDO($config['dsn'], $config['username'], $config['passwd']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection = $this->createDefaultDBConnection(self::$pdo, $config['dbname']);
        }
        return self::$connection;
    }

    /**
     * return array(
     *     'dsn'      => '',
     *     'username' => '',
     *     'passwd'   => '',
     *     'dbname'   => ''
     * );
     */
    protected static function getPDOConfig()
    {
        throw new Exception('you should override this method in a subclass');
    }
}

abstract class PHPUnit_DbUnit_Mysql_TestCase extends PHPUnit_DbUnit_TestCase
{
    protected static $mysqlHost     = '127.0.0.1';
    protected static $mysqlPort     = 3307;
    protected static $mysqlDbname   = 'test';
    protected static $mysqlCharset  = 'utf8';
    protected static $mysqlUsername = 'root';
    protected static $mysqlPasswd   = '123456';

    protected static function getPDOConfig()
    {
        return array(
            'dsn'      => sprintf(
                            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                            static::$mysqlHost,
                            static::$mysqlPort,
                            static::$mysqlDbname,
                            static::$mysqlCharset
                          ),
            'username' => static::$mysqlUsername,
            'passwd'   => static::$mysqlPasswd,
            'dbname'   => static::$mysqlDbname
        );
    }
}
