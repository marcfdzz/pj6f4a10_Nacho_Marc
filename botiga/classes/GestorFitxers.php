<?php
class GestorFitxers {
    // Llegeix un fitxer JSON i retorna un array. Si falla, retorna array buit.
    public static function llegirTot($ruta) {
        if (!file_exists($ruta)) return [];
        $contingut = file_get_contents($ruta);
        return json_decode($contingut, true) ?? [];
    }

    // Guarda un array en un fitxer JSON. Crea la carpeta si no existeix.
    public static function guardarTot($ruta, $dades) {
        // Assegurar que la carpeta existeix
        $carpeta = dirname($ruta);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        $json = json_encode($dades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($ruta, $json);
    }
}
?>
