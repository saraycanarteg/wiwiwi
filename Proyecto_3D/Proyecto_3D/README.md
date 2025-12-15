# Editor 3D - Proyecto de Computación Gráfica

## 📋 Descripción

Herramienta interactiva de computación gráfica 3D desarrollada en C# con Windows Forms que permite crear, visualizar y manipular figuras tridimensionales. La aplicación cuenta con una interfaz visual inspirada en Blender, ofreciendo una experiencia profesional y fácil de usar.

## ✨ Características Principales

### 🎨 Figuras Primitivas 3D
- **Cubo**: Figura básica de 6 caras
- **Esfera**: Generada con segmentos y anillos configurables
- **Cilindro**: Con bases circulares y altura ajustable
- **Cono**: Pirámide con base circular
- **Pirámide**: Base cuadrada con 4 caras triangulares
- **Toroide**: Figura tipo dona con radio mayor y menor

### 🔄 Transformaciones 3D
- **Traslación**: Movimiento en los ejes X, Y, Z
- **Rotación**: Giro en cualquier eje (en grados)
- **Escalamiento**: Cambio de tamaño independiente por eje

### 📷 Sistema de Cámara Orbital
- **Rotación orbital**: Click izquierdo + arrastrar
- **Paneo**: Click medio o Shift + Click izquierdo + arrastrar
- **Zoom**: Rueda del mouse
- **Reset de cámara**: Botón para volver a la vista inicial

### 🎭 Propiedades Visuales
- **Color de líneas**: Personalizable por figura
- **Color de relleno**: Con transparencia para efecto 3D
- **Mostrar/Ocultar relleno**: Toggle para wireframe
- **Visibilidad**: Mostrar u ocultar objetos individuales

### 🛠️ Herramientas de Edición
- **Selección de objetos**: Click en la lista lateral
- **Duplicar objetos**: Clonación rápida de figuras
- **Eliminar objetos**: Tecla Delete o botón eliminar
- **Lista de objetos**: Gestión visual de la escena

### 📐 Ayudas Visuales
- **Ejes 3D**: X (Rojo), Y (Verde), Z (Azul)
- **Grid**: Cuadrícula de referencia en el plano XZ
- **Indicadores**: Iconos visuales de visibilidad

## 🏗️ Arquitectura del Proyecto

### Clases Principales

#### `Punto3D.cs`
Representa un punto en el espacio 3D con coordenadas homogéneas.

```csharp
// Propiedades principales
public double X, Y, Z, W

// Operaciones vectoriales
- ProductoCruz(): Producto vectorial
- ProductoPunto(): Producto escalar
- VectorNormalizado(): Vector unitario
- Magnitud(): Longitud del vector
```

#### `Arista.cs`
Define una conexión entre dos vértices.

```csharp
public int Inicio  // Índice del vértice inicial
public int Fin     // Índice del vértice final
```

#### `Figura3D.cs`
Clase principal para representar figuras 3D.

```csharp
// Geometría
- List<Punto3D> Vertices
- List<Arista> Aristas
- List<List<int>> Caras

// Transformaciones
- Punto3D Posicion
- Punto3D Rotacion
- Punto3D Escala

// Métodos de creación
- CrearCubo()
- CrearEsfera()
- CrearCilindro()
- CrearCono()
- CrearPiramide()
- CrearToroide()
```

#### `Motor3D.cs`
Motor de renderizado y transformaciones 3D.

```csharp
// Cámara orbital
- ActualizarPosicionCamara()
- RotarCamara()
- ZoomCamara()
- PanearCamara()

// Transformaciones
- Trasladar()
- Escalar()
- RotarX(), RotarY(), RotarZ()
- AplicarTransformaciones()

// Renderizado
- ProyectarPunto(): Proyección perspectiva
- DibujarFigura()
- DibujarEjes()
- DibujarGrid()
```

## 🎯 Algoritmos Implementados

### 1. Proyección Perspectiva
Convierte coordenadas 3D a 2D usando matriz de vista y proyección:

```
1. Transformar a espacio de cámara (View Matrix)
2. Aplicar proyección perspectiva (Projection Matrix)
3. Convertir a coordenadas de pantalla (Viewport Transform)
```

### 2. Transformaciones 3D

#### Traslación
```
x' = x + tx
y' = y + ty
z' = z + tz
```

#### Rotación sobre eje X
```
y' = y*cos(θ) - z*sin(θ)
z' = y*sin(θ) + z*cos(θ)
```

#### Rotación sobre eje Y
```
x' = x*cos(θ) + z*sin(θ)
z' = -x*sin(θ) + z*cos(θ)
```

#### Rotación sobre eje Z
```
x' = x*cos(θ) - y*sin(θ)
y' = x*sin(θ) + y*cos(θ)
```

#### Escalamiento
```
x' = cx + (x - cx)*sx
y' = cy + (y - cy)*sy
z' = cz + (z - cz)*sz
```

### 3. Generación de Primitivas

#### Esfera
Utiliza coordenadas esféricas:
```
x = r * sin(φ) * cos(θ)
y = r * cos(φ)
z = r * sin(φ) * sin(θ)
```

#### Toroide
Combina dos círculos:
```
x = (R + r*cos(θ)) * cos(φ)
y = r * sin(θ)
z = (R + r*cos(θ)) * sin(φ)
```

## 🎮 Controles

### Mouse
- **Click Izquierdo + Arrastrar**: Rotar cámara
- **Click Medio + Arrastrar**: Paneo de cámara
- **Shift + Click Izquierdo**: Paneo alternativo
- **Rueda del Mouse**: Zoom in/out

### Teclado
- **Delete**: Eliminar objeto seleccionado

### Interfaz
- **Panel Izquierdo**: Agregar figuras y gestionar objetos
- **Panel Central**: Viewport 3D de renderizado
- **Panel Derecho**: Propiedades y transformaciones

## 📊 Especificaciones Técnicas

- **Lenguaje**: C# (.NET Framework 4.8)
- **UI Framework**: Windows Forms
- **Renderizado**: GDI+ con doble buffer
- **Proyección**: Perspectiva con FOV configurable
- **Frame Rate**: ~60 FPS (16ms por frame)

## 🚀 Características Avanzadas

1. **Sistema de Cámara Orbital**
   - Rotación suave alrededor del punto objetivo
   - Límites de ángulo vertical para evitar gimbal lock
   - Distancia dinámica con límites

2. **Renderizado Optimizado**
   - Doble buffer para eliminar parpadeo
   - Anti-aliasing activado
   - Manejo de excepciones en proyección

3. **Gestión de Estado**
   - Vértices originales guardados para transformaciones
   - Transformaciones acumulativas
   - Clonación profunda de objetos

4. **Interfaz Estilo Blender**
   - Tema oscuro profesional
   - Paneles laterales organizados
   - Controles numéricos precisos
   - Iconos y emojis para mejor UX

## 📝 Decisiones de Diseño

### Coordinadas Homogéneas
Se utilizan coordenadas homogéneas (X, Y, Z, W) para facilitar las transformaciones mediante multiplicación de matrices, aunque en esta implementación se aplicaron directamente.

### Separación de Responsabilidades
- **Punto3D**: Operaciones vectoriales básicas
- **Figura3D**: Geometría y propiedades visuales
- **Motor3D**: Transformaciones y renderizado
- **Form1**: Lógica de UI e interacción

### Renderizado en Tiempo Real
Se usa un Timer con intervalo de 16ms para actualizar continuamente la escena, permitiendo animaciones suaves y respuesta inmediata a las transformaciones.

### Sistema de Selección
Los objetos se seleccionan desde la lista lateral, mostrándose en amarillo en el viewport para facilitar la identificación.

## 🎓 Conceptos de Computación Gráfica Aplicados

1. **Transformaciones Afines**: Traslación, rotación, escalamiento
2. **Proyección Perspectiva**: De 3D a 2D
3. **Matrices de Vista**: Cámara en el espacio 3D
4. **Geometría Procedural**: Generación de primitivas
5. **Producto Vectorial**: Para calcular normales
6. **Coordenadas Esféricas**: Para generar esferas
7. **Interpolación**: En la generación de superficies

## 🔄 Flujo de Renderizado

```
1. Timer dispara evento de renderizado (60 FPS)
2. Limpiar buffer con color de fondo
3. Si Grid habilitado → DibujarGrid()
4. Si Ejes habilitados → DibujarEjes()
5. Para cada figura visible:
   a. AplicarTransformaciones()
   b. ProyectarPunto() para cada vértice
   c. DibujarFigura() con relleno y aristas
6. Invalidar panel para mostrar buffer
```

## 🛡️ Manejo de Errores

- **Proyección**: Try-catch para puntos fuera del viewport
- **División por cero**: Validación en proyección perspectiva
- **Límites de cámara**: Restricciones de ángulo y distancia
- **Normalización de vectores**: Verificación de magnitud > 0

## 📈 Posibles Mejoras Futuras

- [ ] Iluminación Phong/Gouraud
- [ ] Texturas y materiales
- [ ] Eliminación de caras ocultas (Back-face culling)
- [ ] Z-buffering para profundidad correcta
- [ ] Exportación a formatos 3D (OBJ, STL)
- [ ] Animaciones con keyframes
- [ ] Soporte para modelos externos
- [ ] Shader pipeline personalizado
- [ ] Ray tracing básico

## 👥 Autor

Proyecto desarrollado para la materia de Computación Gráfica
Universidad - 5to Semestre

## 📄 Licencia

Proyecto educativo - Uso libre para aprendizaje

---

**Nota**: Este proyecto demuestra los conceptos fundamentales de computación gráfica 3D en un entorno interactivo, similar a herramientas profesionales como Blender, pero con un enfoque educativo y simplicidad en la implementación.
