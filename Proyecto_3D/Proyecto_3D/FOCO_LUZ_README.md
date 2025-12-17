# 💡 Implementación de Foco de Luz

## Descripción
Se ha agregado un sistema de iluminación puntual (foco) al editor 3D que permite proyectar luz sobre las figuras en la escena.

## Características Implementadas

### 1. **Objeto Foco de Luz**
- Representado como una pequeña esfera amarilla brillante
- Tamaño: 0.3 unidades de radio
- Color: Amarillo (#FFFF64)
- Posición inicial: (3, 3, 3)

### 2. **Control de Visibilidad**
- Nuevo checkbox **"Mostrar Foco"** en el panel "Vista"
- ☐ Desactivado: El foco está oculto y se usa iluminación direccional por defecto
- ☑ Activado: El foco aparece en la escena y proyecta luz puntual

### 3. **Iluminación Puntual**
Cuando el foco está activo:
- La luz se proyecta desde la posición del foco hacia todas las figuras
- **Atenuación con distancia**: La intensidad de la luz disminuye según la fórmula:
  ```
  atenuación = 1.0 / (1.0 + 0.1 * distancia + 0.01 * distancia²)
  ```
- Las superficies cercanas al foco reciben más luz
- Las superficies alejadas se ven más oscuras

### 4. **Controles de Movimiento**
Al activar el checkbox "Mostrar Foco":
- El foco se **selecciona automáticamente**
- Puedes moverlo usando los controles de:
  - **Posición (X, Y, Z)**: Traslación en el espacio 3D
  - **Rotación (X, Y, Z)**: Aunque es una esfera, la rotación está disponible para consistencia

### 5. **Modos de Iluminación**

#### Luz Puntual (Foco Activado)
- Fuente de luz en una posición específica en el espacio
- La dirección de la luz varía según la posición de cada cara de las figuras
- Intensidad variable con la distancia

#### Luz Direccional (Foco Desactivado)
- Iluminación uniforme desde una dirección fija
- Similar a la luz del sol
- No hay atenuación por distancia

## Uso

### Activar el Foco
1. Ve al panel derecho → Sección "Vista"
2. Marca el checkbox **"Mostrar Foco"**
3. El foco aparecerá automáticamente y quedará seleccionado

### Mover el Foco
Con el foco seleccionado:
1. Usa los controles de **Posición** para moverlo:
   - X: Mover horizontalmente (izquierda-derecha)
   - Y: Mover verticalmente (arriba-abajo)  
   - Z: Mover en profundidad (cerca-lejos)

2. Observa cómo la iluminación de las figuras cambia en tiempo real

### Desactivar el Foco
1. Desmarca el checkbox **"Mostrar Foco"**
2. El sistema vuelve a usar iluminación direccional
3. El foco se deselecciona automáticamente

## Ejemplos de Uso

### Escena con Iluminación Dramática
```
1. Agrega varios cubos en diferentes posiciones
2. Activa el foco
3. Mueve el foco a (5, 5, 5)
4. Observa cómo los cubos cercanos están más iluminados
```

### Simular un Candelabro
```
1. Agrega un cilindro (poste del candelabro)
2. Agrega una esfera en la parte superior
3. Activa el foco y posiciónalo en la misma ubicación de la esfera
4. Las figuras alrededor parecerán iluminadas por el candelabro
```

### Jugar con Sombras
```
1. Coloca varias figuras en fila
2. Activa el foco
3. Mueve el foco de un lado a otro (cambia X)
4. Observa cómo cambian las áreas iluminadas y oscuras
```

## Detalles Técnicos

### Archivos Modificados

#### `Form1.cs`
- Agregado campo `focoLuz` de tipo `Figura3D`
- Método `InicializarEscena()`: Crea e inicializa el foco
- Método `ConfigurarEventos()`: Agrega evento para el checkbox
- Método `RenderizarEscena()`: Actualiza la posición de luz cuando el foco está activo
- Método `SeleccionarFoco()`: Permite seleccionar el foco para manipulación

#### `Form1.Designer.cs`
- Nuevo control `chkMostrarFoco` (CheckBox)
- Ajuste de tamaños del `groupBoxVista` (de 137 a 160 píxeles)
- Ajuste de posición del `panelPropiedades` (de 145 a 168 píxeles)

#### `Motor3D.cs`
- Nuevas propiedades:
  - `PosicionLuz`: Punto3D con la ubicación del foco
  - `UsarLuzPosicional`: Boolean para alternar entre luz puntual y direccional
- Método `CalcularColorConIluminacion()` mejorado:
  - Calcula vector desde superficie hacia la luz
  - Aplica atenuación por distancia
  - Soporte para ambos modos de iluminación

### Cálculo de Iluminación

#### Componentes
1. **Luz Ambiente**: Base constante (30% por defecto)
2. **Luz Difusa**: Basada en el ángulo entre normal y dirección de luz
3. **Atenuación**: Solo en modo puntual, usando distancia al cuadrado

#### Fórmula Difusa (Puntual)
```csharp
direccionALuz = (PosicionLuz - CentroFigura).Normalizado()
difusa = max(0, ProductoPunto(normal, direccionALuz))
distancia = (PosicionLuz - CentroFigura).Magnitud()
atenuacion = 1.0 / (1.0 + 0.1 * distancia + 0.01 * distancia²)
difusa *= atenuacion * intensidadLuz
```

#### Fórmula Difusa (Direccional)
```csharp
difusa = max(0, -ProductoPunto(normal, DireccionLuz))
difusa *= intensidadLuz
```

## Limitaciones y Consideraciones

1. **Un Solo Foco**: El sistema soporta solo un foco de luz a la vez
2. **No Proyecta Sombras**: Las figuras no crean sombras entre sí
3. **Iluminación por Cara**: Se calcula una iluminación promedio por cara, no por píxel
4. **Selección Automática**: Al activar el foco, se deselecciona cualquier otra figura

## Mejoras Futuras Posibles

- [ ] Soporte para múltiples focos
- [ ] Control de intensidad del foco (brillo)
- [ ] Control de color del foco
- [ ] Proyección de sombras
- [ ] Efecto de spotlight (cono de luz direccional)
- [ ] Luz especular (reflejos brillantes)

## Compatibilidad

- Compatible con todas las figuras primitivas existentes
- Compatible con todos los modos de cámara (Orbital, Libre, Fija)
- Compatible con todas las texturas (Cristal, Piedra, Esponja, Oro, Diamante)
- No interfiere con los controles de iluminación por figura

## Notas de Rendimiento

- El cálculo de iluminación puntual es ligeramente más costoso que el direccional
- En escenas con muchas figuras, puede haber una pequeña reducción de FPS
- La atenuación cuadrática proporciona resultados visuales realistas

---

**¡Experimenta con diferentes posiciones del foco para crear escenas visualmente interesantes!** 💡✨
