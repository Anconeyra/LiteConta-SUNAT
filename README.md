# 📊 MYPE SUNAT Lite  
### Sistema Web para Clasificación y Pre-registro Contable de MYPEs Peruanas

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Vite](https://img.shields.io/badge/Vite-5.x-646CFF?logo=vite&logoColor=white)](https://vitejs.dev/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-336791?logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

> **MYPE SUNAT Lite** es una herramienta web ligera que permite a micro y pequeñas empresas (MYPEs) del Perú realizar un **pre-registro contable automatizado** a partir de comprobantes electrónicos (facturas, boletas), facilitando el cumplimiento tributario ante la SUNAT y la generación de reportes compatibles con software contable.

---

## 🎯 Características principales

- ✅ **Subida y parseo automático** de comprobantes electrónicos (XML/ZIP)
- 🔍 **Clasificación inteligente** de ingresos y gastos según catálogo SUNAT
- 🛡️ **Validación básica de RUC** (estado activo/inactivo)
- 📥 **Exportación a CSV** compatible con Contpaqi, SIIGO y otros
- 📱 **Interfaz responsive** con Blade + Tailwind CSS (compilado con Vite)
- 🔐 Autenticación segura con Laravel Breeze
- ☁️ Listo para despliegue en AWS, Railway o cualquier entorno PHP

---

## 🛠️ Requisitos

- PHP 8.2 o superior
- Composer 2.5+
- Node.js 18+ y npm
- PostgreSQL 12+ (o MySQL si prefieres)
- Extensiones PHP: `xml`, `zip`, `pgsql` (o `pdo_mysql`)

---

## 🚀 Instalación local

Sigue estos pasos para ejecutar el proyecto en tu entorno de desarrollo:

### 1. Clonar el repositorio
```bash
git clone https://github.com/frankanconeyra/mype-sunat-lite.git
cd mype-sunat-lite
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de frontend y compilar assets
```bash
npm install
npm run build
```
Para desarrollo en vivo (hot-reload):
```bash
npm run dev
```

### 4. Configurar variables de entorno
```bash
cp .env.example .env
```
Edita el archivo `.env` y configura tu base de datos:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mype_sunat
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```
💡 Si usas MySQL, cambia a:
```
DB_CONNECTION=mysql
DB_PORT=3306
```

### 5. Generar clave de aplicación y preparar la base de datos
```bash
php artisan key:generate
php artisan migrate --seed
```

### 6. Iniciar servidor de desarrollo
```bash
php artisan serve
```
Accede a la aplicación en:  
👉 http://127.0.0.1:8000

---

## 🗂️ Estructura relevante del proyecto

```
app/
├── Models/               # Transaction.php, Category.php, ...
├── Http/Controllers/     # XMLParserController.php, ReportController.php, ...
resources/
├── views/                # Blade templates (layouts, components, livewire)
├── js/                   # app.js (punto de entrada Vite)
routes/
├── web.php               # Rutas principales
database/
├── seeders/              # CategorySeeder.php (códigos y tablas SUNAT)
```

---

## 🚢 Despliegue en producción

Pasos recomendados:
```bash
# Compilar assets optimizados
npm run build

# Configurar entorno producción
# (en .env o variables del servidor)
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# Optimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Configura tu servidor web (Nginx / Apache) para que apunte a la carpeta `public/`.

**Plataformas compatibles:**
- AWS EC2 + RDS
- Railway.app
- Render.com
- Laravel Forge
- DigitalOcean App Platform / Droplets
- VPS con Nginx + PHP-FPM

---

## 📝 Licencia

Este proyecto está licenciado bajo la MIT License – ver el archivo LICENSE para más detalles.  
© 2026 Frank Hernán Anconeyra Suyo  
Proyecto académico para optar al Título Profesional en Diseño y Desarrollo de Software – TECSUP

---

## 🤝 ¿Quieres contribuir?

¡Las contribuciones son bienvenidas!

1. Haz fork del repositorio
2. Crea tu rama:
    ```bash
    git checkout -b feature/nueva-funcionalidad
    ```
3. Realiza tus cambios y haz commit:
    ```bash
    git commit -m "Agrega nueva funcionalidad"
    ```
4. Haz push:
    ```bash
    git push origin feature/nueva-funcionalidad
    ```
5. Abre un Pull Request

---

## 📬 Contacto

¿Tienes dudas, sugerencias o quieres colaborar?

- 📧 anconeyrafsuyo@gmail.com
- 👤 [LinkedIn: Frank Hernán Anconeyra](www.linkedin.com/in/frank-anconeyra)

¡Gracias por tu interés en el proyecto!
