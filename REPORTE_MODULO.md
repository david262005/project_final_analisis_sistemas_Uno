# Módulo de Reportes - Examen Final Análisis de Sistemas

Repositorio forkeado: https://github.com/david262005/project_final_analisis_sistemas_Uno

## Descripción

Módulo completo de reportes para el Sistema Hospitalario Integrado que permite:
- Listar usuarios filtrados por rol y rango de fechas
- Exportar datos a CSV
- Interfaz web interactiva con Vue 3
- API REST con autenticación JWT
- Soporte multitenancy

## Estructura

### Backend (Laravel)
- **Controlador:** `app/Http/Controllers/Api/V1/ReportController.php`
  - Método `index()`: Retorna usuarios con filtros opcionales (rol, fecha_desde, fecha_hasta)
  - Exportación CSV con stream
  - Relaciones precargadas (roles, tenant)

### Frontend (Vue 3)
- **Componente:** `resources/js/modules/reports/pages/ReportPage.vue`
  - Formulario de filtros
  - Tabla de resultados
  - Botón de exportación CSV
  - Axios interceptor con autenticación automática

### Rutas
- `GET /api/v1/reports` - Listar reportes (requiere JWT)
  - Query params: `role`, `date_from`, `date_to`, `export` (csv)

### Tests
- **Feature:** `tests/Feature/ReportTest.php` - 4 tests de integración
  - test_report_index_returns_users
  - test_report_index_filters_by_role
  - test_report_export_csv
  - test_report_requires_authentication

- **Unit:** `tests/Unit/ReportControllerTest.php` - Tests básicos de estructura

## Instalación y Ejecución

### 1. Dependencias
```bash
composer install
pnpm install
```

### 2. Configuración
```bash
# Copiar .env.example a .env
cp .env.example .env

# Generar key
php artisan key:generate

# Configurar database en .env
php artisan migrate --seed
```

### 3. Ejecutar localmente
```bash
# Terminal 1: Backend Laravel
php artisan serve

# Terminal 2: Frontend Vite
pnpm dev
```

### 4. Acceso
- Abrir `http://localhost:5173`
- Ir a `/login` y autenticarse
- Navegar a `/reports`

## Uso del Módulo

### Filtros en la UI
- **Fecha desde:** Filtra usuarios creados a partir de esta fecha
- **Fecha hasta:** Filtra usuarios creados hasta esta fecha
- **Rol:** Busca por nombre de rol exacto (ej: "Recepcionista", "Doctor")

### Exportar CSV
- Click en "Exportar CSV" descarga archivo con estructura:
  - id, name, email, roles, tenant, created_at

### API Direct
```bash
# Sin filtros
curl -H "Authorization: Bearer TOKEN" \
     -H "X-Tenant-ID: TENANT_ID" \
     http://localhost:8000/api/v1/reports

# Con filtros
curl -H "Authorization: Bearer TOKEN" \
     -H "X-Tenant-ID: TENANT_ID" \
     "http://localhost:8000/api/v1/reports?role=Doctor&date_from=2026-01-01&date_to=2026-12-31&export=csv"
```

## Tests

### Ejecutar todos los tests
```bash
php artisan test
```

### Ejecutar solo reportes
```bash
php artisan test tests/Feature/ReportTest.php
php artisan test tests/Unit/ReportControllerTest.php
```

## Sprints Implementados

### Sprint 1: Funcionalidad Base
- ✅ Controlador ReportController con filtros por rol y fecha
- ✅ Ruta API `/reports` con autenticación JWT
- ✅ Página Vue ReportPage con tabla y formulario

### Sprint 2: Mejoras y Testing
- ✅ Exportación CSV completa con headers correctos
- ✅ Tests Feature (4 casos) validando autenticación, filtros, exportación
- ✅ Tests Unit básicos
- ✅ README con documentación completa
- ✅ Commits ordenados y descriptivos

## Commits Realizados

Ver historial de commits en GitHub para evidencia clara de:
1. Creación de controlador y rutas
2. Implementación de frontend
3. Integración de filtros
4. Añadido exportación CSV
5. Tests y documentación

## Próximas Mejoras (Sugeridas)

- Paginación en listado de reportes
- Sorting por columnas
- Validación de permisos por rol
- Generación de reportes en PDF
- Gráficos estadísticos de usuarios por rol/tenant

## Autor

Entrega de examen final - Análisis de Sistemas 2026
