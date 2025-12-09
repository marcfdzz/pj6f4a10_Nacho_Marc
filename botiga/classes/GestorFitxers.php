<?php
class GestorFitxers {
    // Lee un archivo JSON y devuelve un array. Si falla, devuelve array vacio.
    public static function llegirTot($ruta) {
        if (!file_exists($ruta)) return [];
        $contingut = file_get_contents($ruta);
        return json_decode($contingut, true) ?? [];
    }

    // Guarda un array en un archivo JSON. Crea la carpeta si no existe.
    public static function guardarTot($ruta, $dades) {
        // Asegurar que la carpeta existe
        $carpeta = dirname($ruta);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        $json = json_encode($dades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($ruta, $json);
    }
}
?>
