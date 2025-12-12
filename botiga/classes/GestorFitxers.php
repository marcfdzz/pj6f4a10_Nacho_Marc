<?php
class GestorFitxers {
    public static function llegirTot($ruta) {
        if (!file_exists($ruta)) {
            return [];
        }
        $contingut = file_get_contents($ruta);
        $result = json_decode($contingut, true);
        return $result ?? [];
    }

    public static function guardarTot($ruta, $dades) {
        $carpeta = dirname($ruta);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        $json = json_encode($dades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($ruta, $json);
    }
}
?>
