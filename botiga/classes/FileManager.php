<?php
class FileManager {
    public static function readJson($filePath) {
        if (!file_exists($filePath)) return [];
        $content = file_get_contents($filePath);
        return json_decode($content, true) ?? [];
    }

    public static function saveJson($filePath, $data) {
        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filePath, $json);
    }
}
?>
