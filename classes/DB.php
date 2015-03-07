<?php
class DB
{
    private $dbh;
    private $class = 'stdClass';

    public function __construct()
    {
        try {
            require __DIR__ . '/../core/config.php';
            $dsn = 'mysql:dbname=' . $config['db']['db_name'] . ';host=' . $config['db']['db_host'];
            $this->dbh = new PDO($dsn, $config['db']['db_username'], $config['db']['db_password']);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $view = new View();
            $view->error = 'Не удалось подключиться к базе данных: ' . $e->getMessage();
            $view->display('errors/403.php');
            die;
        }
    }

    public function setClassName($class)
    {
        $this->class = $class;
    }

    public function query($sql, $params = [])
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
        } catch (PDOException $e) {
            $view = new View();
            $view->error = 'Ошибка запроса: ' . $e->getMessage();
            $view->display('errors/403.php');
            die;
        }

        return $sth->fetchAll(PDO::FETCH_CLASS, $this->class);
    }

    public function execute($sql, $params = [])
    {
        $sth = $this->dbh->prepare($sql);
        return $sth->execute($params);
    }

    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}