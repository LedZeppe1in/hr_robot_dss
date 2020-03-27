<?php

/**
 * Class DBConnector - класс для взаимодействия с базой данных.
 */
class DBConnector
{
    // Настройки подключения к БД на сервере Yandex.Cloud
    protected $host = 'rc1a-cxj2zyrrqtga2084.mdb.yandexcloud.net';
    protected $port = 6432;
    protected $userName = 'u-2';
    protected $password = 'MXh;dod_22892h_u3_4748@';
    protected $dbName = 'D1';

    /**
     * Подключение к БД.
     *
     * @return bool
     */
    public function connect()
    {
        return pg_connect("
            host=$this->host
            dbname=$this->dbName
            port=$this->port 
            user=$this->userName
            password=$this->password") or die("Не удалось открыть соединение с сервером базы данных!");
    }

    /**
     * Закрытие подключения к БД.
     *
     * @param $connection - соединение с БД
     */
    public function close($connection)
    {
        if (!pg_close($connection))
            echo("Не удалось завершить соединение с базой!");
    }

    /**
     * Выборка записей из таблицы "hrrobot_video_interview" с не пустым полем "landmark_file_name".
     *
     * @param $connection - соединение с БД
     * @return resource - выборка из таблицы "hrrobot_video_interview"
     */
    public function getVideoInterviews($connection)
    {
        // SQL-запрос
        $sql = 'SELECT *
            FROM hrrobot_video_interview,
            WHERE landmark_file_name IS NOT NULL';
        // Выполнение SQL-запроса
        $res = pg_query($connection, $sql) or die("Ошибка в запросе: " . iconv('UTF-8', 'CP1251', $sql) . " " .
            pg_last_error($connection));

        return $res;
    }

    /**
     * Обновление таблицы "hrrobot_video_interview".
     *
     * @param $connection - соединение с БД
     * @param $value - обновляемое значение для поля "advanced_landmark_file_name"
     */
    public function updateVideoInterview($connection, $value)
    {
        // SQL-запрос
        $sql = 'UPDATE hrrobot_video_interview
            SET advanced_landmark_file_name = ' . $value;
        // Выполнение SQL-запроса
        pg_query($connection, $sql) or die("Ошибка в запросе: " . iconv('UTF-8', 'CP1251', $sql) . " " .
            pg_last_error($connection));
    }
}