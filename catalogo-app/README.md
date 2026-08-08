# Catálogo cliente (React)

App del catálogo para clientes. Consume la API PHP en `../sistema/api/`.

## Desarrollo

```bash
cd catalogo-app
npm install
npm run dev
```

Abre: `http://localhost:5173/sistema/app/`

El proxy de Vite reenvía `/sistema/api` y `/sistema/assets` a producción.

## Build y despliegue (Hostinger)

```bash
npm run build
```

Copia el contenido de `catalogo-app/dist/` a `sistema/app/` en el hosting (incluye `.htaccess`).

URL final:

`https://multilicoreschapinero.com/sistema/app/?idCli=57XXXXXXXXXX`

## Flujo

1. Categorías / productos / promociones
2. Carrito local
3. Checkout (buscar o registrar cliente)
4. `POST /sistema/api/pedido.php` → WhatsApp

## API

| Endpoint | Método | Uso |
|----------|--------|-----|
| `/sistema/api/categorias.php` | GET | Listado categorías |
| `/sistema/api/productos.php` | GET | Productos paginados |
| `/sistema/api/promociones.php` | GET | Promos activas |
| `/sistema/api/sugerencias.php` | GET | Autocomplete |
| `/sistema/api/cliente.php` | GET/POST | Buscar / registrar |
| `/sistema/api/pedido.php` | POST | Crear pedido |
