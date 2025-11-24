<?php
class Conexao {

    private static $host = "localhost";
    private static $usuario = "root";
    private static $senha = "";
    private static $banco = "teste";

    private static $conn = null;


    private function __construct() {}

    public static function get() {
        if (self::$conn === null) {

            self::$conn = new mysqli(
                self::$host,
                self::$usuario,
                self::$senha,
                self::$banco
            );

            if (self::$conn->connect_error) {
                die("Erro de conexão: " . self::$conn->connect_error);
            }

            self::$conn->set_charset("utf8mb4");
        }

        return self::$conn;
    }

  
    public static function select($sql, $params = []) {
        $conn = self::get();

        $stmt = $conn->prepare($sql);
        if ($params) {
            self::bind($stmt, $params);
        }
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function execute($sql, $params = []) {
        $conn = self::get();

        $stmt = $conn->prepare($sql);
        if ($params) {
            self::bind($stmt, $params);
        }
        return $stmt->execute();
    }

    private static function bind($stmt, $params) {
        $tipos = "";

        foreach ($params as $p) {
            if (is_int($p))        $tipos .= "i";
            elseif (is_double($p)) $tipos .= "d";
            else                   $tipos .= "s";
        }

        $stmt->bind_param($tipos, ...$params);
    }
}
