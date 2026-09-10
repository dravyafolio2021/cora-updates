# Cora Local Test Environment Credentials (`http://cora.local`)

This document contains pre-configured test credentials for accessing local workspace builds across all supported business industry verticals.

---

## 🔑 Accounts Directory

| Workspace & Role | Username / Email | Password | Direct Local URL |
| :--- | :--- | :--- | :--- |
| 🏡 **Real Estate Workspace Owner** | `re_owner`<br>`owner.realestate@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=real_estate](http://cora.local/workspace/dashboard?industry=real_estate) |
| 📸 **Photography Studio Workspace Owner** | `studio_owner`<br>`owner.studio@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=photography_studio](http://cora.local/workspace/dashboard?industry=photography_studio) |
| 📈 **Marketing Agency Workspace Owner** | `marketing_owner`<br>`owner.marketing@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=marketing_agency](http://cora.local/workspace/dashboard?industry=marketing_agency) |
| 👑 **Platform Super Admin** | `cora_admin`<br>`admin@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard](http://cora.local/workspace/dashboard) |

---

## 🛠️ Management & Provisioning Commands

- **Login Portal**: `http://cora.local/workspace/login` or `http://cora.local/wp-login.php`
- **Re-provision Accounts Script**:
  ```bash
  php scripts/setup_local_accounts.php
  ```
- **Instant Industry URL Query Param**:
  Append `?industry=real_estate`, `?industry=photography_studio`, or `?industry=marketing_agency` to any workspace page URL while logged in to toggle industry mode instantly on your local build.
