# Proyecto Tienda de Deportes - Estructura Reestructurada

## 📁 Nueva Estructura del Proyecto (Español)

```
tienda/
├── clases/              # Clases del modelo de negocio
│   ├── Cesta.php        # Gestión del carrito de compra
│   ├── Cliente.php      # Clase Cliente (hereda de Usuario)
│   ├── Pedido.php       # Gestión de pedidos (antes Comanda)
│   ├── Producto.php     # Modelo de producto
│   ├── Trabajador.php   # Clase Trabajador (hereda de Usuario)
│   └── Usuario.php      # Clase abstracta base para usuarios
├── gestion/             # Área de gestión (Admin)
│   ├── trabajadores/    # Gestión de empleados
│   ├── clientes/        # Gestión de clientes
│   ├── productos/       # Gestión de catálogo
│   └── pedidos/         # Gestión de órdenes
├── compra/              # Área de compra (Cliente)
│   └── area_clientes/   # Panel del cliente
├── config.php           # Configuración global
├── utilidades.php       # Funciones de ayuda
└── index.php            # Punto de entrada
```

## 🎯 Clases Implementadas

### **Usuario** (Abstracta)
Clase base para todos los usuarios.
- Propiedades protegidas para herencia.
- Métodos para hash y verificación de contraseñas.

### **Cliente**
Extiende de Usuario.
- Añade gestión de tarjeta de crédito (sin exponer datos sensibles).
- Tipo: 'cliente'.

### **Trabajador**
Extiende de Usuario.
- Añade roles ('admin', 'trabajador').
- Métodos para verificar permisos de administración.

### **Producto**
Modelo simple de producto con ID, nombre y precio.

### **Cesta**
Gestión del carrito de compras en memoria (array).
- Agregar/Eliminar productos.
- Calcular totales.

### **Pedido**
Representa una compra finalizada.
- Vincula usuario y productos.
- Preparado para generación de facturas PDF.

## 🚀 Cómo Empezar

1.  Revisar `config.php` para ajustar rutas si es necesario.
2.  Implementar la lógica de persistencia (guardar en archivos/BD) en las clases `Pedido` y `Usuario`.
3.  Desarrollar las interfaces HTML/PHP en las carpetas `gestion/` y `compra/`.

---
**Estructura lista para implementación según requerimientos.**
