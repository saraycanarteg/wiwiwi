using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;

namespace Proyecto_3D
{
    /// <summary>
    /// Representa una figura 3D con sus vértices, aristas y propiedades
    /// </summary>
    public class Figura3D
    {
        public List<Punto3D> Vertices { get; set; }
        public List<Punto3D> VerticesOriginales { get; set; } // Para resetear transformaciones
        public List<Arista> Aristas { get; set; }
        public List<List<int>> Caras { get; set; } // Cada cara es una lista de índices de vértices
        
        public string Nombre { get; set; }
        public Color ColorLinea { get; set; }
        public Color ColorRelleno { get; set; }
        public bool MostrarRelleno { get; set; }
        public bool Visible { get; set; }
        public bool Seleccionada { get; set; }

        // Transformaciones acumuladas
        public Punto3D Posicion { get; set; }
        public Punto3D Rotacion { get; set; } // En grados
        public Punto3D Escala { get; set; }

        public Figura3D(string nombre = "Figura")
        {
            Vertices = new List<Punto3D>();
            VerticesOriginales = new List<Punto3D>();
            Aristas = new List<Arista>();
            Caras = new List<List<int>>();
            
            Nombre = nombre;
            ColorLinea = Color.White;
            ColorRelleno = Color.FromArgb(100, 100, 150, 200);
            MostrarRelleno = true;
            Visible = true;
            Seleccionada = false;

            Posicion = new Punto3D(0, 0, 0);
            Rotacion = new Punto3D(0, 0, 0);
            Escala = new Punto3D(1, 1, 1);
        }

        public void GuardarEstadoOriginal()
        {
            VerticesOriginales.Clear();
            foreach (var v in Vertices)
            {
                VerticesOriginales.Add(v.Clone());
            }
        }

        public Punto3D ObtenerCentro()
        {
            if (Vertices.Count == 0) return new Punto3D(0, 0, 0);

            double sumX = 0, sumY = 0, sumZ = 0;
            foreach (var v in Vertices)
            {
                sumX += v.X;
                sumY += v.Y;
                sumZ += v.Z;
            }

            return new Punto3D(
                sumX / Vertices.Count,
                sumY / Vertices.Count,
                sumZ / Vertices.Count
            );
        }

        #region Figuras Primitivas

        public static Figura3D CrearCubo(double tamaño = 1.0)
        {
            var figura = new Figura3D("Cubo");
            double s = tamaño / 2;

            // 8 vértices del cubo
            figura.Vertices.Add(new Punto3D(-s, -s, -s)); // 0
            figura.Vertices.Add(new Punto3D(s, -s, -s));  // 1
            figura.Vertices.Add(new Punto3D(s, s, -s));   // 2
            figura.Vertices.Add(new Punto3D(-s, s, -s));  // 3
            figura.Vertices.Add(new Punto3D(-s, -s, s));  // 4
            figura.Vertices.Add(new Punto3D(s, -s, s));   // 5
            figura.Vertices.Add(new Punto3D(s, s, s));    // 6
            figura.Vertices.Add(new Punto3D(-s, s, s));   // 7

            // 12 aristas del cubo
            // Cara frontal
            figura.Aristas.Add(new Arista(0, 1));
            figura.Aristas.Add(new Arista(1, 2));
            figura.Aristas.Add(new Arista(2, 3));
            figura.Aristas.Add(new Arista(3, 0));
            // Cara trasera
            figura.Aristas.Add(new Arista(4, 5));
            figura.Aristas.Add(new Arista(5, 6));
            figura.Aristas.Add(new Arista(6, 7));
            figura.Aristas.Add(new Arista(7, 4));
            // Conexiones
            figura.Aristas.Add(new Arista(0, 4));
            figura.Aristas.Add(new Arista(1, 5));
            figura.Aristas.Add(new Arista(2, 6));
            figura.Aristas.Add(new Arista(3, 7));

            // Caras
            figura.Caras.Add(new List<int> { 0, 1, 2, 3 }); // Frente
            figura.Caras.Add(new List<int> { 4, 5, 6, 7 }); // Atrás
            figura.Caras.Add(new List<int> { 0, 1, 5, 4 }); // Abajo
            figura.Caras.Add(new List<int> { 3, 2, 6, 7 }); // Arriba
            figura.Caras.Add(new List<int> { 0, 3, 7, 4 }); // Izquierda
            figura.Caras.Add(new List<int> { 1, 2, 6, 5 }); // Derecha

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearEsfera(double radio = 1.0, int segmentos = 16, int anillos = 12)
        {
            var figura = new Figura3D("Esfera");

            // Generar vértices
            for (int i = 0; i <= anillos; i++)
            {
                double phi = Math.PI * i / anillos;
                double y = radio * Math.Cos(phi);
                double r = radio * Math.Sin(phi);

                for (int j = 0; j <= segmentos; j++)
                {
                    double theta = 2 * Math.PI * j / segmentos;
                    double x = r * Math.Cos(theta);
                    double z = r * Math.Sin(theta);

                    figura.Vertices.Add(new Punto3D(x, y, z));
                }
            }

            // Generar aristas
            for (int i = 0; i < anillos; i++)
            {
                for (int j = 0; j < segmentos; j++)
                {
                    int actual = i * (segmentos + 1) + j;
                    int siguiente = actual + segmentos + 1;

                    // Arista horizontal
                    figura.Aristas.Add(new Arista(actual, actual + 1));
                    // Arista vertical
                    figura.Aristas.Add(new Arista(actual, siguiente));
                }
            }

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearCilindro(double radio = 1.0, double altura = 2.0, int segmentos = 16)
        {
            var figura = new Figura3D("Cilindro");
            double h = altura / 2;

            // Círculo superior
            for (int i = 0; i < segmentos; i++)
            {
                double angulo = 2 * Math.PI * i / segmentos;
                double x = radio * Math.Cos(angulo);
                double z = radio * Math.Sin(angulo);
                figura.Vertices.Add(new Punto3D(x, h, z));
            }

            // Círculo inferior
            for (int i = 0; i < segmentos; i++)
            {
                double angulo = 2 * Math.PI * i / segmentos;
                double x = radio * Math.Cos(angulo);
                double z = radio * Math.Sin(angulo);
                figura.Vertices.Add(new Punto3D(x, -h, z));
            }

            // Aristas del círculo superior
            for (int i = 0; i < segmentos; i++)
            {
                figura.Aristas.Add(new Arista(i, (i + 1) % segmentos));
            }

            // Aristas del círculo inferior
            for (int i = 0; i < segmentos; i++)
            {
                figura.Aristas.Add(new Arista(segmentos + i, segmentos + ((i + 1) % segmentos)));
            }

            // Aristas verticales
            for (int i = 0; i < segmentos; i++)
            {
                figura.Aristas.Add(new Arista(i, segmentos + i));
            }

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearCono(double radio = 1.0, double altura = 2.0, int segmentos = 16)
        {
            var figura = new Figura3D("Cono");
            double h = altura / 2;

            // Vértice superior (punta del cono)
            figura.Vertices.Add(new Punto3D(0, h, 0));

            // Círculo de la base
            for (int i = 0; i < segmentos; i++)
            {
                double angulo = 2 * Math.PI * i / segmentos;
                double x = radio * Math.Cos(angulo);
                double z = radio * Math.Sin(angulo);
                figura.Vertices.Add(new Punto3D(x, -h, z));
            }

            // Aristas desde la punta hasta la base
            for (int i = 1; i <= segmentos; i++)
            {
                figura.Aristas.Add(new Arista(0, i));
            }

            // Aristas del círculo base
            for (int i = 1; i <= segmentos; i++)
            {
                figura.Aristas.Add(new Arista(i, (i % segmentos) + 1));
            }

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearPiramide(double tamaño = 1.0)
        {
            var figura = new Figura3D("Pirámide");
            double s = tamaño / 2;
            double h = tamaño;

            // Vértice superior
            figura.Vertices.Add(new Punto3D(0, h, 0)); // 0

            // Base cuadrada
            figura.Vertices.Add(new Punto3D(-s, 0, -s)); // 1
            figura.Vertices.Add(new Punto3D(s, 0, -s));  // 2
            figura.Vertices.Add(new Punto3D(s, 0, s));   // 3
            figura.Vertices.Add(new Punto3D(-s, 0, s));  // 4

            // Aristas desde el vértice a la base
            for (int i = 1; i <= 4; i++)
            {
                figura.Aristas.Add(new Arista(0, i));
            }

            // Aristas de la base
            figura.Aristas.Add(new Arista(1, 2));
            figura.Aristas.Add(new Arista(2, 3));
            figura.Aristas.Add(new Arista(3, 4));
            figura.Aristas.Add(new Arista(4, 1));

            // Caras
            figura.Caras.Add(new List<int> { 1, 2, 3, 4 }); // Base
            figura.Caras.Add(new List<int> { 0, 1, 2 });    // Cara 1
            figura.Caras.Add(new List<int> { 0, 2, 3 });    // Cara 2
            figura.Caras.Add(new List<int> { 0, 3, 4 });    // Cara 3
            figura.Caras.Add(new List<int> { 0, 4, 1 });    // Cara 4

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearToroide(double radioMayor = 1.5, double radioMenor = 0.5, int segmentos = 16, int tubos = 12)
        {
            var figura = new Figura3D("Toroide");

            for (int i = 0; i < segmentos; i++)
            {
                double phi = 2 * Math.PI * i / segmentos;
                double cosPhi = Math.Cos(phi);
                double sinPhi = Math.Sin(phi);

                for (int j = 0; j < tubos; j++)
                {
                    double theta = 2 * Math.PI * j / tubos;
                    double cosTheta = Math.Cos(theta);
                    double sinTheta = Math.Sin(theta);

                    double x = (radioMayor + radioMenor * cosTheta) * cosPhi;
                    double y = radioMenor * sinTheta;
                    double z = (radioMayor + radioMenor * cosTheta) * sinPhi;

                    figura.Vertices.Add(new Punto3D(x, y, z));
                }
            }

            // Generar aristas
            for (int i = 0; i < segmentos; i++)
            {
                for (int j = 0; j < tubos; j++)
                {
                    int actual = i * tubos + j;
                    int siguiente = ((i + 1) % segmentos) * tubos + j;
                    int siguienteTubo = i * tubos + ((j + 1) % tubos);

                    figura.Aristas.Add(new Arista(actual, siguiente));
                    figura.Aristas.Add(new Arista(actual, siguienteTubo));
                }
            }

            figura.GuardarEstadoOriginal();
            return figura;
        }

        #endregion

        public Figura3D Clonar()
        {
            var clon = new Figura3D(Nombre + " (Copia)")
            {
                ColorLinea = ColorLinea,
                ColorRelleno = ColorRelleno,
                MostrarRelleno = MostrarRelleno,
                Visible = Visible,
                Posicion = Posicion.Clone(),
                Rotacion = Rotacion.Clone(),
                Escala = Escala.Clone()
            };

            foreach (var v in Vertices)
                clon.Vertices.Add(v.Clone());
            
            foreach (var v in VerticesOriginales)
                clon.VerticesOriginales.Add(v.Clone());
            
            foreach (var a in Aristas)
                clon.Aristas.Add(new Arista(a.Inicio, a.Fin));
            
            foreach (var cara in Caras)
                clon.Caras.Add(new List<int>(cara));

            return clon;
        }
    }
}
