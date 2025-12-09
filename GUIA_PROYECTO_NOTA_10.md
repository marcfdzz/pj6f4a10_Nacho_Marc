# GUÍA TÉCNICA MINIMALISTA: PROYECTO PHP (SIN BASE DE DATOS)

**OBJETIVO:** Implementar el "Projecte Grupal 1" utilizando **únicamente** las tecnologías y estructuras más sencillas permitidas por la documentación oficial.
**REGLA DE ORO:** Si algo no es obligatorio en los PDFs, no se implementa. Si hay una forma compleja y una simple, se usa la simple.

---

## 1. TECNOLOGÍAS BASE (Simplificadas)

* **Lenguaje:** PHP 8.x.
* **Servidor:** Apache (XAMPP/MAMP).
* **Almacenamiento:** **JSON** para todo.
    * *Por qué:* `json_encode` y `json_decode` son nativos y simples.
* **Frontend:** HTML5 básico + CSS (sin frameworks JS).
* **Idioma:** **CATALÁN** o **ESPAÑOL**. Las variables DEBEN ser en este idioma (ej: `$usuari`, `$producte`). **Prohibido inglés** en nombres de variables/funciones.

---

## 2. ESTRUCTURA DE ARCHIVOS (Literal y Estricta)

Estructura exacta según enunciado:

```text
botiga/
├── gestio/                  # Aplicación interna
│   ├── app/                 # PHP de gestión
│   ├── clients/             # clients.json
│   ├── products/            # products.json original
│   ├── treballadors/        # treballadors.json
│   └── comandes_gestionades/# Pedidos procesados
├── compra/                  # Aplicación pública
│   ├── app/                 # PHP de compra
│   └── area_clients/        # Datos usuarios
│       └── [nombre_usuario]/
│           ├── cistella     # Carrito
│           ├── dades        # JSON datos
│           └── comanda_...  # Pedidos
├── productes_copia/         # Copia para lectura pública
└── comandes_copia/          # Pedidos pendientes de procesar
```

---

## 3. MANEJO DE DATOS (JSON + POO Básica)

La rúbrica exige POO. Cumpliremos con lo mínimo estricto.

### 3.1. Leer y Escribir (Helper)
Usar una clase estática simple (`FileManager`) para no repetir código.
```php
class FileManager {
    public static function readJson($path) {
        return json_decode(file_get_contents($path), true) ?? [];
    }
    public static function saveJson($path, $data) {
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }
}
```

### 3.2. Clases (Entidades)
Solo las necesarias.
*   **Clase Base:** `User` (propiedades: `nomUsuari`, `contrasenya`, `rol`).
*   **Hijas:** `Client` y `Treballador` (heredan de `User`).
*   **Clase:** `Producte`.
*   **Interfaz:** `interface ISerializable { public function toArray(); }`.
*   **Métodos:** Solo `__construct`, `getters`, `setters`, y `toArray`. Nada de lógica de negocio compleja dentro de las clases.

---

## 4. FUNCIONALIDAD GESTIÓ (Back-office)

### Gestión de Fichas (CRUD)
*   **Listar:** Tabla HTML simple recorriendo el array del JSON.
*   **Crear/Editar:** Formulario POST. Al recibir datos:
    1.  Leer JSON.
    2.  Buscar por ID (hacer bucle `foreach`).
    3.  Si existe, actualizar; si no, añadir al array.
    4.  Guardar JSON.
*   **Borrar:** Formulario POST con `<input type="hidden" name="_method" value="DELETE">`. Filtrar array y guardar.

### Sincronización Productos
*   Al guardar un producto en `botiga/gestio/productes/`, guardar **inmediatamente** una copia exacta en `botiga/productes_copia/`. Así la web de compra siempre ve lo actualizado.

### Comandes
*   **Ver:** `scandir('botiga/comandes_copia')` para listar archivos.
*   **Procesar:** `rename($ruta_origen, $ruta_destino)` para mover de `comandes_copia` a `comandes_gestionades`.

---

## 5. FUNCIONALIDAD COMPRA (Front-office)

### Sesión
*   Validar usuario contra `clients.json`.
*   Guardar `$_SESSION['usuario']`.

### Catálogo
*   Leer `botiga/productes_copia/productes.json`.
*   Mostrar con HTML simple (`div` o `table`).

### Cistella (Carrito)
*   Guardar en fichero: `botiga/compra/area_clients/[usuario]/cistella`.
*   Formato simple: Array de objetos/arrays `['id_producto' => 1, 'cantidad' => 2]`.

### Finalizar Compra
1.  Leer cistella.
2.  Crear contenido del pedido (texto o JSON).
3.  Nombre de archivo: `pedido_[timestamp]_[md5].json`.
4.  Guardar en dos sitios:
    *   `botiga/compra/area_clients/[usuario]/`
    *   `botiga/comandes_copia/`
5.  `unlink()` del fichero cistella.

---

## 6. LIBRERÍAS (Solo las obligatorias)

*   **DomPDF:** Solo para generar el PDF cuando se pulsa el botón "PDF". No guardar el PDF en disco si no es necesario, solo forzar descarga (`stream`).
*   **PHPMailer:** Configuración básica con SMTP (ej. Gmail). Función simple `enviarCorreo($destinatario, $asunto, $body)`.

---

## 7. SEGURIDAD "LOW COST"

*   **Protección Archivos:** `.htaccess` con `Deny from all` en carpetas que no sean `app` o `css`.
*   **Redirección:** Si `$_SERVER['HTTPS']` no está on, redirigir.
*   **Contraseñas:** `password_hash()` y `password_verify()`. No inventar algoritmos propios.

---

**RESUMEN:**
*   Si se puede hacer con un `foreach`, no uses funciones complejas de array.
*   Si se puede guardar en un solo JSON, no uses carpetas complicadas (salvo donde se exige).
*   Mantén el código PHP mezclado con HTML si separar lógica complica, pero intenta mantener la lógica arriba del archivo y la vista abajo.
