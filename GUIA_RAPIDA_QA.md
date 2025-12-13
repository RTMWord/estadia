# Guía Rápida - Sistema Q&A Comunidad MetaHogar

## 🚀 Instalación Rápida

### 1. Ejecutar Schema
```sql
-- Opción 1: Desde terminal
mysql -u root -p estadia < comunidad_schema.sql

-- Opción 2: Desde PHPMyAdmin
-- Copiar todo el contenido de comunidad_schema.sql y ejecutar
```

### 2. Verificar Instalación
```sql
SHOW TABLES LIKE 'respuesta%';
SHOW TABLES LIKE 'voto%';
SELECT * FROM incidencia LIMIT 1;
```

---

## 🎯 Flujo de Usuario

### Crear Pregunta
1. Click en "Comunidad Digital" → `/public/comunidad.php`
2. Click "+ Hacer una Pregunta" (requiere login)
3. Llenar título (min 10 chars) y descripción (min 20 chars)
4. Click "Publicar Pregunta"
5. Se crea en tabla `incidencia` con Estado='ABIERTA'

### Ver Listado
- `/public/comunidad.php`
- Mostrar todas las preguntas (excepto Estado='CERRADA')
- Ordenar por: Recientes (FechaRegistro) o Populares (Puntos)
- Mostrar autor, vistas, respuestas

### Ver Detalle
1. Click en título de pregunta
2. Url: `/public/pregunta_detalle.php?id={idIncidencia}`
3. Mostrar:
   - Pregunta completa
   - Botones votar (up/down)
   - Contador de vistas (se incrementa al cargar)
   - Lista de respuestas ordenadas por: aceptada DESC, luego puntos DESC

### Responder Pregunta
1. En pregunta_detalle.php, llenar campo "Cuerpo de respuesta"
2. Click "Enviar Respuesta" (requiere login)
3. Se crea en tabla `respuesta_incidencia` con Aceptada=0

### Votar
1. Click botón ⬆️ para upvote (+1)
2. Click botón ⬇️ para downvote (-1)
3. Cada usuario solo puede votar una vez por pregunta/respuesta
4. Voto se guarda en `voto_incidencia` o `voto_respuesta_incidencia`
5. Se suma a campo `Puntos` correspondiente

### Aceptar Respuesta
1. Si eres autor de la pregunta, ves botón ✓ en cada respuesta
2. Click botón ✓ para marcar como aceptada
3. Respuesta se destaca con badge verde
4. Question Estado pasa a 'RESUELTA'
5. `RespuestaAceptada_idRespuestaIncidencia` se actualiza

---

## 📊 Vistas Principales

### 1. Comunidad (`comunidad.php`)
```
Header: "Comunidad MetaHogar - Q&A"
├─ Estadísticas: [Total Preguntas] [Total Respuestas] [Total Miembros] [% Resueltas]
├─ Filtros: [Recientes] [Populares]
├─ Lista de Preguntas:
│  ├─ [Puntos] | Título (link a detalle)
│  ├─ Hace 2 horas • 3 respuestas • 45 vistas • por Usuario
│  ├─ Preview descripción...
│  └─ [Badge RESUELTA si aplica]
└─ Sidebar:
   ├─ Etiquetas Populares
   └─ Consejos de Comunidad
```

### 2. Detalle (`pregunta_detalle.php`)
```
Header: "Título de Pregunta"
├─ Pregunta:
│  ├─ [⬆️ Puntos ⬇️] | Descripción completa
│  ├─ Usuario: [Avatar] Nombre • Hace 2 horas
│  └─ Info: 45 vistas • 3 respuestas
├─ Respuestas (ordenadas por aceptada DESC, puntos DESC):
│  ├─ [✓ ACEPTADA] (si aplica)
│  ├─ [⬆️ Puntos ⬇️] | Cuerpo respuesta
│  ├─ Usuario: [Avatar] Nombre • Respondió hace 1 hora
│  └─ [✓ Aceptar] (si eres autor)
└─ Formulario Nueva Respuesta (solo logged-in)
   └─ Textarea cuerpo + botón "Enviar Respuesta"
```

### 3. Nueva Pregunta (`pregunta_nueva.php`)
```
Header: "Hacer una Pregunta"
├─ Título (text input, min 10 chars)
├─ Descripción (textarea, min 20 chars)
├─ Etiquetas (text input, opcional)
│  └─ Sugerencias clickeables
├─ [Publicar Pregunta] [Cancelar]
└─ Sidebar Tips:
   ├─ Consejos para buena pregunta
   └─ Buscar preguntas similares
```

---

## 🗄️ Mapeo Base de Datos

### Creación de Pregunta
```
POST /pregunta_nueva.php
  ↓
ComunidadController::crearPregunta()
  ↓
Pregunta::create()
  ↓
INSERT INTO incidencia (Usuario_idUsuario, Titulo, Descripcion, Estado, FechaRegistro)
  ↓
Return idIncidencia
  ↓
Redirect: pregunta_detalle.php?id={idIncidencia}
```

### Creación de Respuesta
```
POST /pregunta_detalle.php
  ↓
ComunidadController::crearRespuesta()
  ↓
Respuesta::create()
  ↓
INSERT INTO respuesta_incidencia (Incidencia_idIncidencia, Usuario_idUsuario, Cuerpo, FechaRegistro)
  ↓
Redirect: pregunta_detalle.php?id={idIncidencia}
```

### Votación en Pregunta
```
POST /pregunta_detalle.php (votar_pregunta)
  ↓
ComunidadController::votarPregunta($idIncidencia, $userId, $valor)
  ↓
Pregunta::votar()
  ↓
[START TRANSACTION]
  - INSERT INTO voto_incidencia (Incidencia_idIncidencia, Usuario_idUsuario, Valor)
  - UPDATE incidencia SET Puntos = Puntos + $valor WHERE idIncidencia = ?
[COMMIT]
  ↓
Redirect: pregunta_detalle.php?id={idIncidencia}
```

### Aceptar Respuesta
```
POST /pregunta_detalle.php (aceptar_respuesta)
  ↓
ComunidadController::aceptarRespuesta($idIncidencia, $idRespuesta, $userId)
  ↓
Pregunta::marcarResuelta()
  ↓
[START TRANSACTION]
  - UPDATE incidencia SET Estado = 'RESUELTA', RespuestaAceptada_idRespuestaIncidencia = ?
  - UPDATE respuesta_incidencia SET Aceptada = 1
[COMMIT]
  ↓
Redirect: pregunta_detalle.php?id={idIncidencia}
```

---

## 🔍 Consultas SQL Comunes

### Preguntas Más Populares
```sql
SELECT * FROM incidencia 
WHERE Estado != 'CERRADA'
ORDER BY Puntos DESC 
LIMIT 10;
```

### Preguntas Resueltas
```sql
SELECT * FROM incidencia 
WHERE Estado = 'RESUELTA'
ORDER BY FechaRegistro DESC;
```

### Respuestas a una Pregunta
```sql
SELECT ri.*, u.Nombre, u.ApellidoP
FROM respuesta_incidencia ri
JOIN usuario u ON ri.Usuario_idUsuario = u.idUsuario
WHERE ri.Incidencia_idIncidencia = ?
ORDER BY ri.Aceptada DESC, ri.Puntos DESC;
```

### Votos de un Usuario
```sql
SELECT * FROM voto_incidencia 
WHERE Usuario_idUsuario = ?;

SELECT * FROM voto_respuesta_incidencia 
WHERE Usuario_idUsuario = ?;
```

### Estadísticas de Comunidad
```sql
SELECT 
  COUNT(DISTINCT i.idIncidencia) as totalPreguntas,
  COUNT(DISTINCT ri.idRespuestaIncidencia) as totalRespuestas,
  COUNT(DISTINCT i.Usuario_idUsuario) as totalMiembros,
  ROUND(SUM(CASE WHEN i.Estado = 'RESUELTA' THEN 1 ELSE 0 END) / 
        COUNT(DISTINCT i.idIncidencia) * 100, 0) as porcentajeResueltas
FROM incidencia i
LEFT JOIN respuesta_incidencia ri ON i.idIncidencia = ri.Incidencia_idIncidencia
WHERE i.Estado != 'CERRADA';
```

---

## 🚨 Troubleshooting

### "Error: Table doesn't exist"
- Ejecutar `comunidad_schema.sql` nuevamente
- Verificar que tabla `incidencia` existe
- Verificar que tabla `usuario` existe

### "Can't insert foreign key"
- Verificar que Usuario_idUsuario existe en tabla `usuario`
- Verificar que Incidencia_idIncidencia existe en tabla `incidencia`
- Revisar constraints con: `SHOW CREATE TABLE respuesta_incidencia;`

### Voto duplicado no se guarda
- **Esperado** - Solo un voto por usuario por pregunta/respuesta
- Para cambiar voto: primero DELETE, luego INSERT
- Unique constraint: `(Incidencia_idIncidencia, Usuario_idUsuario)`

### Pregunta no aparece en listado
- Verificar Estado != 'CERRADA'
- Verificar Usuario_idUsuario existe
- Revisar logs de BD

---

## 🔒 Permisos y Seguridad

### Sin Autenticación
- ✅ Ver listado de preguntas
- ✅ Ver detalle de pregunta
- ✅ Ver respuestas
- ❌ Crear pregunta → Redirect a login
- ❌ Crear respuesta → Redirect a login
- ❌ Votar → Disabled

### Con Autenticación
- ✅ Crear pregunta
- ✅ Crear respuesta a cualquier pregunta
- ✅ Votar en preguntas y respuestas
- ⚠️ Aceptar respuesta → Solo si eres autor de pregunta

### Verificaciones de Seguridad
```php
// Crear pregunta - requiere login
if (!isLogged()) header('Location: login.php');

// Aceptar respuesta - requiere ser autor
if (getUserId() != $pregunta['Usuario_idUsuario']) 
    throw new Exception('No autorizado');

// Votar - requiere login
if (!isLogged()) // Disabled button
```

---

## 📱 URLs Clave

| URL | Descripción |
|-----|-------------|
| `/public/comunidad.php` | Listado de preguntas |
| `/public/comunidad.php?orden=Puntos` | Preguntas populares |
| `/public/pregunta_nueva.php` | Crear pregunta |
| `/public/pregunta_detalle.php?id=5` | Ver pregunta ID=5 |
| `/public/login.php` | Login (redirect si no autenticado) |

---

## 💡 Ejemplos de Uso en PHP

### En un View
```php
<?php
require_once __DIR__ . '/../app/controllers/ComunidadController.php';

$ctrl = new ComunidadController($pdo);
$stats = $ctrl->getEstadisticas();
$preguntas = $ctrl->index('Puntos', 20);

foreach ($preguntas as $p) {
    echo $p['Titulo']; // string
    echo $p['Puntos']; // int
    echo $p['NumRespuestas']; // int
    echo $p['Estado']; // ABIERTA|RESUELTA|CERRADA
}
?>
```

### Crear Pregunta
```php
$ctrl = new ComunidadController($pdo);
$id = $ctrl->crearPregunta(
    getUserId(),
    "¿Cómo conectar WiFi?",
    "Detalles de la pregunta..."
);
header("Location: pregunta_detalle.php?id=$id");
```

### Votar
```php
$ctrl = new ComunidadController($pdo);
$ctrl->votarPregunta(
    $_POST['idIncidencia'],
    getUserId(),
    intval($_POST['valor']) // 1 o -1
);
```

---

## 📞 Soporte

Para reportar problemas o sugerencias:
1. Revisar logs de BD en `/includes/errors.log`
2. Verificar permisos en carpetas `/public/` y `/app/`
3. Consultar documentación en `COMUNIDAD_QA_SETUP.md`

**Sistema listo para uso.** ¡Buena suerte! 🎉
