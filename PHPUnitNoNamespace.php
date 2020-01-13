<?php

class PHPUnit_Framework_TestCase extends PHPUnit\Framework\TestCase
{
    public function getMock($class)
    {
        return $this->createMock($class);
    }
}

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

    public function getMock($class)
    {
        return $this->createMock($class);
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

abstract class PHPUnit_DbUnit_Mysql_Zend_TestCase extends PHPUnit_DbUnit_Mysql_TestCase
{
    protected static $db;

    public static function getDb()
    {
        if (self::$db == null) {
            self::$db = new Zend_Db_Adapter_Pdo_Mysql(array(
                'host'     => static::$mysqlHost,
                'username' => static::$mysqlUsername,
                'password' => static::$mysqlPasswd,
                'port'     => static::$mysqlPort,
                'dbname'   => static::$mysqlDbname,
                'charset'  => static::$mysqlCharset
            ));
            Zend_Db_Table_Abstract::setDefaultAdapter(self::$db);
        }
        return self::$db;
    }

    public function getConnection()
    {
        if (self::$connection == null) {
            self::$pdo = self::getDb()->getConnection();
            self::$connection = $this->createDefaultDBConnection(self::$pdo, static::$mysqlDbname);
        }
        return self::$connection;
    }
}

class PHPUnit_DbUnit_DataSet_FilterDataSet extends PHPUnit\DbUnit\DataSet\Filter
{}

class PHPUnit_DbUnit_DataSet_QueryDataSet extends PHPUnit\DbUnit\DataSet\QueryDataSet
{}
