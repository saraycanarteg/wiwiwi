using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;

namespace Proyecto_3D
{
    public enum TipoTextura
    {
        Cristal,
        Piedra,
        Esponja,
        Oro,
        Diamante
    }

    /// <summary>
    /// Representa una figura 3D con sus vértices, aristas y propiedades
    /// </summary>
    public class Figura3D
    {
        public List<Punto3D> Vertices { get; set; }
        public List<Punto3D> VerticesOriginales { get; set; }
        public List<Arista> Aristas { get; set; }
        public List<List<int>> Caras { get; set; }
        public List<Punto3D> NormalesCaras { get; set; } // Normales para iluminación
        public List<Punto3D> NormalesVertices { get; set; } // Normales por vértice para suavizado

        public string Nombre { get; set; }
        public Color ColorLinea { get; set; }
        public Color ColorRelleno { get; set; }
        public bool MostrarRelleno { get; set; }
        public bool Visible { get; set; }
        public bool Seleccionada { get; set; }

        // Propiedades de iluminación
        public double IntensidadLuz { get; set; } = 0.8;
        public double LuzAmbiente { get; set; } = 0.3;
        public TipoTextura TipoTextura { get; set; } = TipoTextura.Cristal;

        // Propiedades especulares para mayor solidez visual
        public double SpecularStrength { get; set; } = 0.6; // intensidad del brillo especular
        public int Shininess { get; set; } = 20; // exponente de brillo

        // Transformaciones acumuladas
        public Punto3D Posicion { get; set; }
        public Punto3D Rotacion { get; set; }
        public Punto3D Escala { get; set; }

        public Figura3D(string nombre = "Figura")
        {
            Vertices = new List<Punto3D>();
            VerticesOriginales = new List<Punto3D>();
            Aristas = new List<Arista>();
            Caras = new List<List<int>>();
            NormalesCaras = new List<Punto3D>();
            NormalesVertices = new List<Punto3D>();

            Nombre = nombre;
            ColorLinea = Color.White;
            // Hacer el relleno más opaco por defecto para que las figuras se vean sólidas
            ColorRelleno = Color.FromArgb(230, 100, 150, 200);
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

        public void CalcularNormalesCaras()
        {
            NormalesCaras.Clear();

            foreach (var cara in Caras)
            {
                if (cara.Count < 3)
                {
                    NormalesCaras.Add(new Punto3D(0, 1, 0));
                    continue;
                }

                // Tomar los tres primeros vértices para calcular la normal
                Punto3D v0 = Vertices[cara[0]];
                Punto3D v1 = Vertices[cara[1]];
                Punto3D v2 = Vertices[cara[2]];

                Punto3D edge1 = v1 - v0;
                Punto3D edge2 = v2 - v0;

                Punto3D normal = Punto3D.ProductoCruz(edge1, edge2).VectorNormalizado();
                NormalesCaras.Add(normal);
            }

            // Calcular normales por vértice promediando normales de caras adyacentes
            NormalesVertices.Clear();
            for (int i = 0; i < Vertices.Count; i++)
            {
                NormalesVertices.Add(new Punto3D(0, 0, 0));
            }

            int caraIndex = 0;
            foreach (var cara in Caras)
            {
                Punto3D normalCara = (caraIndex < NormalesCaras.Count) ? NormalesCaras[caraIndex] : new Punto3D(0, 1, 0);
                foreach (int vi in cara)
                {
                    if (vi >= 0 && vi < NormalesVertices.Count)
                    {
                        NormalesVertices[vi] = NormalesVertices[vi] + normalCara;
                    }
                }
                caraIndex++;
            }

            // Normalizar
            for (int i = 0; i < NormalesVertices.Count; i++)
            {
                if (NormalesVertices[i].Magnitud() == 0)
                    NormalesVertices[i] = new Punto3D(0, 1, 0);
                else
                    NormalesVertices[i] = NormalesVertices[i].VectorNormalizado();
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

        public Color ObtenerColorTextura()
        {
            // Usar ColorRelleno como base y aplicar tint según tipo de textura
            Color baseColor = ColorRelleno;
            int alpha;

            switch (TipoTextura)
            {
                case TipoTextura.Cristal:
                    alpha = Math.Min(220, Math.Max(40, (int)baseColor.A / 2));
                    return Color.FromArgb(alpha, baseColor.R, baseColor.G, baseColor.B);

                case TipoTextura.Piedra:
                    alpha = Math.Min(255, Math.Max(150, (int)baseColor.A));
                    // Tonificar hacia gris
                    int rP = ((int)baseColor.R + 100) / 2;
                    int gP = ((int)baseColor.G + 100) / 2;
                    int bP = ((int)baseColor.B + 100) / 2;
                    return Color.FromArgb(alpha, rP, gP, bP);

                case TipoTextura.Esponja:
                    alpha = Math.Min(230, (int)baseColor.A);
                    // Aclarar un poco
                    int rS = Math.Min(255, (int)baseColor.R + 20);
                    int gS = Math.Min(255, (int)baseColor.G + 20);
                    int bS = Math.Min(255, (int)baseColor.B + 10);
                    return Color.FromArgb(alpha, rS, gS, bS);

                case TipoTextura.Oro:
                    alpha = Math.Min(255, (int)baseColor.A);
                    // Dorado aproximado mezclando
                    int rO = Math.Min(255, ((int)baseColor.R + 255) / 2);
                    int gO = Math.Min(255, ((int)baseColor.G + 215) / 2);
                    int bO = Math.Min(255, (int)baseColor.B / 2);
                    return Color.FromArgb(alpha, rO, gO, bO);

                case TipoTextura.Diamante:
                    alpha = Math.Min(220, Math.Max(120, (int)baseColor.A));
                    int rD = Math.Min(255, (int)baseColor.R + 30);
                    int gD = Math.Min(255, (int)baseColor.G + 30);
                    int bD = Math.Min(255, (int)baseColor.B + 50);
                    return Color.FromArgb(alpha, rD, gD, bD);

                default:
                    return baseColor;
            }
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
            figura.Aristas.Add(new Arista(0, 1));
            figura.Aristas.Add(new Arista(1, 2));
            figura.Aristas.Add(new Arista(2, 3));
            figura.Aristas.Add(new Arista(3, 0));
            figura.Aristas.Add(new Arista(4, 5));
            figura.Aristas.Add(new Arista(5, 6));
            figura.Aristas.Add(new Arista(6, 7));
            figura.Aristas.Add(new Arista(7, 4));
            figura.Aristas.Add(new Arista(0, 4));
            figura.Aristas.Add(new Arista(1, 5));
            figura.Aristas.Add(new Arista(2, 6));
            figura.Aristas.Add(new Arista(3, 7));

            // Caras
            figura.Caras.Add(new List<int> { 0, 1, 2, 3 }); // Frente
            figura.Caras.Add(new List<int> { 5, 4, 7, 6 }); // Atrás
            figura.Caras.Add(new List<int> { 0, 4, 5, 1 }); // Abajo
            figura.Caras.Add(new List<int> { 2, 6, 7, 3 }); // Arriba
            figura.Caras.Add(new List<int> { 0, 3, 7, 4 }); // Izquierda
            figura.Caras.Add(new List<int> { 1, 5, 6, 2 }); // Derecha

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearEsfera(double radio = 1.0, int segmentos = 16, int anillos = 12)
        {
            var figura = new Figura3D("Esfera");

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

            // Generar aristas y caras
            for (int i = 0; i < anillos; i++)
            {
                for (int j = 0; j < segmentos; j++)
                {
                    int actual = i * (segmentos + 1) + j;
                    int siguiente = actual + segmentos + 1;

                    figura.Aristas.Add(new Arista(actual, actual + 1));
                    figura.Aristas.Add(new Arista(actual, siguiente));

                    // Crear caras (triángulos)
                    if (i < anillos && j < segmentos)
                    {
                        figura.Caras.Add(new List<int> { actual, siguiente, siguiente + 1, actual + 1 });
                    }
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

            // Aristas
            for (int i = 0; i < segmentos; i++)
            {
                figura.Aristas.Add(new Arista(i, (i + 1) % segmentos));
                figura.Aristas.Add(new Arista(segmentos + i, segmentos + ((i + 1) % segmentos)));
                figura.Aristas.Add(new Arista(i, segmentos + i));
            }

            // Caras laterales
            for (int i = 0; i < segmentos; i++)
            {
                int next = (i + 1) % segmentos;
                figura.Caras.Add(new List<int> { i, segmentos + i, segmentos + next, next });
            }

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearCono(double radio = 1.0, double altura = 2.0, int segmentos = 16)
        {
            var figura = new Figura3D("Cono");
            double h = altura / 2;

            figura.Vertices.Add(new Punto3D(0, h, 0));

            for (int i = 0; i < segmentos; i++)
            {
                double angulo = 2 * Math.PI * i / segmentos;
                double x = radio * Math.Cos(angulo);
                double z = radio * Math.Sin(angulo);
                figura.Vertices.Add(new Punto3D(x, -h, z));
            }

            for (int i = 1; i <= segmentos; i++)
            {
                figura.Aristas.Add(new Arista(0, i));
                figura.Aristas.Add(new Arista(i, (i % segmentos) + 1));
            }

            // Caras laterales
            for (int i = 1; i <= segmentos; i++)
            {
                int next = (i % segmentos) + 1;
                figura.Caras.Add(new List<int> { 0, i, next });
            }

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearPiramide(double tamaño = 1.0)
        {
            var figura = new Figura3D("Pirámide");
            double s = tamaño / 2;
            double h = tamaño;

            figura.Vertices.Add(new Punto3D(0, h, 0));
            figura.Vertices.Add(new Punto3D(-s, 0, -s));
            figura.Vertices.Add(new Punto3D(s, 0, -s));
            figura.Vertices.Add(new Punto3D(s, 0, s));
            figura.Vertices.Add(new Punto3D(-s, 0, s));

            for (int i = 1; i <= 4; i++)
            {
                figura.Aristas.Add(new Arista(0, i));
            }
            figura.Aristas.Add(new Arista(1, 2));
            figura.Aristas.Add(new Arista(2, 3));
            figura.Aristas.Add(new Arista(3, 4));
            figura.Aristas.Add(new Arista(4, 1));

            figura.Caras.Add(new List<int> { 1, 2, 3, 4 });
            figura.Caras.Add(new List<int> { 0, 1, 2 });
            figura.Caras.Add(new List<int> { 0, 2, 3 });
            figura.Caras.Add(new List<int> { 0, 3, 4 });
            figura.Caras.Add(new List<int> { 0, 4, 1 });

            figura.GuardarEstadoOriginal();
            return figura;
        }

        public static Figura3D CrearToroide(double radioMayor = 1.5, double radioMenor = 0.5, int segmentos = 24, int tubos = 16)
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

            // Aristas y caras
            for (int i = 0; i < segmentos; i++)
            {
                for (int j = 0; j < tubos; j++)
                {
                    int actual = i * tubos + j;
                    int siguiente = ((i + 1) % segmentos) * tubos + j;
                    int siguienteTubo = i * tubos + ((j + 1) % tubos);

                    figura.Aristas.Add(new Arista(actual, siguiente));
                    figura.Aristas.Add(new Arista(actual, siguienteTubo));

                    // Cara cuadrangular
                    int nextI = ((i + 1) % segmentos) * tubos + j;
                    int nextJ = i * tubos + ((j + 1) % tubos);
                    int nextIJ = ((i + 1) % segmentos) * tubos + ((j + 1) % tubos);
                    figura.Caras.Add(new List<int> { actual, nextI, nextIJ, nextJ });
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
                IntensidadLuz = IntensidadLuz,
                LuzAmbiente = LuzAmbiente,
                TipoTextura = TipoTextura,
                Posicion = Posicion.Clone(),
                Rotacion = Rotacion.Clone(),
                Escala = Escala.Clone(),
                SpecularStrength = SpecularStrength,
                Shininess = Shininess
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