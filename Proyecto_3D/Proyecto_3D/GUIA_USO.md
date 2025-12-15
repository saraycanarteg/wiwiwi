# 📖 Guía de Uso - Editor 3D

## 🚀 Inicio Rápido

### Primer Uso
1. Ejecuta `Proyecto_3D.exe`
2. La aplicación se abrirá con un cubo predeterminado
3. Explora la interfaz dividida en tres paneles

## 🎨 Panel Izquierdo - Creación y Gestión

### Agregar Figuras
Haz clic en cualquiera de los botones de figuras:

- 🎲 **Cubo**: Figura básica perfecta para empezar
- ⚽ **Esfera**: Superficie suave con múltiples segmentos
- 📦 **Cilindro**: Ideal para columnas o tubos
- 🎩 **Cono**: Base circular con punta
- 🔺 **Pirámide**: Base cuadrada estilo egipcio
- 🍩 **Toroide**: Figura tipo dona

### Lista de Objetos
- **Seleccionar**: Click en el nombre del objeto
- **Eliminar**: Selecciona y presiona `Delete` o usa el botón "Eliminar"
- **Duplicar**: Selecciona y presiona "Duplicar"
- **Visibilidad**: El icono 👁 indica que el objeto es visible

## 🖼️ Panel Central - Viewport 3D

### Navegación con Mouse

#### Rotar Cámara
```
Click Izquierdo + Arrastrar
```
- Mueve el mouse horizontalmente para rotar alrededor del objeto
- Mueve verticalmente para cambiar la elevación
- La cámara siempre mira al centro de la escena

#### Paneo (Mover Vista)
```
Click Medio + Arrastrar
o
Shift + Click Izquierdo + Arrastrar
```
- Mueve la cámara y el punto objetivo
- Útil para centrar objetos específicos

#### Zoom
```
Rueda del Mouse
```
- Hacia arriba: Acerca la cámara
- Hacia abajo: Aleja la cámara
- Rango: 1 a 50 unidades

### Elementos Visuales

#### Ejes de Coordenadas
- 🔴 **Rojo**: Eje X (horizontal derecha)
- 🟢 **Verde**: Eje Y (vertical arriba)
- 🔵 **Azul**: Eje Z (profundidad)

#### Grid (Cuadrícula)
- Ayuda a entender la escala y posición
- Dibujado en el plano XZ (suelo)
- Espaciado de 1 unidad

#### Objeto Seleccionado
- Se dibuja con líneas **amarillas**
- Las demás figuras mantienen su color original

## ⚙️ Panel Derecho - Propiedades

### Vista
Controles generales de visualización:

- ☑️ **Mostrar Ejes**: Activa/desactiva los ejes de coordenadas
- ☑️ **Mostrar Grid**: Activa/desactiva la cuadrícula
- 🔄 **Resetear Cámara**: Vuelve a la posición inicial de la cámara

### Posición
Mueve el objeto en el espacio 3D:

- **X**: -100 a +100 (Izquierda-Derecha)
- **Y**: -100 a +100 (Abajo-Arriba)
- **Z**: -100 a +100 (Cerca-Lejos)

💡 **Tip**: Usa valores pequeños (±5) para movimientos sutiles

### Rotación
Rota el objeto en grados:

- **X**: 0° a 360° (Pitch - Cabeceo)
- **Y**: 0° a 360° (Yaw - Guiñada)
- **Z**: 0° a 360° (Roll - Alabeo)

💡 **Tip**: 45°, 90°, 180° son ángulos comunes útiles

### Escala
Cambia el tamaño del objeto:

- **X**: 0.01 a 10 (Ancho)
- **Y**: 0.01 a 10 (Alto)
- **Z**: 0.01 a 10 (Profundidad)

💡 **Tips**:
- 1.0 = tamaño original
- 2.0 = doble de tamaño
- 0.5 = mitad de tamaño
- Valores diferentes crean deformaciones interesantes

### Apariencia

#### Color de Líneas
- Click en el botón de color
- Selecciona un color en el diálogo
- Las aristas del objeto cambiarán

#### Color de Relleno
- Click en el botón de color
- El relleno de las caras cambiará
- El color tiene transparencia para ver el 3D

#### Mostrar Relleno
- ☑️ Activado: Modo sólido (se ven las caras)
- ☐ Desactivado: Modo wireframe (solo aristas)

#### Visible
- ☑️ Activado: Objeto se muestra en la escena
- ☐ Desactivado: Objeto oculto (útil para escenas complejas)

## 🎯 Ejemplos de Uso

### Crear una Escena Simple

1. **Agregar un suelo**:
   - Añade un Cubo
   - Escala: X=5, Y=0.1, Z=5
   - Posición: Y=-1

2. **Agregar una columna**:
   - Añade un Cilindro
   - Escala: X=0.5, Y=2, Z=0.5
   - Posición: X=-2

3. **Agregar una esfera decorativa**:
   - Añade una Esfera
   - Posición: X=2, Y=1, Z=0
   - Color: Azul brillante

### Crear una Pirámide Egipcia

1. Añade una Pirámide
2. Escala: X=3, Y=2, Z=3
3. Rotación: Y=45° (para verla en diagonal)
4. Color línea: Dorado
5. Color relleno: Arena

### Crear un Sistema Solar Simple

1. **Sol** (centro):
   - Esfera
   - Escala: 2, 2, 2
   - Color: Amarillo

2. **Planeta 1**:
   - Esfera pequeña
   - Escala: 0.5, 0.5, 0.5
   - Posición: X=3
   - Color: Azul

3. **Planeta 2**:
   - Esfera pequeña
   - Posición: X=-4
   - Color: Rojo

### Crear un Robot Simple

1. **Cabeza**: Cubo pequeño arriba
2. **Cuerpo**: Cubo rectangular en el centro
3. **Brazos**: Cilindros a los lados
4. **Piernas**: Cilindros abajo

## ⌨️ Atajos de Teclado

| Tecla | Acción |
|-------|--------|
| Delete | Eliminar objeto seleccionado |

## 🎨 Consejos de Diseño

### Composición
- Usa el grid para alinear objetos
- El eje Y=0 representa el "suelo"
- Agrupa objetos relacionados cerca

### Colores
- Usa colores contrastantes para distinguir objetos
- El blanco se ve bien sobre fondo oscuro
- Colores brillantes para objetos importantes

### Escala
- Mantén proporciones realistas
- Objetos muy grandes o pequeños pueden ser difíciles de ver
- La escala 1.0 es un buen punto de partida

### Rotación
- 90° y 180° crean simetría
- 45° da un toque dinámico
- Combina rotaciones en varios ejes para efectos complejos

## 🔧 Solución de Problemas

### No veo mi objeto
- ✓ Verifica que esté marcado como Visible
- ✓ Revisa la posición (puede estar muy lejos)
- ✓ Usa "Resetear Cámara" para volver al inicio
- ✓ Zoom out con la rueda del mouse

### El objeto se ve raro
- ✓ Verifica que la escala no sea 0 en ningún eje
- ✓ Escala mínima: 0.01
- ✓ Resetea transformaciones creando uno nuevo

### La cámara está "perdida"
- ✓ Usa el botón "Resetear Cámara"
- ✓ Esto vuelve a la vista inicial

### El programa va lento
- ✓ Demasiados objetos en escena
- ✓ Elimina objetos no necesarios
- ✓ Desactiva el relleno en objetos complejos

## 📊 Límites del Sistema

| Característica | Límite |
|----------------|--------|
| Objetos en escena | Ilimitado* |
| Posición | ±100 unidades |
| Rotación | 0° - 360° |
| Escala | 0.01 - 10 |
| Zoom cámara | 1 - 50 unidades |

*Nota: Muchos objetos pueden afectar el rendimiento

## 🎓 Para Aprender Más

### Experimenta con:
1. **Simetría**: Duplica y posiciona en espejo
2. **Patrones**: Crea repeticiones uniformes
3. **Jerarquías**: Simula objetos compuestos
4. **Transformaciones complejas**: Combina rotación, escala y posición

### Ejercicios Sugeridos:
- [ ] Crear una casa simple (cubo + pirámide)
- [ ] Hacer un muñeco de nieve (3 esferas)
- [ ] Construir una ciudad con edificios
- [ ] Diseñar un paisaje espacial
- [ ] Modelar un vehículo básico

## 💡 Características Avanzadas

### Workflow Eficiente
1. Crea una figura base
2. Ajusta transformaciones básicas
3. Duplica y modifica
4. Cambia colores para diferencia
5. Ajusta la cámara para la mejor vista

### Trucos Profesionales
- Usa **Duplicar** en lugar de crear desde cero
- Ajusta **un eje a la vez** para control preciso
- **Desactiva el relleno** para ver dentro de objetos
- **Esconde objetos** temporalmente para trabajar mejor

## 📝 Notas Importantes

- Los cambios se aplican en **tiempo real**
- No hay función de deshacer (usa Duplicar antes de experimentos)
- Los objetos no se guardan automáticamente
- La cámara siempre mira al centro (0,0,0) de la escena

---

**¿Listo para crear?** ¡Empieza agregando tu primera figura! 🚀
