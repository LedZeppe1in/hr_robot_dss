<?php

/**
 * Class DBConnector - класс для взаимодействия с базой данных.
 */
class DBConnector
{
    // Настройки подключения к БД на сервере Yandex.Cloud
    const HR_ROBOT_HOST = 'rc1a-cxj2zyrrqtga2084.mdb.yandexcloud.net';
    const HR_ROBOT_PORT = 6432;
    const HR_ROBOT_USER_NAME = 'u-2';
    const HR_ROBOT_PASSWORD = 'MXh;dod_22892h_u3_4748@';
    const HR_ROBOT_DB_NAME = 'D1';

    /**
     * Подключение к БД.
     *
     * @return bool
     */
    public function connect()
    {
        return pg_connect(
            'host=' . self::HR_ROBOT_HOST .
            'dbname=' . self::HR_ROBOT_DB_NAME .
            'port=' . self::HR_ROBOT_PORT .
            'user=' . self::HR_ROBOT_USER_NAME .
            'password=' . self::HR_ROBOT_PASSWORD) or die("Не удалось открыть соединение с сервером базы данных!");
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
     * @return resource - выборка (строки) из таблицы "hrrobot_video_interview"
     */
    public function getVideoInterviews($connection)
    {
        // SQL-запрос
        $sql = 'SELECT *
            FROM hrrobot_video_interview,
            WHERE landmark_file_name IS NOT NULL';
        // Выполнение SQL-запроса
        $res = pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));

        return $res;
    }

    /**
     * Добавление новой записи в таблицу "hrrobot_advanced_landmark".
     *
     * @param $connection - соединение с БД
     * @param $fileName - название json-файла модифицированной цифровой маски (сохраняемого на Object Storage)
     * @param $videoInterviewId - идентификатор видеоинтервью (дочернего ключа, FK) из таблицы "hrrobot_video_interview")
     */
    public function insertAdvancedLandmark($connection, $fileName, $videoInterviewId)
    {
        // Получение текущего времени
        $currentTime = time();
        // SQL-запрос
        $sql = "INSERT INTO hrrobot_advanced_landmark (created_at, updated_at, file_name, video_interview_id) 
            VALUES ('$currentTime', '$currentTime', '$fileName', '$videoInterviewId')";
        // Выполнение SQL-запроса
        pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));
    }

    /**
     * Обновление таблицы "hrrobot_advanced_landmark".
     *
     * @param $connection - соединение с БД
     * @param $id - идентификатор (PK) записи о json-файле модифицированной маски (таблица "hrrobot_advanced_landmark")
     * @param $fileName - обновляемое значение для поля названия json-файла модифицированной цифровой маски (file_name)
     * @param $videoInterviewId - обновляемое значение для поля идентификатора видеоинтервью (дочернего ключа, FK)
     */
    public function updateAdvancedLandmark($connection, $id, $fileName, $videoInterviewId)
    {
        // Получение текущего времени
        $currentTime = time();
        // SQL-запрос
        $sql = "UPDATE hrrobot_advanced_landmark
            SET updated_at = '$currentTime', file_name = '$fileName', video_interview_id = '$videoInterviewId'
            WHERE id = '$id'";
        // Выполнение SQL-запроса
        pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));
    }
}