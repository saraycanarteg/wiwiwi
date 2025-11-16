using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using static System.Windows.Forms.MonthCalendar;

namespace PoligonoEstrellado
{
    internal class Hexagono
    {
        public double CalcularArea(double altura)
        {
            // Área de un hexágono regular: (3 * √3 / 2) * lado²
            // En un hexágono regular, el lado es igual al radio (altura)
            double area = (3 * Math.Sqrt(3) / 2) * Math.Pow(altura, 2);
            return area;
        }

        public double CalcularPerimetro(double altura)
        {
            // Perímetro de un hexágono regular: 6 * lado
            // El lado es igual al radio (altura)
            double perimetro = 6 * altura;
            return perimetro;
        }

        private double angulo = 2 * Math.PI / 6;

        private PointF CalcularVertice(double radio, int k)
        {
            double ang = (angulo * k) + (3 * Math.PI / 2);
            float x = (float)(radio * Math.Cos(ang));
            float y = (float)(radio * Math.Sin(ang));
            return new PointF(x, y);
        }

        public PointF[] CalcularCoordenadasExternas(double altura, Transformacion trans,
                                                     float centroX, float centroY)
        {
            PointF[] coordenadas = new PointF[6];

            for (int i = 0; i < 6; i++)
            {
                PointF p = CalcularVertice(altura, i);
                coordenadas[i] = trans.TransformarPunto(p, centroX, centroY);
            }

            return coordenadas;
        }

        public PointF[] CalcularCoordenadasMediasInternas(double altura, Transformacion trans,
                                                          float centroX, float centroY)
        {
            PointF[] coordenadasMediasInternas = new PointF[6];
            double radiom = altura / 2;

            for (int i = 0; i < 6; i++)
            {
                PointF p = CalcularVertice(radiom, i);
                coordenadasMediasInternas[i] = trans.TransformarPunto(p, centroX, centroY);
            }

            return coordenadasMediasInternas;
        }

        public PointF[] CalcularCoordenadasMediasExternas(double altura, Transformacion trans,
                                                          float centroX, float centroY)
        {
            PointF[] coordenadasMediasExternas = new PointF[6];
            PointF[] coordenadasExternas = CalcularCoordenadasExternas(altura, trans, centroX, centroY);

            for (int i = 0; i < 6; i++)
            {
                int siguiente = (i + 1) % 6;
                coordenadasMediasExternas[i] = new PointF(
                    (coordenadasExternas[i].X + coordenadasExternas[siguiente].X) / 2.0f,
                    (coordenadasExternas[i].Y + coordenadasExternas[siguiente].Y) / 2.0f
                );
            }

            return coordenadasMediasExternas;
        }

        public PointF[,,] CalcularCoordenadasSegmentos(double altura, Transformacion trans,
                                                       float centroX, float centroY)
        {
            PointF[,,] coordenadasSegmentos = new PointF[6, 4, 6];
            PointF[] coordenadasMediasInternas = CalcularCoordenadasMediasInternas(altura, trans, centroX, centroY);

            double radio = altura / 2;
            double paso = radio / 5;

            for (int hex = 0; hex < 6; hex++)
            {
                float centroMiniX = coordenadasMediasInternas[hex].X;
                float centroMiniY = coordenadasMediasInternas[hex].Y;

                for (int j = 1; j < 5; j++)
                {
                    double radioSegmento = radio - (paso * j);

                    for (int vertice = 0; vertice < 6; vertice++)
                    {
                        PointF p = CalcularVertice(radioSegmento, vertice);
                        PointF rotado = trans.AplicarRotacion(p);

                        coordenadasSegmentos[hex, j - 1, vertice] = new PointF(
                            centroMiniX + rotado.X,
                            centroMiniY + rotado.Y
                        );
                    }
                }
            }

            return coordenadasSegmentos;
        }

        public void Dibujarhexagono(Graphics g, double altura, Transformacion trans,
                                    float centroX, float centroY, bool mostrarCoordenadas = false)
        {
            float alturaEscala = (float)(altura * trans.Escala);

            PointF[] coordenadas = CalcularCoordenadasExternas(alturaEscala, trans, centroX, centroY);
            PointF[] coordenadasMediasInternas = CalcularCoordenadasMediasInternas(alturaEscala, trans, centroX, centroY);
            PointF[] coordenadasMediasExternas = CalcularCoordenadasMediasExternas(alturaEscala, trans, centroX, centroY);
            PointF[,,] segmentos = CalcularCoordenadasSegmentos(alturaEscala, trans, centroX, centroY);

            PointF centroTransformado = new PointF(
                centroX + trans.OffsetX,
                centroY + trans.OffsetY
            );

            DibujarLineas(g, coordenadas, coordenadasMediasInternas, coordenadasMediasExternas,
                         segmentos, centroTransformado, mostrarCoordenadas);
        }

        private void DibujarLineas(Graphics g, PointF[] coordenadas, PointF[] coordenadasMediasInternas,
                                   PointF[] coordenadasMediasExternas, PointF[,,] segmentos,
                                   PointF centro, bool mostrarCoordenadas)
        {
            using (Pen lapizRed = new Pen(Color.Black, 1))
            {
                // Líneas desde centro a puntos medios internos
                for (int i = 0; i < 6; i++)
                {
                    g.DrawLine(lapizRed, coordenadasMediasInternas[i], centro);
                }

                // Perímetro exterior
                for (int i = 0; i < 6; i++)
                {
                    int siguiente = (i + 1) % 6;
                    g.DrawLine(lapizRed, coordenadas[i], coordenadas[siguiente]);
                }

                // Líneas entre puntos medios internos y externos
                for (int i = 0; i < 6; i++)
                {
                    int siguiente = (i + 1) % 6;
                    g.DrawLine(lapizRed, coordenadasMediasInternas[siguiente],
                              coordenadasMediasExternas[i]);
                }

                // Dibujar segmentos internos
                for (int miniHex = 0; miniHex < 6; miniHex++)
                {
                    int ladoOmitido1 = (miniHex + 3) % 6;
                    int ladoOmitido2 = (miniHex + 4) % 6;

                    for (int seg = 0; seg < 4; seg++)
                    {
                        for (int vertice = 0; vertice < 6; vertice++)
                        {
                            if (vertice == ladoOmitido1 || vertice == ladoOmitido2)
                                continue;

                            int siguiente = (vertice + 1) % 6;
                            g.DrawLine(lapizRed,
                                segmentos[miniHex, seg, vertice],
                                segmentos[miniHex, seg, siguiente]);
                        }
                    }
                }
            }

            if (mostrarCoordenadas)
            {
                DibujarCoordenadas(g, coordenadas, centro);
            }
        }

        private void DibujarCoordenadas(Graphics g, PointF[] coordenadas, PointF centro)
        {
            using (Font fuente = new Font("Arial", 8))
            using (Brush brush = new SolidBrush(Color.Blue))
            {
                // Dibujar coordenadas de los vértices externos
                for (int i = 0; i < coordenadas.Length; i++)
                {
                    string coord = $"({coordenadas[i].X:F1}, {coordenadas[i].Y:F1})";

                    // Calcular dirección desde el centro hacia el vértice
                    float dx = coordenadas[i].X - centro.X;
                    float dy = coordenadas[i].Y - centro.Y;
                    float distancia = (float)Math.Sqrt(dx * dx + dy * dy);

                    if (distancia > 0)
                    {
                        float offsetX = (dx / distancia) * 20; // 20 píxeles hacia afuera
                        float offsetY = (dy / distancia) * 20;

                        g.DrawString(coord, fuente, brush,
                            coordenadas[i].X + offsetX,
                            coordenadas[i].Y + offsetY);
                    }
                }

                string coordCentro = $"C({centro.X:F1}, {centro.Y:F1})";
                SizeF tamanio = g.MeasureString(coordCentro, fuente);
                g.DrawString(coordCentro, fuente, brush,
                    centro.X - tamanio.Width / 2,
                    centro.Y - tamanio.Height - 10);
            }
        }
    }
}
