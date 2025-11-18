using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Figuras_Dos_Y_Seis
{
    internal class Figura_2
    {
        private double angulo = 2 * Math.PI / 5;
        private double centroX = 0;
        private double centroY = 0;
        private double aux = 0;

        private PictureBox picGrafico;
        private Graphics g;
        private Pen lapiz;
        Pen lapiz2 = new Pen(Color.Red, 3);

        private PointF[] puntosPrimeraEstrella = new PointF[5];
        private PointF[] puntosSegundaEstrella = new PointF[5];
        private PointF[] puntosTerceraEstrella = new PointF[5];
        private PointF[] puntosPrimerPentagono = new PointF[5];
        private PointF[] puntosSegundoPentagono = new PointF[5];
        private PointF[] puntosTercerPentagono = new PointF[5];

        // Transformaciones
        private float escalado = 1f;
        private float rotacionGrados = 0f;
        private float traslacionX = 0f;
        private float traslacionY = 0f;

        public Figura_2(PictureBox picGrafico)
        {
            this.picGrafico = picGrafico;
            this.g = picGrafico.CreateGraphics();
            this.lapiz = new Pen(Color.Blue, 3);

            this.centroX = picGrafico.Width / 2;
            this.centroY = picGrafico.Height / 2;
        }

        // Setters para transformaciones
        public void setRotacion(float grados)
        {
            this.rotacionGrados = grados;
        }
        public void setEscalado(float s)
        {
            if (s <= 0) return;
            this.escalado = s;
        }
        public void setTraslacion(float tx, float ty)
        {
            this.traslacionX = tx;
            this.traslacionY = ty;
        }

        // Setter para aux (altura de entrada)
        public void setAux(double a)
        {
            this.aux = a;
        }

        // Calcula coordenadas relativas (local) de un vértice con radio y angulo base
        public double calculoVerticeEstrellaX(double radio, int k, double ang)
        {
            double coordenada;
            coordenada = radio * (Math.Cos((angulo * k) + (ang)));
            return coordenada;
        }

        public double calculoVerticeEstrellaY(double radio, int k, double ang)
        {
            double coordenada;
            coordenada = radio * (Math.Sin((angulo * k) + (ang)));
            return coordenada;
        }

        // Aplicar transformaciones al Graphics: trasladar al centro + traslación, rotar, escalar
        private GraphicsState ApplyTransforms()
        {
            GraphicsState state = g.Save();
            g.TranslateTransform((float)centroX + traslacionX, (float)centroY + traslacionY);
            g.RotateTransform(rotacionGrados);
            g.ScaleTransform(escalado, escalado);
            return state;
        }
        private void RestoreTransforms(GraphicsState state)
        {
            g.Restore(state);
        }

        // Recalcula puntos en coordenadas locales (sin sumar centro)
        private void calcularPuntos()
        {
            double altura;

            altura = 50 + aux;
            for (int i = 0; i < 5; i++)
            {
                puntosPrimeraEstrella[i].X = (float)(calculoVerticeEstrellaX(altura, i, (3 * Math.PI / 2)));
                puntosPrimeraEstrella[i].Y = (float)(calculoVerticeEstrellaY(altura, i, (3 * Math.PI / 2)));
            }

            altura = 125 + aux;
            for (int i = 0; i < 5; i++)
            {
                puntosSegundaEstrella[i].X = (float)(calculoVerticeEstrellaX(altura, i, (Math.PI / 2)));
                puntosSegundaEstrella[i].Y = (float)(calculoVerticeEstrellaY(altura, i, (Math.PI / 2)));
            }

            altura = 90 + aux;
            for (int i = 0; i < 5; i++)
            {
                puntosTerceraEstrella[i].X = (float)(calculoVerticeEstrellaX(altura, i, (Math.PI / 2)));
                puntosTerceraEstrella[i].Y = (float)(calculoVerticeEstrellaY(altura, i, (Math.PI / 2)));
            }

            altura = 155 + aux;
            for (int i = 0; i < 5; i++)
            {
                puntosPrimerPentagono[i].X = (float)(calculoVerticeEstrellaX(altura, i, ((3 * Math.PI / 2))));
                puntosPrimerPentagono[i].Y = (float)(calculoVerticeEstrellaY(altura, i, ((3 * Math.PI / 2))));
            }

            altura = 210 + aux;
            for (int i = 0; i < 5; i++)
            {
                puntosSegundoPentagono[i].X = (float)(calculoVerticeEstrellaX(altura, i, (29 * Math.PI / 36)));
                puntosSegundoPentagono[i].Y = (float)(calculoVerticeEstrellaY(altura, i, (29 * Math.PI / 36)));
            }

            altura = 210 + aux;
            for (int i = 0; i < 5; i++)
            {
                puntosTercerPentagono[i].X = (float)(calculoVerticeEstrellaX(altura, i, (7 * Math.PI / 36)));
                puntosTercerPentagono[i].Y = (float)(calculoVerticeEstrellaY(altura, i, (7 * Math.PI / 36)));
            }
        }

        public void dibujarFigura(bool mostrarCoordenadas = false)
        {
            // Limpiar superficie
            this.g.Clear(Color.White);

            // Calcular puntos en coordenadas locales
            calcularPuntos();

            // Aplicar transformaciones y dibujar
            GraphicsState state = ApplyTransforms();

            dibujarPrimeraEstrella();
            dibujarSegundaEstrella();
            dibujarTerceraEstrella();
            dibujarPrimerPentagono();
            dibujarSegundoPentagono();
            dibujarTercerPentagono();
            dibujarTrazosInternosAPrimerPentagono();
            dibujarTrazosExternosAPrimerPentagono();

            RestoreTransforms(state);

            // Dibujar etiquetas de coordenadas en coordenadas de dispositivo si se solicita
            if (mostrarCoordenadas)
            {
                DibujarCoordenadas();
            }
        }

        public void dibujarPrimeraEstrella()
        {
            // puntosPrimeraEstrella ya están calculados en coordenadas locales
            dibujarLineasPrimeraEstrella();
        }

        public void dibujarLineasPrimeraEstrella()
        {
            int i, j;

            for (i = 0; i < 5; i++)
            {
                for (j = 0; j < 5; j++)
                {
                    if (((i + 2) % 5) == j)
                    {
                        g.DrawLine(lapiz, puntosPrimeraEstrella[i].X, puntosPrimeraEstrella[i].Y,
                            puntosPrimeraEstrella[j].X, puntosPrimeraEstrella[j].Y);
                    }
                }
            }

        }

        public void dibujarSegundaEstrella()
        {
            dibujarLineasSegundaEstrella();
        }

        public void dibujarLineasSegundaEstrella()
        {
            int i;

            for (i = 0; i < 5; i++)
            {
                g.DrawLine(lapiz, puntosPrimeraEstrella[i].X, puntosPrimeraEstrella[i].Y,
                        puntosSegundaEstrella[(i + 2) % 5].X, puntosSegundaEstrella[(i + 2) % 5].Y);
                g.DrawLine(lapiz, puntosPrimeraEstrella[i].X, puntosPrimeraEstrella[i].Y,
                        puntosSegundaEstrella[(i + 3) % 5].X, puntosSegundaEstrella[(i + 3) % 5].Y);
            }
        }

        public void dibujarTerceraEstrella()
        {
            dibujarLineasTerceraEstrella();

        }

        public void dibujarLineasTerceraEstrella()
        {
            int i;

            for (i = 0; i < 5; i++)
            {
                g.DrawLine(lapiz, puntosPrimeraEstrella[i].X, puntosPrimeraEstrella[i].Y,
                        puntosTerceraEstrella[(i + 2) % 5].X, puntosTerceraEstrella[(i + 2) % 5].Y);
                g.DrawLine(lapiz, puntosPrimeraEstrella[i].X, puntosPrimeraEstrella[i].Y,
                        puntosTerceraEstrella[(i + 3) % 5].X, puntosTerceraEstrella[(i + 3) % 5].Y);
            }
        }

        public void dibujarTrazosInternosAPrimerPentagono()
        {
            for (int i = 0; i < 5; i++)
            {
                g.DrawLine(lapiz, puntosPrimerPentagono[i].X, puntosPrimerPentagono[i].Y,
                    puntosSegundaEstrella[((i + 1) % 5)].X, puntosSegundaEstrella[((i + 1) % 5)].Y);
                g.DrawLine(lapiz, puntosPrimerPentagono[i].X, puntosPrimerPentagono[i].Y,
                    puntosSegundaEstrella[((i + 4) % 5)].X, puntosSegundaEstrella[((i + 4) % 5)].Y);
            }
        }

        public void dibujarTrazosExternosAPrimerPentagono()
        {

            for (int i = 0; i < 5; i++)
            {
                g.DrawLine(lapiz, puntosPrimerPentagono[i].X, puntosPrimerPentagono[i].Y,
                    puntosSegundoPentagono[((i + 1) % 5)].X, puntosSegundoPentagono[((i + 1) % 5)].Y);
                g.DrawLine(lapiz, puntosPrimerPentagono[i].X, puntosPrimerPentagono[i].Y,
                    puntosTercerPentagono[((i + 4) % 5)].X, puntosTercerPentagono[((i + 4) % 5)].Y);
            }
        }

        public void dibujarPrimerPentagono()
        {
            g.DrawPolygon(lapiz, puntosPrimerPentagono);
        }

        public void dibujarSegundoPentagono()
        {
            g.DrawPolygon(lapiz, puntosSegundoPentagono);
        }

        public void dibujarTercerPentagono()
        {
            g.DrawPolygon(lapiz, puntosTercerPentagono);
        }

        public double calcularArea(double altura)
        {
            // Área total aproximada de la figura basada en la altura
            double areaEstrella = (5 * altura * altura * Math.Tan(Math.PI / 5)) / 4;
            double areaPentagono = (5 * altura * altura) / (4 * Math.Tan(Math.PI / 5));
            return areaEstrella + areaPentagono;
        }

        public double calcularPerimetro(double altura)
        {
            double ladoPentagono = 2 * (altura+210) * Math.Sin(Math.PI / 10);
            double perimetroPentagonoGrande = 5 * ladoPentagono;

            return (5 * ladoPentagono);
        }

        // Devuelve una representación en texto de las coordenadas actuales de los puntos,
        // ya transformadas por rotación, escala y traslación.
        public string GetCoordinatesString()
        {
            // Asegurar puntos actualizados
            calcularPuntos();

            // Clonar arrays para transformar
            PointF[] a1 = (PointF[])puntosPrimeraEstrella.Clone();
            PointF[] a2 = (PointF[])puntosSegundaEstrella.Clone();
            PointF[] a3 = (PointF[])puntosTerceraEstrella.Clone();
            PointF[] p1 = (PointF[])puntosPrimerPentagono.Clone();
            PointF[] p2 = (PointF[])puntosSegundoPentagono.Clone();
            PointF[] p3 = (PointF[])puntosTercerPentagono.Clone();

            // Build transform matrix using same order as drawing
            using (Matrix m = new Matrix())
            {
                m.Translate((float)centroX + traslacionX, (float)centroY + traslacionY);
                m.Rotate(rotacionGrados);
                m.Scale(escalado, escalado);

                m.TransformPoints(a1);
                m.TransformPoints(a2);
                m.TransformPoints(a3);
                m.TransformPoints(p1);
                m.TransformPoints(p2);
                m.TransformPoints(p3);
            }

            StringBuilder sb = new StringBuilder();
            sb.AppendLine("Primera Estrella:");
            for (int i = 0; i < a1.Length; i++) sb.AppendLine($"P{i}: ({a1[i].X:F1}, {a1[i].Y:F1})");
            sb.AppendLine();

            sb.AppendLine("Segunda Estrella:");
            for (int i = 0; i < a2.Length; i++) sb.AppendLine($"P{i}: ({a2[i].X:F1}, {a2[i].Y:F1})");
            sb.AppendLine();

            sb.AppendLine("Tercera Estrella:");
            for (int i = 0; i < a3.Length; i++) sb.AppendLine($"P{i}: ({a3[i].X:F1}, {a3[i].Y:F1})");
            sb.AppendLine();

            sb.AppendLine("Primer Pentágono:");
            for (int i = 0; i < p1.Length; i++) sb.AppendLine($"P{i}: ({p1[i].X:F1}, {p1[i].Y:F1})");
            sb.AppendLine();

            sb.AppendLine("Segundo Pentágono:");
            for (int i = 0; i < p2.Length; i++) sb.AppendLine($"P{i}: ({p2[i].X:F1}, {p2[i].Y:F1})");
            sb.AppendLine();

            sb.AppendLine("Tercer Pentágono:");
            for (int i = 0; i < p3.Length; i++) sb.AppendLine($"P{i}: ({p3[i].X:F1}, {p3[i].Y:F1})");

            return sb.ToString();
        }

        // Dibuja las coordenadas (etiquetas) directamente en un Graphics pasado como parámetro.
        public void DibujarCoordenadas()
        {
            if (g == null) return;

            // Asegurar puntos calculados y clonar
            calcularPuntos();
            PointF[] a1 = (PointF[])puntosPrimeraEstrella.Clone();
            PointF[] a2 = (PointF[])puntosSegundaEstrella.Clone();
            PointF[] a3 = (PointF[])puntosTerceraEstrella.Clone();
            PointF[] p1 = (PointF[])puntosPrimerPentagono.Clone();
            PointF[] p2 = (PointF[])puntosSegundoPentagono.Clone();
            PointF[] p3 = (PointF[])puntosTercerPentagono.Clone();

            // Transformar puntos usando la misma matriz que se aplica al dibujo
            using (Matrix m = new Matrix())
            {
                m.Translate((float)centroX + traslacionX, (float)centroY + traslacionY);
                m.Rotate(rotacionGrados);
                m.Scale(escalado, escalado);

                m.TransformPoints(a1);
                m.TransformPoints(a2);
                m.TransformPoints(a3);
                m.TransformPoints(p1);
                m.TransformPoints(p2);
                m.TransformPoints(p3);
            }

            // Determinar centro transformado
            PointF centroTransformado = new PointF((float)centroX + traslacionX, (float)centroY + traslacionY);

            using (Font fuente = new Font("Arial", 10, FontStyle.Bold))
            using (Brush brush = new SolidBrush(Color.Black))
            {
                // Función local para dibujar un arreglo de puntos con offsets
                Action<PointF[], string> dibujarArray = (arr, prefijo) =>
                {
                    for (int i = 0; i < arr.Length; i++)
                    {
                        string coord = $"{prefijo}{i}: ({arr[i].X:F1} {";"} {arr[i].Y:F1})";

                        float dx = arr[i].X - centroTransformado.X;
                        float dy = arr[i].Y - centroTransformado.Y;
                        float distancia = (float)Math.Sqrt(dx * dx + dy * dy);

                        float offsetX = 0f, offsetY = 0f;
                        if (distancia > 0)
                        {
                            offsetX = (dx / distancia) * 20f;
                            offsetY = (dy / distancia) * 20f;
                        }

                        g.DrawString(coord, fuente, brush, arr[i].X + offsetX, arr[i].Y + offsetY);
                    }
                };

                // Dibujar todas las series con prefijos para identificar
                dibujarArray(a1, "E1-");
                dibujarArray(a2, "E2-");
                dibujarArray(a3, "E3-");
                dibujarArray(p1, "P1-");
                dibujarArray(p2, "P2-");
                dibujarArray(p3, "P3-");

                
            }
        }

    }
}
