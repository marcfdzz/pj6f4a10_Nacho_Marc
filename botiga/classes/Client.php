<?php
/**
 * Client class – represents a client loaded from a JSON file.
 *
 * Expected JSON structure (example):
 * {
 *   "id": 1,
 *   "name": "Acme Corp",
 *   "email": "contact@acme.com",
 *   "address": "123 Main St",
 *   "phone": "555-1234"
 * }
 */
class Client {
    /** @var array Holds the raw data loaded from JSON */
    private $data = [];

    /**
     * Constructor – optionally pass an associative array.
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * Load a client from a JSON file.
     * @param string $jsonFilePath Absolute path to the JSON file.
     * @return self|null Returns a Client instance or null on failure.
     */
    public static function loadFromFile(string $jsonFilePath): ?self {
        if (!file_exists($jsonFilePath)) {
            trigger_error("Client JSON file not found: $jsonFilePath", E_USER_WARNING);
            return null;
        }
        $json = file_get_contents($jsonFilePath);
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            trigger_error('Invalid JSON in client file: ' . json_last_error_msg(), E_USER_WARNING);
            return null;
        }
        return new self($data);
    }

    /**
     * Magic getter to access data fields as properties.
     */
    public function __get(string $name) {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic setter to modify data fields.
     */
    public function __set(string $name, $value): void {
        $this->data[$name] = $value;
    }

    /**
     * Export the client data back to an associative array.
     * @return array
     */
    public function toArray(): array {
        return $this->data;
    }

    /**
     * Save the current client data back to its JSON file.
     * @param string $jsonFilePath Path where the JSON should be saved.
     * @return bool Success status.
     */
    public function saveToFile(string $jsonFilePath): bool {
        $json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            trigger_error('Failed to encode client data to JSON.', E_USER_WARNING);
            return false;
        }
        return file_put_contents($jsonFilePath, $json) !== false;
    }
}
?>
