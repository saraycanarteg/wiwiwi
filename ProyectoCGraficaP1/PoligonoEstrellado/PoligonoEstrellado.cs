using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PoligonoEstrellado
{
    internal class PoligonoEstrellado
    {
        public double CalcularArea(double altura)
        {
            // El polígono estrellado de 16 puntas se calcula como área del polígono regular de 16 lados
            // Fórmula: Área = (n * r² * sin(2π/n)) / 2
            // donde n = 16 y r = altura (radio circunscrito)
            int n = 16;
            double area = (n * Math.Pow(altura, 2) * Math.Sin(2 * Math.PI / n)) / 2;
            return area;
        }

        public double CalcularPerimetro(double altura)
        {
            // Calculamos la distancia entre dos vértices consecutivos del perímetro exterior (16 puntas)
            // Usamos la fórmula de distancia euclidiana entre puntos
            PointF p1 = CalcularVertice16(altura, 0);
            PointF p2 = CalcularVertice16(altura, 1);

            double lado = Math.Sqrt(Math.Pow(p2.X - p1.X, 2) + Math.Pow(p2.Y - p1.Y, 2));
            double perimetro = 16 * lado;

            return perimetro;
        }

        private double angulo16 = 2 * Math.PI / 16;
        private double angulo8 = 2 * Math.PI / 8;

        private PointF CalcularVertice(double radio, int k, double anguloBase)
        {
            double ang = (k * anguloBase) + (3 * Math.PI / 2);

            float x = (float)(radio * Math.Cos(ang));
            float y = (float)(radio * Math.Sin(ang));

            return new PointF(x, y);
        }

        private PointF CalcularVertice16(double radio, int k)
        {
            return CalcularVertice(radio, k, angulo16);
        }

        private PointF CalcularVertice8(double radio, int k)
        {
            return CalcularVertice(radio, k, angulo8);
        }

        public PointF[] CalcularExternas(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[16];

            for (int i = 0; i < 16; i++)
            {
                PointF p = CalcularVertice16(altura, i);
                pts[i] = trans.TransformarPunto(p, cx, cy);
            }

            return pts;
        }

        public PointF[] CalcularInternas(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[8];
            double radio = Math.Cos(Math.PI / 4) * altura;

            for (int i = 0; i < 8; i++)
            {
                PointF p = CalcularVertice8(radio, i);
                pts[i] = trans.TransformarPunto(p, cx, cy);
            }

            return pts;
        }

        public PointF[] CalcularInternas2(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[8];
            double radio = Math.Cos(Math.PI / 6) * altura;

            for (int i = 0; i < 8; i++)
            {
                PointF p = CalcularVertice8(radio, i);
                pts[i] = trans.TransformarPunto(p, cx, cy);
            }

            return pts;
        }

        public PointF[] CalcularInternas3(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[8];

            double radioExt = Math.Cos(Math.PI / 4) * altura;
            double radio = radioExt * Math.Sin(Math.PI / 4);

            for (int i = 0; i < 8; i++)
            {
                double ang = (i * angulo8) + (3 * Math.PI / 2) + (Math.PI / 8);
                float x = (float)(radio * Math.Cos(ang));
                float y = (float)(radio * Math.Sin(ang));

                pts[i] = trans.TransformarPunto(new PointF(x, y), cx, cy);
            }

            return pts;
        }

        public PointF[] CalcularInternas4(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[8];

            double radioExt = Math.Cos(Math.PI / 4) * altura;
            double radio = radioExt * Math.Cos(Math.PI / 6);

            for (int i = 0; i < 8; i++)
            {
                PointF p = CalcularVertice8(radio, i);
                pts[i] = trans.TransformarPunto(p, cx, cy);
            }

            return pts;
        }

        public PointF[] CalcularInternas5(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[8];
            double radioExt = Math.Cos(Math.PI / 4) * altura;
            double radio = radioExt * Math.Cos(Math.PI / 4) * Math.Cos(Math.PI / 4);

            for (int i = 0; i < 8; i++)
            {
                PointF p = CalcularVertice8(radio, i);
                pts[i] = trans.TransformarPunto(p, cx, cy);
            }

            return pts;
        }

        public PointF[] CalcularInternas6(double altura, Transformacion trans, float cx, float cy)
        {
            PointF[] pts = new PointF[8];
            double radioExt = Math.Cos(Math.PI / 4) * altura;
            double radio = radioExt * Math.Cos(Math.PI / 4) * Math.Cos(Math.PI / 3);

            for (int i = 0; i < 8; i++)
            {
                PointF p = CalcularVertice8(radio, i);
                pts[i] = trans.TransformarPunto(p, cx, cy);
            }

            return pts;
        }

        public void DibujarPoligonoEstrellado(Graphics g, double altura, Transformacion trans, float cx, float cy,
            bool mostrarCoordenadas = false)
        {
            float alt = (float)(altura * trans.Escala);

            PointF[] ext = CalcularExternas(alt, trans, cx, cy);
            PointF[] i1 = CalcularInternas(alt, trans, cx, cy);
            PointF[] i2 = CalcularInternas2(alt, trans, cx, cy);
            PointF[] i3 = CalcularInternas3(alt, trans, cx, cy);
            PointF[] i4 = CalcularInternas4(alt, trans, cx, cy);
            PointF[] i5 = CalcularInternas5(alt, trans, cx, cy);
            PointF[] i6 = CalcularInternas6(alt, trans, cx, cy);

            PointF centro = new PointF(cx + trans.OffsetX, cy + trans.OffsetY);
            DibujarLineas(g, ext, i1, i2, i3, i4, i5, i6, centro, mostrarCoordenadas);
        }

        private void DibujarLineas(Graphics g,
            PointF[] ext, PointF[] i1, PointF[] i2, PointF[] i3, PointF[] i4, PointF[] i5, PointF[] i6,
            PointF centro, bool mostrarCoordenadas)
        {
            using (Pen pen = new Pen(Color.Black, 1))
            {
                // Líneas al centro
                for (int i = 0; i < 16; i++)
                    g.DrawLine(pen, ext[i], centro);

                // Perímetro exterior
                for (int i = 0; i < 16; i++)
                    g.DrawLine(pen, ext[i], ext[(i + 1) % 16]);

                // Enlaces i1 ↔ exterior
                for (int i = 0; i < 8; i++)
                {
                    int externo = (i * 2 + 1) % 16;
                    int sig = (i + 1) % 8;

                    g.DrawLine(pen, i1[i], ext[externo]);
                    g.DrawLine(pen, ext[externo], i1[sig]);
                }

                // Repetir mismo patrón para i2
                for (int i = 0; i < 8; i++)
                {
                    int externo = (i * 2 + 1) % 16;
                    int sig = (i + 1) % 8;

                    g.DrawLine(pen, i2[i], ext[externo]);
                    g.DrawLine(pen, ext[externo], i2[sig]);
                }

                // Enlaces internos radiales
                DibujarInterno(g, pen, i1, i3);
                DibujarInterno(g, pen, i4, i3);
                DibujarInterno(g, pen, i5, i3);
                DibujarInterno(g, pen, i6, i3);
            }
            if (mostrarCoordenadas)
            {
                DibujarCoordenadas(g, ext, centro);
            }
        }

        private void DibujarInterno(Graphics g, Pen p, PointF[] a, PointF[] b)
        {
            for (int i = 0; i < 8; i++)
            {
                int sig = (i + 1) % 8;

                g.DrawLine(p, a[i], b[i]);
                g.DrawLine(p, b[i], a[sig]);
            }
        }

        private void DibujarCoordenadas(Graphics g, PointF[] coordenadas, PointF centro)
        {
            using (Font fuente = new Font("Arial", 8))
            using (Brush brush = new SolidBrush(Color.Blue))
            {
                // Dibujar coordenadas de los vértices externos (16 puntas)
                for (int i = 0; i < coordenadas.Length; i++)
                {
                    string coord = $"({coordenadas[i].X:F1}, {coordenadas[i].Y:F1})";

                    // Calcular dirección desde el centro hacia el vértice
                    float dx = coordenadas[i].X - centro.X;
                    float dy = coordenadas[i].Y - centro.Y;
                    float distancia = (float)Math.Sqrt(dx * dx + dy * dy);

                    // Normalizar y extender hacia afuera
                    if (distancia > 0)
                    {
                        float offsetX = (dx / distancia) * 25; // 25 píxeles hacia afuera
                        float offsetY = (dy / distancia) * 25;

                        g.DrawString(coord, fuente, brush,
                            coordenadas[i].X + offsetX,
                            coordenadas[i].Y + offsetY);
                    }
                }

                // Dibujar coordenadas del centro
                string coordCentro = $"C({centro.X:F1}, {centro.Y:F1})";
                SizeF tamanio = g.MeasureString(coordCentro, fuente);
                g.DrawString(coordCentro, fuente, brush,
                    centro.X - tamanio.Width / 2,
                    centro.Y - tamanio.Height - 10);
            }
        }
    }
}
