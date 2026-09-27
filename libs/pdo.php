<?php
require_once 'database.php';
function pdo_execute($sql, ...$args)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    } catch (PDOException $e) {
        die("Error executing query: " . $e->getMessage());
    }
}
function pdo_insert($sql, ...$args)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($args);
        return $db->lastInsertId();
    } catch (PDOException $e) {
        die("Error executing query: " . $e->getMessage());
    }
}
function pdo_getAll($sql, ...$args)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Error executing query: " . $e->getMessage());
    }
}
function pdo_getOne($sql, ...$args)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetch();
    } catch (PDOException $e) {
        die("Error executing query: " . $e->getMessage());
    }
}
function pdo_getValue($sql, ...$args)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        die("Error executing query: " . $e->getMessage());
    }
}
?>