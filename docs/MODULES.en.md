# System Modules (Feature Modules)

The **Inventory Management System** is functionally subdivided. Below is the list of the main modules and their respective responsibilities.

---

## 📦 Stock
Responsible for the physical control of stored products.
- **Visual Alerts:** Controls the expiration of product batches, signaling:
  - 🔴 Expired.
  - 🟡 Nearing expiration (less than 7 days).
  - 🟣 Below the minimum stock defined by the manager.
- **Movements:** Registers physical entries and exits from the warehouse or store.
- **Auditable History:** The system saves the exit history via *Cache* (exclusive to the Admin profile).

---

## 🛒 Purchases
Manages the complete workflow of the supplier relationship and the acquisition of new supplies or products.
- **Lifecycle:** Requisition → Quotation → Purchase Order → Receipt → Conference → Return.
- **Financial Integration:** When a purchase is "Received/Effectuated," the module automatically feeds the **Finance** module, sending data to Accounts Payable.

---

## 💰 Finance
Where money goes in and out. Responsible for cash flow and basic accounting organization.
- **Cost Centers (`CostCenters`):** Allows organizing expenses hierarchically (Parent/Child) to understand where expenditures come from (E.g., Administrative > HR > Salaries).
- **Accounting Accounts (`AccountingAccounts`):** Official categorization of finances.
- **Expenses (`Expenses`):** Accounts payable entries, integrated or not with purchases.

---

## 🏷️ Products
The commercial heart of the system.
- **General Registration:** Products (SKU, NCM, Barcode), Brands (`Brands`), and Categories (`Categories`).
- **Units of Measure (`MeasureUnits`):** Control of UN, KG, CX, PCT.
- **Activation:** Allows temporarily inactivating a product/brand. Administrators can see them; salespeople cannot.

---

## 🤝 Customers & Suppliers
Centralized registries.
- **Suppliers (`Suppliers`):** Intensely used in the Purchases module.
- **Customers (`Customers`):** Used in the Sales module.

---

## 💼 Sales
Management of outgoing products generating revenue.
- **POS / Orders:** Fast interface for sales.
- **Price Tables (`PriceTables`):** Allows having different prices per customer profile (Wholesale, Retail, VIP).
- **Stock Integration:** Confirming the sale immediately triggers the deduction in the **Stock** module.

---

## ⚙️ Admin & Settings
General system management.
- Access controls, user permissions (simple RBAC with Jetstream/Sanctum).
- Global system settings.

***

*(The modular architecture allows us, in the future, to extract an entire module — like Finance — and convert it into a microservice just by isolating the communication routes).*
