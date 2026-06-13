# Diagrama UML - Módulo de Reportes

## Diagrama de Clases

```
┌─────────────────┐
│   ReportController   │
├─────────────────┤
│ - filters: array     │
│ - users: Collection  │
├─────────────────┤
│ + index()       │
│ + export_csv()  │
└────────┬────────┘
         │ uses
         ▼
    ┌───────────┐
    │  User     │
    ├───────────┤
    │ - id: int │
    │ - name: string
    │ - email   │
    │ - tenant_id │
    ├───────────┤
    │ + roles() │
    │ + tenant()│
    └────┬──────┘
         │ belongs_to
         ▼
    ┌──────────┐
    │  Tenant  │
    ├──────────┤
    │ - id: int│
    │ - name   │
    └──────────┘

    ┌────────┐
    │  Role  │
    ├────────┤
    │ - id   │
    │ - name │
    └────────┘
         ▲
         │ many_to_many
         │
    ┌─────────────────┐
    │ model_has_role  │
    │ (pivot table)   │
    └─────────────────┘
         ▲
         │ belongs_to
         │
    ┌─────────────────┐
    │      User       │
    └─────────────────┘
```

## Diagrama de Secuencia - Consulta de Reporte

```
Usuario          Frontend         Backend         Database
  │                │                 │                │
  ├─ Click Reports─>│                 │                │
  │                 ├─ GET /reports ──>│                │
  │                 │   (JWT Token)    │                │
  │                 │   X-Tenant-ID    ├─ Query Users ─>│
  │                 │                  │<─ User List ──┤
  │                 │                  │                │
  │                 │  JSON Response  │                │
  │<─ Show Table ───┤<─ data[] ────────┤                │
  │                 │                  │                │
  ├─ Export CSV ──>│                 │                │
  │                 ├─ GET /reports?export=csv ──>    │
  │                 │                  ├─ Generate CSV─>│
  │<─ Download CSV ─┤<─ CSV Stream ────┤                │
```

## Diagrama de Casos de Uso - Módulo Reportes

```
                   ┌─────────────────┐
                   │   Usuario       │
                   │  Autenticado    │
                   └────────┬────────┘
                            │
                ┌───────────┼───────────┐
                │           │           │
                ▼           ▼           ▼
           ┌────────┐  ┌────────┐  ┌──────────┐
           │ Ver    │  │Filtrar │  │ Exportar │
           │Reporte │  │  por   │  │   CSV    │
           │Usuarios│  │ Rol    │  │          │
           └────────┘  └────────┘  └──────────┘
                │           │           │
                └───────────┼───────────┘
                            │
                ┌───────────┼────────────┐
                │           │            │
                ▼           ▼            ▼
            ┌────────┐ ┌──────────┐ ┌─────────┐
            │ Autent │ │Multitenancy│ JWT Auth│
            │-icación│ │ Validation  │ Token   │
            └────────┘ └──────────┘ └─────────┘
```

## Relaciones de Base de Datos

```
users
├── id (PK)
├── tenant_id (FK → tenants.id)
├── name
├── email
├── password
└── created_at

tenants
├── id (PK)
└── name

model_has_roles (pivot)
├── role_id (FK → roles.id)
├── model_id (FK → users.id) [referencia polimórfica]
└── model_type (string)

roles
├── id (PK)
└── name
```

## Flujo de Datos - Reporte con Filtros

```
Input (Query Params)
    │
    ├─ role: string (opcional)
    ├─ date_from: date (opcional)
    └─ date_to: date (opcional)
    │
    ▼
ReportController::index()
    │
    ├─ Obtener tenant del request
    ├─ Filtrar Users WHERE tenant_id
    ├─ Si role: filtrar por roles().name
    ├─ Si date_from: filtrar WHERE created_at >= date_from
    ├─ Si date_to: filtrar WHERE created_at <= date_to
    │
    ▼
    ├─ Si export=csv: Generar stream CSV
    │   └─ Headers: [id, name, email, roles, tenant, created_at]
    │
    └─ Si no: Retornar JSON { data: [...] }
    │
    ▼
Output
    ├─ CSV File (descarga)
    └─ JSON API Response
```

## Componentes Implementados

### Backend (PHP/Laravel)
- **ReportController**: Controlador API para reportes
  - `index($request)`: Procesa filtros y exportación
  
- **Middleware**:
  - `JwtAuth`: Valida token JWT
  - `TenantMiddleware`: Extrae tenant del header X-Tenant-ID

### Frontend (Vue 3/JavaScript)
- **ReportPage.vue**: Componente principal
  - Formulario de filtros
  - Tabla de resultados
  - Función exportación

- **Axios Plugin**: Interceptor para autenticación automática
  - Añade Authorization header
  - Añade X-Tenant-ID header

## Seguridad

```
Acceso a Reportes
    │
    ├─ Validar JWT Token (JwtAuth middleware)
    │
    ├─ Validar Tenant ID en header
    │
    ├─ Filtrar datos solo del tenant del usuario
    │   (WHERE users.tenant_id = auth()->user()->tenant_id)
    │
    └─ Retornar solo usuarios de ese tenant
```

---

**Última actualización:** Sprint 2, Ciclo 1
**Estado:** ✅ Completado y Documentado
