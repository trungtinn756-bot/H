<?php
require_once 'database.php';

function pdo_execute($sql, ...$args)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare($sql);

        // Bind từng tham số theo đúng kiểu dữ liệu thực tế
        foreach ($args as $index => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($index + 1, $value, $paramType);
        }

        $stmt->execute();
        return $stmt;
    } catch (PDOException $e) {
        die("Error executing query: " . $e->getMessage());
    }
}