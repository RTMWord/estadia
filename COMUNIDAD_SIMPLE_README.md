# Comunidad Digital - Solución Simple

## 📋 Descripción

Sistema de **Comunidad Digital simple y escalable** para MetaHogar. Permite que usuarios publiquen preguntas/dudas y que otras personas puedan actualizarlas a diferentes estados.

**Características:**
- ✅ Crear preguntas/comentarios (requiere login)
- ✅ Ver todas las preguntas con estado
- ✅ Actualizar estado de preguntas (ABIERTA → EN_PROGRESO → RESUELTA → CERRADA)
- ✅ Estadísticas automáticas
- ✅ Sin complejidad innecesaria

---

## 🗄️ Base de Datos

**Tabla existente utilizada:** `Incidencia`

```sql
CREATE TABLE Incidencia (
  idIncidencia INT AUTO_INCREMENT PRIMARY KEY,
  Usuario_idUsuario INT (FK -> Usuario),
  Titulo VARCHAR(150),
  Descripcion TEXT,
  Estado ENUM('ABIERTA','EN_PROGRESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

**Sin tablas nuevas.** Solo reutiliza estructura existente.

---

## 📁 Archivos

```
public/
├── comunidad.php          ← Vista listado + crear pregunta
├── partials/
│   └── bs-navbar.php      ← Navegación (ya actualizada)
└── includes/
    └── footer.php         ← Pie
```

**Nota:** Se eliminan archivos complejos previos (pregunta_detalle.php, pregunta_nueva.php, modelos Q&A avanzados, etc.)

---

## 🎯 Flujo

### 1. Ver Comunidad
```
GET /public/comunidad.php
```
Muestra:
- Estadísticas: Total, Abiertas, Resueltas, Cerradas
- Lista de todas las preguntas ordenadas por fecha (más recientes primero)
- Para cada pregunta:
  - Título
  - Descripción (primeros 150 caracteres)
  - Autor
  - Hace cuánto se publicó
  - Estado actual (badge con color)
  - **Selector para cambiar estado** (requiere estar logged-in)

### 2. Crear Pregunta
```
POST /public/comunidad.php
```
Datos:
- `titulo` (mínimo 5 caracteres, máximo 150)
- `descripcion` (mínimo 10 caracteres)

**Resultado:** Inserta en tabla `Incidencia` con Estado='ABIERTA'

### 3. Actualizar Estado
```
POST /public/comunidad.php
```
Datos:
- `idIncidencia` (ID de la pregunta)
- `estado` (ABIERTA, EN_PROGRESO, RESUELTA, CERRADA)
- `actualizar_estado` (indicador de acción)

**Resultado:** Actualiza el Estado de la incidencia

---

## 💻 Código PHP Principal

### Crear Incidencia
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLogged()) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $usuarioId = getUserId();
    
    if (!empty($titulo) && !empty($descripcion)) {
        $stmt = $pdo->prepare("
            INSERT INTO Incidencia (Usuario_idUsuario, Titulo, Descripcion, Estado, FechaRegistro)
            VALUES (:usuario_id, :titulo, :descripcion, 'ABIERTA', NOW())
        ");
        $stmt->execute([...]);
    }
}
```

### Obtener Estadísticas
```php
$estadisticas = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN Estado = 'ABIERTA' THEN 1 ELSE 0 END) as abiertas,
        SUM(CASE WHEN Estado = 'RESUELTA' THEN 1 ELSE 0 END) as resueltas,
        SUM(CASE WHEN Estado = 'CERRADA' THEN 1 ELSE 0 END) as cerradas
    FROM Incidencia
")->fetch(PDO::FETCH_ASSOC);
```

---

## 🎨 Interfaz

### Modal Nueva Pregunta
- Título campo
- Descripción textarea
- Botón Publicar
- Solo visible para usuarios logged-in

### Tarjeta Incidencia
```
┌─ Titulo de Pregunta
│  Hace 2 días • por Juan Pérez
│  Esto es la descripción de la pregunta que...
│  ┌─ [ABIERTA]
│  │
│  └─ [Selector Estado] [Botón Actualizar]
```

---

## 🔐 Seguridad

✅ **SQL Injection:** Prepared statements  
✅ **XSS:** htmlspecialchars() en outputs  
✅ **Autenticación:** isLogged() check  
✅ **Validación:** Longitud mínima de campos  

---

## 📊 Estadísticas Automáticas

Se muestran 4 números actualizados en tiempo real:
- Total Preguntas (todas excepto borradas)
- Abiertas (Estado = 'ABIERTA')
- Resueltas (Estado = 'RESUELTA')
- Cerradas (Estado = 'CERRADA')

---

## 🚀 Instalación

1. **Usar metaH.sql** - Ya existe tabla Incidencia
2. **Copiar comunidad.php** - A /public/
3. **Acceder** - http://localhost/estadia/public/comunidad.php
4. **Listo!** - Sin configuración adicional

---

## ⚙️ Configuración Mínima

No requiere:
- ❌ Controladores personalizados
- ❌ Modelos Q&A
- ❌ Tablas nuevas
- ❌ Migraciones
- ❌ Configuración de BD

---

## 📈 Mejoras Futuras

Si quieres agregar más funcionalidad:

1. **Comentarios en preguntas** - Agregar tabla Comentario
2. **Votación simple** - Agregar likes/dislikes
3. **Búsqueda** - Filtro por palabra clave
4. **Notificaciones** - Email cuando responden tu pregunta
5. **Roles avanzados** - Solo admin puede cambiar estado

---

## 📞 Troubleshooting

### "No aparece la comunidad"
- Verificar que /public/comunidad.php existe
- Verificar que /public/partials/bs-navbar.php existe
- Verificar que /public/includes/footer.php existe

### "No se guarda la pregunta"
- Verificar que usuario está logged-in
- Verificar que tabla Incidencia existe
- Revisar error en BD

### "Estado no se actualiza"
- Verificar que usuario está logged-in
- Verificar valor de estado válido (ABIERTA, EN_PROGRESO, RESUELTA, CERRADA)
- Verificar que idIncidencia existe

---

## 📝 SQL Útiles

### Ver todas las preguntas
```sql
SELECT i.*, u.Nombre, u.ApellidoP 
FROM Incidencia i
LEFT JOIN Usuario u ON i.Usuario_idUsuario = u.idUsuario
ORDER BY i.FechaRegistro DESC;
```

### Contar por estado
```sql
SELECT Estado, COUNT(*) FROM Incidencia GROUP BY Estado;
```

### Últimas 5 abiertas
```sql
SELECT * FROM Incidencia 
WHERE Estado = 'ABIERTA' 
ORDER BY FechaRegistro DESC 
LIMIT 5;
```

---

**Sistema listo. Simple. Funcional.** ✅

