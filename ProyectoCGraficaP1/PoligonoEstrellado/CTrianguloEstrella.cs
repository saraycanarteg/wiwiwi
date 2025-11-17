using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace PoligonoEstrellado
{
    internal class CTrianguloEstrella
    {
        private double mradio;

        public CTrianguloEstrella()
        {
            mradio = 0.0f;
        }

        public void ReadData(TextBox txtRadio)
        {
            try
            {
                mradio = float.Parse(txtRadio.Text);
            }
            catch
            {
                MessageBox.Show("Ingreso no válido...", "Mensaje de error");
            }
        }
        private PointF[] CalcularPuntas(Transformacion trans, double centroX, double centroY)
        {
            PointF[] puntas = new PointF[8];
            double radioEscalado = mradio * trans.Escala;
            double anguloBase = Math.PI / 4;

            for (int i = 0; i < 8; i++)
            {
                double ang = anguloBase * i;
                double x0 = radioEscalado * Math.Cos(ang);
                double y0 = radioEscalado * Math.Sin(ang);

                trans.AplicarTransformacionCompleta(x0, y0, centroX, centroY,
                    out double xFinal, out double yFinal);

                puntas[i] = new PointF((float)xFinal, (float)yFinal);
            }

            return puntas;
        }

        public double CalcularAreaEstrella(double radioEscalado)
        {
            // Fórmula: Área = 4√2 * r^2
            return 4 * Math.Sqrt(2) * Math.Pow(radioEscalado, 2);
        }
        public double CalcularPerimetroEstrella(Transformacion trans, double centroX, double centroY)
        {
            // Obtener coordenadas de los 8 vértices externos
            PointF[] puntas = CalcularPuntas(trans, centroX, centroY);

            double perimetro = 0.0;

            for (int i = 0; i < 8; i++)
            {
                int siguiente = (i + 1) % 8;

                double dx = puntas[siguiente].X - puntas[i].X;
                double dy = puntas[siguiente].Y - puntas[i].Y;

                perimetro += Math.Sqrt(dx * dx + dy * dy);
            }

            return perimetro;
        }

        private void DibujarCoordenadasExternas(Graphics g, PointF[] puntas)
        {
            using (Font fuente = new Font("Arial", 8))
            using (Brush brush = new SolidBrush(Color.Red))
            {
                for (int i = 0; i < puntas.Length; i++)
                {
                    string coord = $"({puntas[i].X:F1}, {puntas[i].Y:F1})";

                    // Dibujar texto cerca del punto
                    g.DrawString(coord, fuente, brush,
                        puntas[i].X + 5,
                        puntas[i].Y + 5);
                }
            }
        }

        public void DibujarTrianguloEstrella(PictureBox picBox, Transformacion trans)
        {
            if (mradio <= 0)
            {
                MessageBox.Show("Ingrese un radio mayor a 0", "Aviso");
                return;
            }

            double centroX = picBox.Width / 2;
            double centroY = picBox.Height / 2;

            picBox.Refresh();
            Graphics g = picBox.CreateGraphics();
            g.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.AntiAlias;
            g.Clear(Color.White);

            double radioEscalado = mradio * trans.Escala;
            float centroTransX = (float)(centroX + trans.OffsetX);
            float centroTransY = (float)(centroY + trans.OffsetY);

            PointF[] puntas = CalcularPuntas(trans, centroX, centroY);

            Pen lapizBlanco = new Pen(Color.Blue, 2);
            Pen lapizPunteado = new Pen(Color.Blue, 2)
            {
                DashStyle = System.Drawing.Drawing2D.DashStyle.Dash
            };

            // Círculo guía
            g.DrawEllipse(lapizPunteado,
                centroTransX - (float)radioEscalado,
                centroTransY - (float)radioEscalado,
                (float)radioEscalado * 2, (float)radioEscalado * 2);

            // Conectar puntas pares
            for (int i = 0; i < 4; i++)
                g.DrawLine(lapizBlanco, puntas[i * 2], puntas[((i + 1) * 2) % 8]);

            // Conectar puntas impares
            for (int i = 0; i < 4; i++)
                g.DrawLine(lapizBlanco, puntas[i * 2 + 1], puntas[((i + 1) * 2 + 1) % 8]);

            // Líneas cruzadas
            for (int i = 0; i < 8; i++)
            {
                int adelante = (i + 3) % 8;
                int atras = (i - 3 + 8) % 8;

                g.DrawLine(lapizBlanco, puntas[i], puntas[adelante]);
                g.DrawLine(lapizBlanco, puntas[i], puntas[atras]);
            }

            DibujarCoordenadasExternas(g, puntas);
        }
    }
}
