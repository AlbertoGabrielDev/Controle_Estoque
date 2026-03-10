# Módulos del Sistema (Feature Modules)

El sistema **Controle de Estoque** está subdividido funcionalmente. A continuación, se presenta la lista de los módulos principales y sus respectivas responsabilidades.

---

## 📦 Stock (Inventario)
Responsable por el control físico de los productos almacenados.
- **Alertas Visuales:** Controla la caducidad de los lotes de productos, señalando:
  - 🔴 Caducado.
  - 🟡 Próximo a caducar (en menos de 7 días).
  - 🟣 Por debajo del stock mínimo definido por el gestor.
- **Movimientos:** Registra entradas y salidas físicas del almacén o tienda.
- **Historial Auditable:** El sistema guarda vía *Cache* el historial de salidas (visible exclusivamente para el perfil de Administrador).

---

## 🛒 Purchases (Compras)
Gestiona el flujo completo de relación con los proveedores y la adquisición de nuevos insumos o productos.
- **Ciclo de Vida:** Requisición → Cotización → Orden de Compra (Purchase Order) → Recepción → Conferencia → Devolución.
- **Integración Financiera:** Cuando una compra es "Recibida/Efectivada", el módulo alimenta automáticamente a **Finance** enviando datos hacia Cuentas por Pagar.

---

## 💰 Finance (Financiero)
Donde el dinero entra y sale. Responsable del flujo de caja y la organización contable básica.
- **Centros de Costo (`CostCenters`):** Permite organizar los gastos jerárquicamente (Padre/Hijo) para comprender de dónde provienen las salidas de dinero (Ej: Administrativo > RRHH > Salarios).
- **Cuentas Contables (`AccountingAccounts`):** Categorización oficial de las finanzas.
- **Gastos (`Expenses`):** Lanzamientos de cuentas por pagar, ya estén integradas o no con las compras.

---

## 🏷️ Products (Productos)
El corazón comercial del sistema.
- **Registro General:** Productos (SKU, NCM, Código de Barras), Marcas (`Brands`) y Categorías (`Categories`).
- **Unidades de Medida (`MeasureUnits`):** Control de UN, KG, CX, PCT.
- **Activación:** Permite inactivar un producto o marca temporalmente. Los administradores pueden verlos; los vendedores y otros perfiles no.

---

## 🤝 Customers & Suppliers (Clientes y Proveedores)
Registros centralizados.
- **Proveedores (`Suppliers`):** Utilizados intensamente en el módulo de Compras.
- **Clientes (`Customers`):** Utilizados en el módulo de Ventas.

---

## 💼 Sales (Ventas)
Gestión de la salida de productos, generando ingresos.
- **TPV / Pedidos:** Interfaz rápida para efectuar ventas.
- **Tablas de Precios (`PriceTables`):** Permite tener distintos precios según el perfil del cliente (Mayorista, Minorista, VIP).
- **Integración de Inventario:** Al confirmar la venta, se dispara inmediatamente la reducción en el módulo de **Stock**.

---

## ⚙️ Admin & Settings
Gestión general del sistema en su totalidad.
- Controles de acceso, permisos de usuarios (RBAC simple con Jetstream/Sanctum).
- Configuraciones globales de la aplicación.

***

*(La arquitectura modular nos permite, en el futuro, extraer un módulo completo — como Finance — y convertirlo en un microservicio simplemente aislando las rutas de comunicación).*
