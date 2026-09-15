# Cora Local Test Environment Credentials (`http://cora.local`)

This document contains pre-configured test credentials for accessing local workspace builds across all supported business industry verticals.

---

## 🔑 Accounts Directory

| Workspace & Role | Username / Email | Password | Direct Local URL |
| :--- | :--- | :--- | :--- |
| 🏡 **Real Estate Workspace Owner** | `re_owner`<br>`owner.realestate@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=real_estate](http://cora.local/workspace/dashboard?industry=real_estate) |
| 📸 **Photography Studio Workspace Owner** | `studio_owner`<br>`owner.studio@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=photography_studio](http://cora.local/workspace/dashboard?industry=photography_studio) |
| 📈 **Marketing Agency Workspace Owner** | `marketing_owner`<br>`owner.marketing@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=marketing_agency](http://cora.local/workspace/dashboard?industry=marketing_agency) |
| 🏭 **Stationery Manufacturing Owner** | `stationery_owner`<br>`owner.stationery@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=stationery_inventory](http://cora.local/workspace/dashboard?industry=stationery_inventory) |
| 🚚 **Field Sales Van Driver** | `driver_rohan`<br>`driver.rohan@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor](http://cora.local/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor) |
| 👑 **Platform Super Admin** | `cora_admin`<br>`admin@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard](http://cora.local/workspace/dashboard) |

---

## 🛠️ Management & Provisioning Commands

- **Login Portal**: `http://cora.local/workspace/login` or `http://cora.local/wp-login.php`
- **Re-provision Accounts Script**:
  ```bash
  php scripts/setup_local_accounts.php
  ```
- **Instant Industry URL Query Param**:
  Append `?industry=real_estate`, `?industry=photography_studio`, `?industry=marketing_agency`, or `?industry=stationery_inventory` to any workspace page URL while logged in to toggle industry mode instantly on your local build. Field Sales Driver terminal can be accessed directly via `?industry=stationery_inventory&subpage=plant_inventory&mode=vendor`.
