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
     * Поиск записей в таблице "hrrobot_video_interview" с не пустым полем "video_file_name".
     *
     * @param $connection - соединение с БД
     * @return resource - выборка (строки) из таблицы "hrrobot_video_interview"
     */
    public function getVideoInterviews($connection)
    {
        // SQL-запрос
        $sql = 'SELECT *
            FROM hrrobot_video_interview,
            WHERE video_file_name IS NOT NULL';
        // Выполнение SQL-запроса
        $result = pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));

        return $result;
    }

    /**
     * Поиск записи в таблице "hrrobot_video_interview" по идентификатору.
     *
     * @param $connection - соединение с БД
     * @param $id - идентификатор видеоинтервью (PK)
     * @return resource - запись из таблицы "hrrobot_video_interview"
     */
    public function getVideoInterview($connection, $id)
    {
        // SQL-запрос
        $sql = "SELECT *
            FROM hrrobot_video_interview,
            WHERE id = '$id'";
        // Выполнение SQL-запроса
        $result = pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));

        return $result;
    }

    /**
     * Поиск записей в таблице "hrrobot_landmark" с не пустым полем "landmark_file_name".
     *
     * @param $connection - соединение с БД
     * @return resource - выборка (строки) из таблицы "hrrobot_landmark"
     */
    public function getLandmarks($connection)
    {
        // SQL-запрос
        $sql = 'SELECT *
            FROM hrrobot_landmark,
            WHERE landmark_file_name IS NOT NULL';
        // Выполнение SQL-запроса
        $result = pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));

        return $result;
    }

    /**
     * Поиск записи в таблице "hrrobot_landmark" по идентификатору.
     *
     * @param $connection - соединение с БД
     * @param $id - идентификатор видеоинтервью (PK)
     * @return resource - запись из таблицы "hrrobot_landmark"
     */
    public function getLandmark($connection, $id)
    {
        // SQL-запрос
        $sql = "SELECT *
            FROM hrrobot_landmark,
            WHERE id = '$id'";
        // Выполнение SQL-запроса
        $result = pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));

        return $result;
    }

    /**
     * Добавление новой записи в таблицу "Цифровая маска" (hrrobot_landmark).
     *
     * @param $connection - соединение с БД
     * @param $fileName - название json-файла с лицевыми точками сохраняемого на Object Storage
     * @param $description - описание цифровой маски
     * @param $videoInterviewId - идентификатор видеоинтервью (дочернего ключа, FK) из таблицы "hrrobot_video_interview")
     */
    public function insertLandmark($connection, $fileName, $description, $videoInterviewId)
    {
        // Получение текущего времени
        $currentTime = time();
        // SQL-запрос
        $sql = "INSERT INTO hrrobot_advanced_landmark (created_at, updated_at, file_name, description, 
                video_interview_id) 
            VALUES ('$currentTime', '$currentTime', '$fileName', '$description', '$videoInterviewId')";
        // Выполнение SQL-запроса
        pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));
    }

    /**
     * Обновление таблицы "hrrobot_landmark".
     *
     * @param $connection - соединение с БД
     * @param $id - идентификатор (PK) записи о json-файле модифицированной маски (таблица "hrrobot_landmark")
     * @param $fileName - название json-файла с лицевыми точками сохраняемого на Object Storage
     * @param $description - описание цифровой маски
     * @param $videoInterviewId - обновляемое значение для поля идентификатора видеоинтервью (дочернего ключа, FK)
     */
    public function updateLandmark($connection, $id, $fileName, $description, $videoInterviewId)
    {
        // Получение текущего времени
        $currentTime = time();
        // SQL-запрос
        $sql = "UPDATE hrrobot_advanced_landmark
            SET updated_at = '$currentTime', file_name = '$fileName', description = '$description', 
                video_interview_id = '$videoInterviewId'
            WHERE id = '$id'";
        // Выполнение SQL-запроса
        pg_query($connection, $sql) or die("Ошибка в запросе: " .
            iconv('UTF-8', 'CP1251', $sql) . " " . pg_last_error($connection));
    }
}