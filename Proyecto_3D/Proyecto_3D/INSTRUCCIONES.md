# 🚀 Instrucciones de Compilación y Ejecución

## Editor 3D - Proyecto de Computación Gráfica

---

## 📋 Requisitos del Sistema

### Software Necesario

1. **Sistema Operativo**: Windows 7 o superior
2. **.NET Framework 4.8**: [Descargar aquí](https://dotnet.microsoft.com/download/dotnet-framework/net48)
3. **Visual Studio** (para compilar):
   - Visual Studio 2019 o superior
   - Visual Studio Community (gratuito)
   - Workload: ".NET desktop development"

### Hardware Recomendado

- **CPU**: Procesador dual-core o superior
- **RAM**: 4 GB mínimo
- **Pantalla**: 1280×720 mínimo (recomendado 1920×1080)
- **Mouse**: Con rueda de scroll

---

## 🔧 Compilación del Proyecto

### Método 1: Con Visual Studio (Recomendado)

#### Pasos:

1. **Abrir el proyecto**:
   ```
   - Navega a la carpeta del proyecto
   - Doble clic en Proyecto_3D.sln
   ```

2. **Restaurar paquetes NuGet** (si es necesario):
   ```
   - Menú: Tools → NuGet Package Manager → Restore NuGet Packages
   ```

3. **Compilar**:
   ```
   - Menú: Build → Build Solution
   - O presiona: Ctrl + Shift + B
   ```

4. **Verificar compilación**:
   ```
   - Ventana Output debe mostrar: "Build succeeded"
   - Ejecutable en: bin\Debug\Proyecto_3D.exe
   ```

### Método 2: Línea de Comandos (MSBuild)

#### Pasos:

1. **Abrir Developer Command Prompt for VS**:
   ```
   Inicio → Visual Studio 2022 → Developer Command Prompt
   ```

2. **Navegar al proyecto**:
   ```cmd
   cd "ruta\al\proyecto\Proyecto_3D"
   ```

3. **Compilar**:
   ```cmd
   msbuild Proyecto_3D.csproj /p:Configuration=Release
   ```

4. **Ejecutable generado en**:
   ```
   bin\Release\Proyecto_3D.exe
   ```

---

## ▶️ Ejecución del Proyecto

### Opción 1: Desde Visual Studio

1. **Modo Debug** (con depuración):
   ```
   - Presiona F5
   - O: Debug → Start Debugging
   ```

2. **Modo Release** (sin depuración):
   ```
   - Presiona Ctrl + F5
   - O: Debug → Start Without Debugging
   ```

### Opción 2: Ejecutable Directo

1. **Navega a la carpeta**:
   ```
   Proyecto_3D\bin\Debug\
   o
   Proyecto_3D\bin\Release\
   ```

2. **Doble clic en**:
   ```
   Proyecto_3D.exe
   ```

### Opción 3: Desde Línea de Comandos

```cmd
cd "Proyecto_3D\bin\Debug"
Proyecto_3D.exe
```

---

## 📦 Distribución del Ejecutable

### Crear Paquete Portable

1. **Compilar en modo Release**:
   ```
   Build → Configuration Manager → Release
   Build → Build Solution
   ```

2. **Copiar archivos necesarios**:
   ```
   Crear carpeta "Proyecto_3D_v1.0"
   
   Copiar:
   - bin\Release\Proyecto_3D.exe
   - bin\Release\Proyecto_3D.exe.config (si existe)
   ```

3. **Archivos opcionales para distribuir**:
   ```
   - README.md (documentación)
   - GUIA_USO.md (manual de usuario)
   - INFORME_TECNICO.md (informe)
   ```

4. **Comprimir en ZIP**:
   ```
   Click derecho → Enviar a → Carpeta comprimida
   Nombre: Proyecto_3D_v1.0.zip
   ```

---

## 🐛 Solución de Problemas

### Error: "No se puede iniciar porque falta .NET Framework"

**Solución**:
```
1. Descargar .NET Framework 4.8
2. Instalar
3. Reiniciar el equipo
4. Intentar ejecutar nuevamente
```

### Error: "No se pudo compilar el proyecto"

**Verificar**:
```
1. Todos los archivos .cs están en el proyecto
2. Referencias están correctamente agregadas
3. No hay errores de sintaxis
4. Visual Studio actualizado
```

### Error: "Pantalla negra o no se ve nada"

**Solución**:
```
1. Presionar "Resetear Cámara"
2. Usar rueda del mouse para zoom out
3. Agregar un nuevo cubo
4. Verificar que "Visible" esté marcado
```

### Rendimiento Lento

**Optimizar**:
```
1. Cerrar otras aplicaciones
2. Reducir número de objetos en escena
3. Desactivar "Mostrar Relleno"
4. Compilar en modo Release
```

---

## 📁 Estructura de Archivos

```
Proyecto_3D/
│
├── Proyecto_3D.sln              # Solución de Visual Studio
├── Proyecto_3D.csproj           # Archivo de proyecto
│
├── Program.cs                   # Punto de entrada
├── Form1.cs                     # Lógica de UI
├── Form1.Designer.cs            # Diseño de UI
│
├── Punto3D.cs                   # Clase de punto 3D
├── Arista.cs                    # Clase de arista
├── Figura3D.cs                  # Clase de figura 3D
├── Motor3D.cs                   # Motor de renderizado
│
├── Properties/
│   ├── AssemblyInfo.cs          # Información del ensamblado
│   └── Resources.resx           # Recursos
│
├── bin/
│   ├── Debug/                   # Ejecutable debug
│   └── Release/                 # Ejecutable release
│
└── Documentación/
    ├── README.md                # Descripción general
    ├── GUIA_USO.md              # Manual de usuario
    ├── INFORME_TECNICO.md       # Informe técnico
    └── INSTRUCCIONES.md         # Este archivo
```

---

## 🔍 Verificación Post-Compilación

### Checklist de Funcionalidades

Después de compilar, verificar que funcione:

- [ ] La aplicación se abre sin errores
- [ ] Se muestra un cubo inicial
- [ ] Los botones de figuras funcionan
- [ ] Se pueden agregar múltiples objetos
- [ ] La cámara se puede rotar con mouse
- [ ] El zoom funciona con rueda del mouse
- [ ] Las transformaciones se aplican en tiempo real
- [ ] Se pueden cambiar colores
- [ ] Se pueden duplicar objetos
- [ ] Se pueden eliminar objetos
- [ ] El grid y ejes se muestran correctamente
- [ ] No hay errores en la consola de Output

---

## 📊 Configuraciones de Compilación

### Debug vs Release

| Característica | Debug | Release |
|----------------|-------|---------|
| Optimización | No | Sí |
| Símbolos debug | Sí | No |
| Tamaño | Mayor | Menor |
| Velocidad | Menor | Mayor |
| Para desarrollo | ✅ | ❌ |
| Para distribución | ❌ | ✅ |

### Cambiar entre configuraciones:

```
Build → Configuration Manager
Seleccionar: Debug o Release
```

---

## 🎯 Primeros Pasos Después de Ejecutar

1. **Familiarízate con la interfaz**:
   - Panel izquierdo: Agregar figuras
   - Panel central: Vista 3D
   - Panel derecho: Propiedades

2. **Prueba la navegación**:
   - Click izquierdo + arrastrar: Rotar
   - Rueda del mouse: Zoom
   - Click medio: Paneo

3. **Agrega algunas figuras**:
   - Click en "Cubo"
   - Click en "Esfera"
   - Experimenta con colores

4. **Explora transformaciones**:
   - Selecciona un objeto
   - Cambia posición X, Y, Z
   - Prueba rotaciones
   - Ajusta escala

5. **Lee la documentación**:
   - GUIA_USO.md para tutoriales
   - README.md para descripción general
   - INFORME_TECNICO.md para detalles técnicos

---

## 💾 Creación de Instalador (Opcional)

### Usando ClickOnce

1. **En Visual Studio**:
   ```
   - Click derecho en el proyecto
   - Publish
   - Seguir el asistente
   ```

2. **Configurar**:
   ```
   - Ubicación de publicación
   - Método de instalación
   - Requisitos previos (.NET Framework 4.8)
   ```

3. **Publicar**:
   ```
   - Click en "Publish Now"
   - Generar archivos en carpeta especificada
   ```

---

## 🔄 Actualización del Proyecto

Si necesitas modificar el código:

1. **Abrir solución en Visual Studio**
2. **Modificar archivos .cs necesarios**
3. **Guardar cambios**: Ctrl + S
4. **Compilar**: Ctrl + Shift + B
5. **Probar**: F5
6. **Si funciona, compilar Release para distribución**

---

## 📞 Soporte

### Problemas Comunes

**Error de referencias**:
```
- Click derecho en References
- Add Reference
- Agregar System.Drawing y System.Windows.Forms
```

**Archivos faltantes**:
```
- Verificar que todos los .cs estén incluidos
- Solution Explorer → Show All Files
- Click derecho en archivos → Include in Project
```

**Conflictos de versión**:
```
- Verificar que la versión de .NET Framework sea 4.8
- Properties → Application → Target Framework
```

---

## ✅ Lista de Verificación Final

Antes de entregar/distribuir:

- [ ] Código compila sin errores
- [ ] Todas las funcionalidades probadas
- [ ] README.md completo
- [ ] GUIA_USO.md clara
- [ ] INFORME_TECNICO.md detallado
- [ ] Capturas de pantalla incluidas
- [ ] Ejecutable funcional en carpeta Release
- [ ] Código fuente organizado
- [ ] Sin archivos temporales (bin/obj pueden excluirse del ZIP)

---

## 📝 Notas Importantes

1. **No incluir bin/obj en control de versiones**: Agregar a .gitignore
2. **Documentar cambios**: Si modificas el código
3. **Probar en máquina limpia**: Verificar que funcione sin Visual Studio
4. **Incluir README**: Siempre en distribuciones

---

## 🎓 Para Presentación Académica

### Preparar Demo:

1. **Compilar en Release**
2. **Tener escena preparada** con varias figuras
3. **Preparar guión** de demostración:
   - Crear figuras
   - Aplicar transformaciones
   - Mostrar navegación de cámara
   - Demostrar colores y propiedades

4. **Screenshots/Video**:
   - Capturar pantalla en varios estados
   - Grabar video de 2-3 minutos mostrando funcionalidades

5. **Documentación impresa** (opcional):
   - README.md
   - Páginas clave del INFORME_TECNICO.md

---

**¡Proyecto listo para compilar y ejecutar!**

*Para cualquier duda, consultar la documentación incluida.*
