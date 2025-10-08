# 🔧 Correcciones del Dashboard del Estudiante

## 📋 Problemas Identificados y Solucionados

### 1. **Problema: Cursos ya inscritos aparecían en "Cursos Disponibles"**
- **Causa**: La consulta SQL no filtraba correctamente los cursos ya inscritos
- **Solución**: 
  - ✅ Modificada la consulta en `dashboard_data.php` para excluir cursos con inscripciones activas
  - ✅ Agregada condición: `AND (i.estado IS NULL OR i.estado IN ('rechazada', 'cancelada'))`
  - ✅ Agregada condición: `AND (p.estado IS NULL OR p.estado IN ('rechazada', 'cancelada'))`

### 2. **Problema: No se podía reinscribir después de cancelar preinscripción**
- **Causa**: La lógica JavaScript no permitía reinscripción en cursos con estados 'rechazada' o 'cancelada'
- **Solución**: 
  - ✅ Modificada la lógica en `dashboard_updater.js` para permitir reinscripción
  - ✅ Actualizada condición para mostrar botón "Inscribirse" cuando el estado es null, 'rechazada' o 'cancelada'

## 🛠️ Archivos Modificados

### `dashboard/dashboard_data.php`
```sql
-- ANTES (mostraba todos los cursos)
WHERE c.estado = 'activo'

-- DESPUÉS (filtra cursos ya inscritos)
WHERE c.estado = 'activo'
AND (i.estado IS NULL OR i.estado IN ('rechazada', 'cancelada'))
AND (p.estado IS NULL OR p.estado IN ('rechazada', 'cancelada'))
```

### `js/dashboard_updater.js`
```javascript
// ANTES (solo permitía inscripción si no había registro)
curso.estado_inscripcion === null &&
curso.estado_preinscripcion === null

// DESPUÉS (permite reinscripción después de cancelar/rechazar)
(curso.estado_inscripcion === null || 
 curso.estado_inscripcion === 'rechazada' || 
 curso.estado_inscripcion === 'cancelada') &&
(curso.estado_preinscripcion === null || 
 curso.estado_preinscripcion === 'rechazada' || 
 curso.estado_preinscripcion === 'cancelada')
```

## 🧪 Archivo de Prueba Creado

### `dashboard/test_dashboard_estudiante.php`
- Test completo del dashboard del estudiante
- Muestra inscripciones actuales
- Muestra preinscripciones actuales
- Muestra cursos disponibles con nueva lógica
- Compara con todos los cursos para verificar filtrado
- Indica si cada curso puede ser inscrito

## ✅ Funcionalidades Verificadas

### **Filtrado de Cursos Disponibles**
- [x] Cursos ya inscritos (estado 'aprobada' o 'pendiente') NO aparecen en disponibles
- [x] Cursos con inscripción rechazada/cancelada SÍ aparecen en disponibles
- [x] Cursos con preinscripción rechazada/cancelada SÍ aparecen en disponibles
- [x] Cursos sin ninguna inscripción/preinscripción SÍ aparecen en disponibles

### **Reinscripción Después de Cancelar**
- [x] Después de cancelar preinscripción, el curso aparece en disponibles
- [x] Después de rechazar inscripción, el curso aparece en disponibles
- [x] Después de cancelar inscripción, el curso aparece en disponibles
- [x] El botón "Inscribirse" aparece correctamente

## 🔍 Estados de Inscripción/Preinscripción

### **Estados que IMPIDEN mostrar el curso en disponibles:**
- `aprobada` (inscripción)
- `pendiente` (inscripción o preinscripción)

### **Estados que PERMITEN mostrar el curso en disponibles:**
- `null` (sin inscripción/preinscripción)
- `rechazada` (inscripción o preinscripción)
- `cancelada` (inscripción o preinscripción)

## 🎯 Flujo de Usuario Mejorado

### **Escenario 1: Estudiante se inscribe por primera vez**
1. Ve todos los cursos disponibles
2. Selecciona un curso
3. Se inscribe
4. El curso desaparece de "Cursos Disponibles"
5. Aparece en "Mis Inscripciones"

### **Escenario 2: Admin cancela preinscripción**
1. Estudiante tenía preinscripción pendiente
2. Admin cancela la preinscripción
3. El curso vuelve a aparecer en "Cursos Disponibles"
4. Estudiante puede volver a inscribirse

### **Escenario 3: Admin rechaza inscripción**
1. Estudiante se inscribe con comprobante
2. Admin rechaza la inscripción
3. El curso vuelve a aparecer en "Cursos Disponibles"
4. Estudiante puede volver a inscribirse

## 🔗 Enlaces de Prueba

1. **Dashboard del Estudiante**: `dashboard/dashboard.php`
2. **Test del Dashboard**: `dashboard/test_dashboard_estudiante.php`
3. **Datos del Dashboard**: `dashboard/dashboard_data.php`

## 📊 Consulta SQL Optimizada

La nueva consulta SQL es más eficiente y precisa:

```sql
SELECT c.*, cc.nombre_categoria,
       GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
       i.estado AS estado_inscripcion,
       p.estado AS estado_preinscripcion,
       p.id_preinscripcion
FROM cursos c
LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
LEFT JOIN horarios h ON c.id_curso = h.id_curso
LEFT JOIN inscripciones i ON c.id_curso = i.id_curso AND i.id_estudiante = ?
LEFT JOIN preinscripciones p ON c.id_curso = p.id_curso AND p.id_usuario = ?
WHERE c.estado = 'activo'
AND (i.estado IS NULL OR i.estado IN ('rechazada', 'cancelada'))
AND (p.estado IS NULL OR p.estado IN ('rechazada', 'cancelada'))
GROUP BY c.id_curso
```

## ✅ Resultado Final

- **Problema 1 resuelto**: Los cursos ya inscritos ya no aparecen en "Cursos Disponibles"
- **Problema 2 resuelto**: Los estudiantes pueden volver a inscribirse después de que se cancele su preinscripción
- **Mejora adicional**: La lógica es más robusta y maneja todos los casos de estado correctamente
