# Q&A Community System Setup Guide

## Overview

This document describes the Q&A (Stack Overflow-style) system implementation for the MetaHogar Comunidad section. The system reuses the existing `incidencia` table from the project database and adds minimal supporting tables.

## Database Schema

### Architecture Decision

Instead of creating a separate parallel schema, the system leverages the existing `incidencia` table:
- **incidencia**: Serves as Questions/Issues (already exists in project)
- **respuesta_incidencia**: Responses to questions (NEW)
- **voto_incidencia**: Voting on questions (NEW)
- **voto_respuesta_incidencia**: Voting on responses (NEW)

### Tables

#### 1. `incidencia` (Extended)
**Existing table with added columns:**
- `Vistas` INT - Number of views (added)
- `Puntos` INT - Points/reputation from votes (added)
- `RespuestaAceptada_idRespuestaIncidencia` INT - Foreign key to accepted response (added)

#### 2. `respuesta_incidencia` (NEW)
```sql
CREATE TABLE `respuesta_incidencia` (
  `idRespuestaIncidencia` INT PRIMARY KEY AUTO_INCREMENT,
  `Incidencia_idIncidencia` INT NOT NULL FK -> incidencia,
  `Usuario_idUsuario` INT NOT NULL FK -> usuario,
  `Cuerpo` LONGTEXT NOT NULL,
  `Puntos` INT DEFAULT 0,
  `Aceptada` TINYINT(1) DEFAULT 0,
  `FechaRegistro` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `FechaActualizacion` DATETIME ON UPDATE CURRENT_TIMESTAMP
)
```

#### 3. `voto_incidencia` (NEW)
```sql
CREATE TABLE `voto_incidencia` (
  PRIMARY KEY (`Incidencia_idIncidencia`, `Usuario_idUsuario`),
  `Valor` TINYINT(2) -- -1 or 1
)
```

#### 4. `voto_respuesta_incidencia` (NEW)
```sql
CREATE TABLE `voto_respuesta_incidencia` (
  PRIMARY KEY (`RespuestaIncidencia_idRespuestaIncidencia`, `Usuario_idUsuario`),
  `Valor` TINYINT(2) -- -1 or 1
)
```

## Installation

### 1. Import Database Schema

```bash
# From MySQL CLI or PHPMyAdmin
mysql -u username -p database_name < comunidad_schema.sql
```

Or execute the SQL file contents directly in PHPMyAdmin.

### 2. Verify Tables Created

```sql
SHOW TABLES LIKE '%respuesta%';
SHOW TABLES LIKE '%voto%';
DESCRIBE incidencia;
```

## File Structure

```
estadia/
├── app/
│   ├── controllers/
│   │   └── ComunidadController.php    [CREATED] Main Q&A controller
│   └── models/
│       ├── Pregunta.php               [UPDATED] Extended to use incidencia table
│       └── Respuesta.php              [UPDATED] Handles responses
├── public/
│   ├── comunidad.php                  [UPDATED] Q&A list view
│   ├── pregunta_detalle.php           [UPDATED] Q&A detail + responses
│   ├── pregunta_nueva.php             [UPDATED] Q&A creation form
│   └── partials/
│       └── bs-navbar.php              [COMPLETED] Navbar with updated structure
├── comunidad_schema.sql               [CREATED] Database initialization
└── COMUNIDAD_QA_SETUP.md             [THIS FILE] Setup documentation
```

## API / Models

### ComunidadController

Main orchestrator for Q&A operations.

**Methods:**

```php
// List questions
index($orderBy = 'FechaRegistro', $limit = 20)

// Get question detail with responses
ver($idIncidencia)

// Create question
crearPregunta($usuarioId, $titulo, $cuerpo)

// Create response
crearRespuesta($idIncidencia, $usuarioId, $cuerpo)

// Vote on question
votarPregunta($idIncidencia, $usuarioId, $valor) // valor: 1 or -1

// Vote on response
votarRespuesta($idRespuesta, $usuarioId, $valor)

// Mark response as accepted
aceptarRespuesta($idIncidencia, $idRespuesta, $usuarioId)

// Get statistics
getEstadisticas() // returns: totalPreguntas, totalRespuestas, totalMiembros, porcentajeResueltas

// Get popular tags (hardcoded)
getEtiquetasPopulares($limit = 15)
```

### Pregunta Model

Handles question/incidencia operations.

**Key Methods:**

```php
// Get all questions
getAll($limit, $offset, $orderBy, $order)

// Get single question
getById($idIncidencia)

// Create question
create($usuarioId, $titulo, $cuerpo)

// Increment views
incrementViews($idIncidencia)

// Vote on question
votar($idIncidencia, $usuarioId, $valor)

// Get statistics
getEstadisticas()

// Mark question as resolved
marcarResuelta($idIncidencia, $idRespuesta, $usuarioId)
```

### Respuesta Model

Handles response/answer operations.

**Key Methods:**

```php
// Get responses for a question
getByIncidencia($idIncidencia)

// Create response
create($idIncidencia, $usuarioId, $cuerpo)

// Vote on response
votar($idRespuesta, $usuarioId, $valor)

// Update response (by owner only)
update($idRespuesta, $usuarioId, $cuerpo)

// Delete response (by owner only)
delete($idRespuesta, $usuarioId)
```

## Views

### comunidad.php
- Lists all questions with:
  - Question title (linked to detail page)
  - Points badge
  - View count
  - Response count
  - Author info
  - Status badge (Resuelta if Estado = 'RESUELTA')
- Sorting options: Recientes (FechaRegistro), Populares (Puntos)
- Statistics box showing community metrics
- Popular tags sidebar

### pregunta_detalle.php
- Full question detail with:
  - Vote buttons (up/down)
  - View/response count
  - Author card with avatar
  - List of all responses sorted by acceptance then points
  - Vote buttons on each response
  - "Accept response" button (for question author)
  - Response form (logged-in users only)

### pregunta_nueva.php
- Question creation form with:
  - Title input (min 10 chars)
  - Description textarea (min 20 chars)
  - Optional tags input with suggestions
  - Client-side and server-side validation
  - Helper tips for asking good questions

## Key Features

### 1. Voting System
- Users can upvote (+1) or downvote (-1) questions and responses
- Unique constraint prevents duplicate votes by same user
- Votes are tracked in `voto_incidencia` and `voto_respuesta_incidencia` tables

### 2. Response Acceptance
- Question author can mark any response as "accepted"
- Accepted response shows green badge and is pinned
- Only one response can be accepted per question
- Updates `RespuestaAceptada_idRespuestaIncidencia` in incidencia table

### 3. Statistics
- Total questions (where Estado != 'CERRADA')
- Total responses across all questions
- Total community members
- Percentage of resolved questions (Estado = 'RESUELTA')

### 4. Status Management
- Questions use Estado field: ABIERTA, EN_PROGRESO, RESUELTA, CERRADA
- New questions default to Estado = 'ABIERTA'
- Answering a question updates Estado to 'EN_PROGRESO' (optional logic)
- Accepting a response updates Estado to 'RESUELTA'

### 5. View Tracking
- View count incremented on pregunta_detalle.php load
- Stored in `Vistas` column of incidencia table

## Usage Examples

### Create a Question
```php
$comunidadCtrl = new ComunidadController($pdo);
$preguntaId = $comunidadCtrl->crearPregunta(
    $userId,
    "¿Cómo configurar sensores?",
    "Detalles de la pregunta..."
);
// Redirects to pregunta_detalle.php?id=$preguntaId
```

### Vote on a Question
```php
$comunidadCtrl->votarPregunta($idIncidencia, $userId, 1); // Upvote
$comunidadCtrl->votarPregunta($idIncidencia, $userId, -1); // Downvote
```

### List Questions
```php
$preguntas = $comunidadCtrl->index('Puntos', 20); // 20 most popular questions
// Returns array of questions with author info and response count
```

## Navigation Integration

The navbar has been updated with the Q&A community links:
- **Sitios de Interés** dropdown now contains:
  - Comunidad Digital (link to `/public/comunidad.php`)
  - Sugerencias MH
  - Testimonios

## Notes

### Tags Implementation
- Tags are currently hardcoded in `ComunidadController::getEtiquetasPopulares()`
- No separate `etiqueta` table is created to minimize database complexity
- Tags in question forms are stored but not indexed (UI only)
- Can be extended later to add tagging functionality

### Authentication
- Uses existing auth helpers: `isLogged()`, `getUserId()`
- Questions require authentication
- Responses require authentication
- Only question author can accept responses

### Error Handling
- All controllers use try-catch for validation
- Database errors are caught and returned as exceptions
- View redirects on invalid IDs

### Performance Optimizations
- Database indexes on:
  - `FechaRegistro` for sorting
  - `Puntos` for popularity ranking
  - `Estado` for status filtering
  - `Vistas` for trending questions
  - `Aceptada` for filtering accepted responses

## Troubleshooting

### Table Already Exists Error
If you get "Table already exists" error when running schema:
- Use `CREATE TABLE IF NOT EXISTS` (already included in schema.sql)
- Or drop tables first: `DROP TABLE IF EXISTS voto_respuesta_incidencia, voto_incidencia, respuesta_incidencia;`

### Foreign Key Constraint Error
- Ensure `usuario` and `incidencia` tables exist before running schema
- Check that `Usuario_idUsuario` values in `incidencia` correspond to existing users

### Vote Not Saving
- Check unique constraint on `(usuario_id, incidencia_id)` pair
- User can only have one vote per question/response

## Future Enhancements

1. **Real Tag System**: Create `etiqueta` table and `incidencia_etiqueta` junction table
2. **Search**: Implement full-text search on titles and descriptions
3. **Notifications**: Email notifications for responses to user's questions
4. **User Reputation**: Calculate user reputation from all votes received
5. **Moderation**: Add moderator role to delete spam questions/responses
6. **Favorites**: Allow users to bookmark favorite questions
7. **Badge System**: Award badges for helpful questions/responses

## Support

For issues or questions about the Q&A system implementation, refer to:
- [app/controllers/ComunidadController.php](app/controllers/ComunidadController.php) - Logic orchestration
- [app/models/Pregunta.php](app/models/Pregunta.php) - Question operations
- [app/models/Respuesta.php](app/models/Respuesta.php) - Response operations
