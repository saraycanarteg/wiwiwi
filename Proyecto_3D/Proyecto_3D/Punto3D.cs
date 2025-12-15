using System;

namespace Proyecto_3D
{
    /// <summary>
    /// Representa un punto en el espacio 3D
    /// </summary>
    public class Punto3D
    {
        public double X { get; set; }
        public double Y { get; set; }
        public double Z { get; set; }
        public double W { get; set; } // Coordenada homogénea para transformaciones

        public Punto3D(double x = 0, double y = 0, double z = 0)
        {
            X = x;
            Y = y;
            Z = z;
            W = 1; // Para transformaciones homogéneas
        }

        public Punto3D Clone()
        {
            return new Punto3D(X, Y, Z) { W = W };
        }

        /// <summary>
        /// Calcula la distancia a otro punto
        /// </summary>
        public double DistanciaA(Punto3D otro)
        {
            double dx = X - otro.X;
            double dy = Y - otro.Y;
            double dz = Z - otro.Z;
            return Math.Sqrt(dx * dx + dy * dy + dz * dz);
        }

        /// <summary>
        /// Normaliza las coordenadas homogéneas
        /// </summary>
        public void Normalizar()
        {
            if (W != 0 && W != 1)
            {
                X /= W;
                Y /= W;
                Z /= W;
                W = 1;
            }
        }

        public override string ToString()
        {
            return $"({X:F2}, {Y:F2}, {Z:F2})";
        }

        // Operadores para facilitar operaciones vectoriales
        public static Punto3D operator +(Punto3D a, Punto3D b)
        {
            return new Punto3D(a.X + b.X, a.Y + b.Y, a.Z + b.Z);
        }

        public static Punto3D operator -(Punto3D a, Punto3D b)
        {
            return new Punto3D(a.X - b.X, a.Y - b.Y, a.Z - b.Z);
        }

        public static Punto3D operator *(Punto3D a, double escalar)
        {
            return new Punto3D(a.X * escalar, a.Y * escalar, a.Z * escalar);
        }

        /// <summary>
        /// Producto cruz (vectorial)
        /// </summary>
        public static Punto3D ProductoCruz(Punto3D a, Punto3D b)
        {
            return new Punto3D(
                a.Y * b.Z - a.Z * b.Y,
                a.Z * b.X - a.X * b.Z,
                a.X * b.Y - a.Y * b.X
            );
        }

        /// <summary>
        /// Producto punto (escalar)
        /// </summary>
        public static double ProductoPunto(Punto3D a, Punto3D b)
        {
            return a.X * b.X + a.Y * b.Y + a.Z * b.Z;
        }

        /// <summary>
        /// Magnitud del vector
        /// </summary>
        public double Magnitud()
        {
            return Math.Sqrt(X * X + Y * Y + Z * Z);
        }

        /// <summary>
        /// Retorna el vector normalizado
        /// </summary>
        public Punto3D VectorNormalizado()
        {
            double mag = Magnitud();
            if (mag > 0.0001)
                return new Punto3D(X / mag, Y / mag, Z / mag);
            return new Punto3D(0, 0, 0);
        }
    }
}
