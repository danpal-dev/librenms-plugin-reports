# Instalación y Persistencia — Plugin Reports

Requisitos, instalación y procedimiento para garantizar que el plugin **sobreviva a `daily.sh` / `git pull`** de LibreNMS.

---

## 1. Prerrequisitos del servidor

### 1.1 Extensiones PHP obligatorias

| Extensión | Propósito | Verificar |
|-----------|-----------|:---------:|
| **GD**      | Renderizado de fuentes TTF en `SimplePdfWriter` | ✅ |
| **mbstring**| Conteo UTF-8 en tablas PDF/XLSX | ✅ |
| **zip**     | Empaquetado OOXML de `SimpleXlsxWriter` | ✅ |
| **PDO MySQL**| Lectura `devices_attribs` y `device_outages` | ✅ |
| **JSON**    | Auditoría exportaciones | ✅ |

Validar:
```bash
php -m | grep -E "^(gd|mbstring|zip|pdo_mysql|json)$"
```
Si falta alguna:
```bash
sudo apt-get install -y php8.4-gd php8.4-mbstring php8.4-zip
sudo systemctl reload php8.4-fpm
```

### 1.2 Memoria PHP recomendada

Edita `/etc/php/8.4/fpm/conf.d/99-reports-custom.ini`:
```ini
memory_limit       = 512M   ; para PDFs grandes > 2000 filas
max_execution_time = 120    ; fetch RRD anual
post_max_size      = 64M
upload_max_filesize= 64M
```
Reinicia:
```bash
sudo systemctl restart php8.4-fpm
```

### 1.3 Binario `rrdtool`

El plugin usa `rrdtool xport --json` internamente (Page.php L1300). Debe estar en `$PATH`:
```bash
which rrdtool && rrdtool --version
```
LibreNMS lo instala por defecto; si no:
```bash
sudo apt-get install -y rrdtool
```

### 1.4 (Opcional) Sudoers para comandos rrdtool

Normalmente NO hace falta: PHP-FPM corre como `librenms` y `/opt/librenms/rrd/` pertenece a ese usuario. Si por algún motivo tienes un wrapper custom que requiere `root`:

```bash
sudo visudo -f /etc/sudoers.d/librenms-reports
```
Agrega (ajusta ruta si es diferente):
```
librenms ALL=(root) NOPASSWD: /usr/bin/rrdtool xport *
```

---

## 2. Instalación del plugin

### 2.1 Despliegue inicial (SOLO si no está en `app/Plugins/Reports/`)

```bash
cd /opt/librenms/app/Plugins
# Descarga o copia Reports/ aquí
chown -R librenms:librenms Reports/
```

### 2.2 Activar en BD (si aparece 404)

Si `/plugin/Reports` devuelve 404, es porque `PluginManager` lo desactivó tras un `TypeError` antiguo. Reactívalo:

```bash
cd /opt/librenms
php artisan tinker --execute="
DB::update('UPDATE plugins SET plugin_active = 1 WHERE plugin_name = ?', ['Reports']);
echo 'OK - Reports plugin_active = 1';
"
```

### 2.3 Registrar rutas (NO ES NECESARIO — sistema hooks)

El plugin usa el sistema `PageHook` / `SettingsHook` nativos de LibreNMS. **No modifiques** `routes/web.php`, `bootstrap/app.php` ni ningún archivo fuera de `app/Plugins/Reports/`. Se respeta AGENTS.md.

---

## 3. 🛡 PERSISTENCIA POST-UPGRADE (OBLIGATORIO)

> LibreNMS se actualiza con `daily.sh` que hace `git pull` en `/opt/librenms/.git`. Si el plugin no tiene su propio `.git`, **git checkout padre puede borrar archivos desconocidos**.

### 3.1 Verificar que Reports/ tiene `.git` independiente

```bash
ls -la /opt/librenms/app/Plugins/Reports/.git/
```
Si no existe → **inicializa sub-repositorio YA** (regla AGENTS.md):

```bash
cd /opt/librenms/app/Plugins/Reports
sudo -u librenms git init
sudo -u librenms git config user.name  "ReportsBot"
sudo -u librenms git config user.email "reports@local"
sudo -u librenms git add -A
sudo -u librenms git commit -m "release 1.2.0: 3x nullable hooks, Flowbite docs, renderer, persistencia"
```

> ✅ **Resultado**: `/opt/librenms/.git` (padre) ve `Reports/` como un directorio normal sin trackear. El `daily.sh / git pull` padre **no tocará** ninguno de los archivos de Reports.

### 3.2 Configurar `.gitignore` de Reports

Ya incluido. Contenido recomendado:
```
# Exports temporales
*.pdf
*.xlsx
*.csv
exports_tmp/
tmp/

# Logs auditoría (rotan, no versionar)
audit_log_*.log
*.log

# Entorno local
.env
.DS_Store
Thumbs.db

# IDE
.vscode/
.idea/
```

### 3.3 Backup cloud opcional (recomendado 1/mes)

Agrega un remote GitHub/GitLab privado:
```bash
cd /opt/librenms/app/Plugins/Reports
sudo -u librenms git remote add origin https://github.com/TU-ORG/librenms-plugin-reports.git
sudo -u librenms git branch -M main
sudo -u librenms git push -u origin main
```

Backup script `/etc/cron.monthly/librenms-backup-plugins`:
```bash
#!/bin/bash
set -e
for PLUGIN in FlowbiteTheme MigrateRrdRetention WebSSH Reports; do
  cd "/opt/librenms/app/Plugins/${PLUGIN}"
  sudo -u librenms git add -A 2>/dev/null || true
  sudo -u librenms git commit -m "monthly backup $(date +%F)" --allow-empty 2>/dev/null || true
  sudo -u librenms git push origin main 2>/dev/null || true
done
```
```bash
sudo chmod +x /etc/cron.monthly/librenms-backup-plugins
```

---

## 4. Recuperación post-borrado accidental

Si `daily.sh` padre borró Reports/ por algún motivo (raro, pero posible):

1. **Desde tu backup cloud**:
   ```bash
   cd /opt/librenms/app/Plugins
   sudo -u librenms git clone https://github.com/TU-ORG/librenms-plugin-reports.git Reports
   chown -R librenms:librenms Reports/
   ```
2. **Reactiva plugin si fuera necesario** (paso 2.2).
3. **Valida**: navega `/plugin/Reports` → 200 OK con pills `v1.2.0 ACTIVO`.

---

## 5. Checklist post-instalación

- [ ] Extensiones PHP `gd mbstring zip pdo_mysql` → `php -m`
- [ ] `memory_limit PHP >= 256M` (512M para PDF anual)
- [ ] `rrdtool` en `$PATH`
- [ ] Carpeta Reports pertenece a `librenms:librenms`
- [ ] Sub-repositorio `.git/` existe dentro de Reports
- [ ] BD `plugins.plugin_active` = 1 para Reports
- [ ] Navegador `/plugin/Reports` → 200 OK + pill `v1.2.0` visible
- [ ] Acordeón 4 pestañas (GUÍA / FAQ / INSTALACIÓN / ARQUITECTURA) renderiza tablas sin JS TypeError
- [ ] Ajustes `/plugin/Reports/edit` → permisos admin + panels gradient

---

## 6. Desinstalación limpia (si alguna vez hace falta)

```bash
cd /opt/librenms
# 1. Desactivar plugin (evita 404s durante borrado)
php artisan tinker --execute="
DB::update('UPDATE plugins SET plugin_active=0 WHERE plugin_name=?', ['Reports']);
"

# 2. Borrar carpeta plugin
rm -rf /opt/librenms/app/Plugins/Reports

# 3. (Opcional) Limpiar attributos de BD asociados
php artisan tinker --execute="
DB::delete(\"DELETE FROM devices_attribs WHERE attrib_type LIKE 'port_%_contract_bandwidth' OR attrib_type = 'device_sla_target'\");
DB::delete(\"DELETE FROM plugins WHERE plugin_name = 'Reports'\");
"

# 4. Limpiar log auditoría
rm -f storage/logs/enlaces_report_audit.log
```
