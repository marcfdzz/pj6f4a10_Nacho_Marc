<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Worker.php';

/**
 * DataRepository class
 * Acts as an in-memory database where objects are instantiated with 'new'.
 */
class DataRepository {
    private static $users = [];
    private static $workers = [];
    private static $initialized = false;

    public static function init() {
        if (self::$initialized) return;

        // Instantiate Users (Clients)
        self::$users[] = new User('client1', '1234', 'client1@example.com', 'Cliente Uno');
        self::$users[] = new User('client2', '1234', 'client2@example.com', 'Cliente Dos');

        // Instantiate Workers
        self::$workers[] = new Worker('admin', 'admin123', 'admin', 'Administrador Principal');
        self::$workers[] = new Worker('worker1', 'worker123', 'worker', 'Trabajador Uno');

        self::$initialized = true;
    }

    public static function getUsers() {
        self::init();
        return self::$users;
    }

    public static function getWorkers() {
        self::init();
        return self::$workers;
    }

    public static function findUserByUsername($username) {
        self::init();
        foreach (self::$users as $user) {
            if ($user->username === $username) {
                return $user;
            }
        }
        return null;
    }

    public static function findWorkerByUsername($username) {
        self::init();
        foreach (self::$workers as $worker) {
            if ($worker->username === $username) {
                return $worker;
            }
        }
        return null;
    }
}
?>
