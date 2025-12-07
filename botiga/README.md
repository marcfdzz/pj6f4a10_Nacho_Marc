Botiga – Aplicación de Gestión y Compra (minimal functional version)
This is a functional skeleton of the project described in the specification.
It uses file-based JSON storage and simple PHP pages (no external libs required).
Paths:
- gestio/ : admin/worker app
- compra/ : customer app
- data/ : contains JSON files (clients.json, workers.json, products.json, orders)
- productes_copia/ : public copy of products
Security notes:
- This example is for educational purposes. For production, follow the documentation in config/.



Hay que arreglar que cuando creas un usuario nuevo desde trabajadores en el apartado de clients.php, y luego vas a iniciar sesion, no deja.