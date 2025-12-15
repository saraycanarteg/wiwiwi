using System;
using System.Collections.Generic;
using System.Drawing;

namespace Proyecto_3D
{
    /// <summary>
    /// Motor de renderizado y transformaciones 3D
    /// </summary>
    public class Motor3D
    {
        // Parámetros de cámara
        public Punto3D PosicionCamara { get; set; }
        public Punto3D ObjetivoCamara { get; set; }
        public Punto3D UpCamara { get; set; }
        
        public double DistanciaCamara { get; set; }
        public double AnguloOrbitaH { get; set; } // Horizontal (azimut)
        public double AnguloOrbitaV { get; set; } // Vertical (elevación)

        // Parámetros de proyección
        public double CampoVision { get; set; } // Field of View en grados
        public double AspectRatio { get; set; }
        public double PlanosCercano { get; set; }
        public double PlanosLejano { get; set; }

        // Viewport
        public int AnchoVista { get; set; }
        public int AltoVista { get; set; }

        // Iluminación
        public Punto3D DireccionLuz { get; set; }

        public Motor3D(int ancho, int alto)
        {
            AnchoVista = ancho;
            AltoVista = alto;

            // Configuración inicial de cámara orbital
            DistanciaCamara = 5;
            AnguloOrbitaH = 45;
            AnguloOrbitaV = 30;
            ObjetivoCamara = new Punto3D(0, 0, 0);
            UpCamara = new Punto3D(0, 1, 0);

            ActualizarPosicionCamara();

            // Configuración de proyección
            CampoVision = 60;
            AspectRatio = (double)ancho / alto;
            PlanosCercano = 0.1;
            PlanosLejano = 100;

            // Luz
            DireccionLuz = new Punto3D(1, -1, -1).VectorNormalizado();
        }

        public void ActualizarPosicionCamara()
        {
            double radianesH = AnguloOrbitaH * Math.PI / 180.0;
            double radianesV = AnguloOrbitaV * Math.PI / 180.0;

            double x = DistanciaCamara * Math.Cos(radianesV) * Math.Sin(radianesH);
            double y = DistanciaCamara * Math.Sin(radianesV);
            double z = DistanciaCamara * Math.Cos(radianesV) * Math.Cos(radianesH);

            PosicionCamara = new Punto3D(
                ObjetivoCamara.X + x,
                ObjetivoCamara.Y + y,
                ObjetivoCamara.Z + z
            );
        }

        public void RotarCamara(double deltaH, double deltaV)
        {
            AnguloOrbitaH += deltaH;
            AnguloOrbitaV += deltaV;

            // Limitar ángulo vertical
            if (AnguloOrbitaV > 89) AnguloOrbitaV = 89;
            if (AnguloOrbitaV < -89) AnguloOrbitaV = -89;

            ActualizarPosicionCamara();
        }

        public void ZoomCamara(double delta)
        {
            DistanciaCamara += delta;
            if (DistanciaCamara < 1) DistanciaCamara = 1;
            if (DistanciaCamara > 50) DistanciaCamara = 50;

            ActualizarPosicionCamara();
        }

        public void PanearCamara(double deltaX, double deltaY)
        {
            // Calcular vectores derecha y arriba de la cámara
            Punto3D adelante = (ObjetivoCamara - PosicionCamara).VectorNormalizado();
            Punto3D derecha = Punto3D.ProductoCruz(adelante, UpCamara).VectorNormalizado();
            Punto3D arriba = Punto3D.ProductoCruz(derecha, adelante).VectorNormalizado();

            double factor = DistanciaCamara * 0.001;
            ObjetivoCamara = ObjetivoCamara + (derecha * deltaX * factor) + (arriba * deltaY * factor);

            ActualizarPosicionCamara();
        }

        #region Transformaciones

        public Punto3D Trasladar(Punto3D punto, double tx, double ty, double tz)
        {
            return new Punto3D(punto.X + tx, punto.Y + ty, punto.Z + tz);
        }

        public Punto3D Escalar(Punto3D punto, Punto3D centro, double sx, double sy, double sz)
        {
            double x = centro.X + (punto.X - centro.X) * sx;
            double y = centro.Y + (punto.Y - centro.Y) * sy;
            double z = centro.Z + (punto.Z - centro.Z) * sz;
            return new Punto3D(x, y, z);
        }

        public Punto3D RotarX(Punto3D punto, Punto3D centro, double angulo)
        {
            double rad = angulo * Math.PI / 180.0;
            double cos = Math.Cos(rad);
            double sin = Math.Sin(rad);

            double y = punto.Y - centro.Y;
            double z = punto.Z - centro.Z;

            return new Punto3D(
                punto.X,
                centro.Y + y * cos - z * sin,
                centro.Z + y * sin + z * cos
            );
        }

        public Punto3D RotarY(Punto3D punto, Punto3D centro, double angulo)
        {
            double rad = angulo * Math.PI / 180.0;
            double cos = Math.Cos(rad);
            double sin = Math.Sin(rad);

            double x = punto.X - centro.X;
            double z = punto.Z - centro.Z;

            return new Punto3D(
                centro.X + x * cos + z * sin,
                punto.Y,
                centro.Z - x * sin + z * cos
            );
        }

        public Punto3D RotarZ(Punto3D punto, Punto3D centro, double angulo)
        {
            double rad = angulo * Math.PI / 180.0;
            double cos = Math.Cos(rad);
            double sin = Math.Sin(rad);

            double x = punto.X - centro.X;
            double y = punto.Y - centro.Y;

            return new Punto3D(
                centro.X + x * cos - y * sin,
                centro.Y + x * sin + y * cos,
                punto.Z
            );
        }

        public void AplicarTransformaciones(Figura3D figura)
        {
            if (figura.VerticesOriginales.Count == 0)
                return;

            Punto3D centro = new Punto3D(0, 0, 0);

            for (int i = 0; i < figura.Vertices.Count; i++)
            {
                Punto3D p = figura.VerticesOriginales[i].Clone();

                // Aplicar escala
                p = Escalar(p, centro, figura.Escala.X, figura.Escala.Y, figura.Escala.Z);

                // Aplicar rotaciones
                p = RotarX(p, centro, figura.Rotacion.X);
                p = RotarY(p, centro, figura.Rotacion.Y);
                p = RotarZ(p, centro, figura.Rotacion.Z);

                // Aplicar traslación
                p = Trasladar(p, figura.Posicion.X, figura.Posicion.Y, figura.Posicion.Z);

                figura.Vertices[i] = p;
            }
        }

        #endregion

        #region Proyección y Renderizado

        public PointF ProyectarPunto(Punto3D punto)
        {
            // Matriz de vista (view matrix)
            Punto3D z = (PosicionCamara - ObjetivoCamara).VectorNormalizado();
            Punto3D x = Punto3D.ProductoCruz(UpCamara, z).VectorNormalizado();
            Punto3D y = Punto3D.ProductoCruz(z, x);

            // Transformar punto al espacio de la cámara
            Punto3D puntoRelativo = punto - PosicionCamara;
            
            double xe = Punto3D.ProductoPunto(puntoRelativo, x);
            double ye = Punto3D.ProductoPunto(puntoRelativo, y);
            double ze = Punto3D.ProductoPunto(puntoRelativo, z);

            // Proyección perspectiva
            if (ze >= -PlanosCercano)
            {
                ze = -PlanosCercano - 0.01; // Evitar división por cero
            }

            double fov = CampoVision * Math.PI / 180.0;
            double d = 1.0 / Math.Tan(fov / 2.0);

            double xp = (-xe * d) / (-ze);
            double yp = (-ye * d) / (-ze);

            // Convertir a coordenadas de pantalla
            float screenX = (float)((xp + 1) * AnchoVista / 2);
            float screenY = (float)((1 - yp / AspectRatio) * AltoVista / 2);

            return new PointF(screenX, screenY);
        }

        public void DibujarFigura(Graphics g, Figura3D figura)
        {
            if (!figura.Visible || figura.Vertices.Count == 0)
                return;

            List<PointF> puntosProyectados = new List<PointF>();
            foreach (var vertice in figura.Vertices)
            {
                puntosProyectados.Add(ProyectarPunto(vertice));
            }

            // Dibujar caras con relleno si está habilitado
            if (figura.MostrarRelleno && figura.Caras.Count > 0)
            {
                using (Brush brush = new SolidBrush(figura.ColorRelleno))
                {
                    foreach (var cara in figura.Caras)
                    {
                        if (cara.Count >= 3)
                        {
                            PointF[] puntos = new PointF[cara.Count];
                            for (int i = 0; i < cara.Count; i++)
                            {
                                if (cara[i] < puntosProyectados.Count)
                                    puntos[i] = puntosProyectados[cara[i]];
                            }
                            
                            try
                            {
                                g.FillPolygon(brush, puntos);
                            }
                            catch { }
                        }
                    }
                }
            }

            // Dibujar aristas
            Pen pen = figura.Seleccionada 
                ? new Pen(Color.Yellow, 2) 
                : new Pen(figura.ColorLinea, 1);

            foreach (var arista in figura.Aristas)
            {
                if (arista.Inicio < puntosProyectados.Count && 
                    arista.Fin < puntosProyectados.Count)
                {
                    try
                    {
                        g.DrawLine(pen, 
                            puntosProyectados[arista.Inicio], 
                            puntosProyectados[arista.Fin]);
                    }
                    catch { }
                }
            }

            pen.Dispose();
        }

        public void DibujarEjes(Graphics g, double longitud = 2)
        {
            Punto3D origen = new Punto3D(0, 0, 0);
            Punto3D ejeX = new Punto3D(longitud, 0, 0);
            Punto3D ejeY = new Punto3D(0, longitud, 0);
            Punto3D ejeZ = new Punto3D(0, 0, longitud);

            PointF pOrigen = ProyectarPunto(origen);
            PointF pX = ProyectarPunto(ejeX);
            PointF pY = ProyectarPunto(ejeY);
            PointF pZ = ProyectarPunto(ejeZ);

            // Eje X - Rojo
            g.DrawLine(new Pen(Color.Red, 2), pOrigen, pX);
            // Eje Y - Verde
            g.DrawLine(new Pen(Color.Lime, 2), pOrigen, pY);
            // Eje Z - Azul
            g.DrawLine(new Pen(Color.Blue, 2), pOrigen, pZ);
        }

        public void DibujarGrid(Graphics g, int tamaño = 10, double espaciado = 1)
        {
            Pen penGrid = new Pen(Color.FromArgb(50, 255, 255, 255), 1);

            for (int i = -tamaño; i <= tamaño; i++)
            {
                double pos = i * espaciado;
                
                // Líneas en X
                PointF p1 = ProyectarPunto(new Punto3D(-tamaño * espaciado, 0, pos));
                PointF p2 = ProyectarPunto(new Punto3D(tamaño * espaciado, 0, pos));
                try { g.DrawLine(penGrid, p1, p2); } catch { }

                // Líneas en Z
                p1 = ProyectarPunto(new Punto3D(pos, 0, -tamaño * espaciado));
                p2 = ProyectarPunto(new Punto3D(pos, 0, tamaño * espaciado));
                try { g.DrawLine(penGrid, p1, p2); } catch { }
            }

            penGrid.Dispose();
        }

        #endregion
    }
}
