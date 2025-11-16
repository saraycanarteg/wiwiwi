using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PoligonoEstrellado
{
    internal class Transformacion
    {
        public double Angulo { get; set; }
        public float OffsetX { get; set; }
        public float OffsetY { get; set; }
        public double Escala { get; set; }

        public Transformacion()
        {
            Angulo = 0;
            OffsetX = 0;
            OffsetY = 0;
            Escala = 1.0;
        }

        public PointF AplicarRotacion(PointF punto)
        {
            double rad = Angulo * Math.PI / 180.0;
            float xRot = (float)(punto.X * Math.Cos(rad) - punto.Y * Math.Sin(rad));
            float yRot = (float)(punto.X * Math.Sin(rad) + punto.Y * Math.Cos(rad));
            return new PointF(xRot, yRot);
        }

        public void AplicarRotacion(double x0, double y0, out double xRot, out double yRot)
        {
            double rad = Angulo * Math.PI / 180.0;
            xRot = x0 * Math.Cos(rad) - y0 * Math.Sin(rad);
            yRot = x0 * Math.Sin(rad) + y0 * Math.Cos(rad);
        }

        public PointF AplicarTraslacion(PointF punto, float centroX, float centroY)
        {
            return new PointF(
                punto.X + centroX + OffsetX,
                punto.Y + centroY + OffsetY
            );
        }

        public void AplicarTraslacion(double x, double y, double centroX, double centroY,
                                      out double xFinal, out double yFinal)
        {
            xFinal = x + centroX + OffsetX;
            yFinal = y + centroY + OffsetY;
        }

        public PointF TransformarPunto(PointF punto, float centroX, float centroY)
        {
            PointF rotado = AplicarRotacion(punto);
            return AplicarTraslacion(rotado, centroX, centroY);
        }

        public void AplicarTransformacionCompleta(double x0, double y0, double centroX, double centroY,
                                                  out double xFinal, out double yFinal)
        {
            AplicarRotacion(x0, y0, out double xRot, out double yRot);
            AplicarTraslacion(xRot, yRot, centroX, centroY, out xFinal, out yFinal);
        }

        public void RotarIzquierda(double incremento = 5)
        {
            Angulo -= incremento;
        }

        public void RotarDerecha(double incremento = 5)
        {
            Angulo += incremento;
        }

        public void TrasladarIzquierda(float paso = 5)
        {
            OffsetX -= paso;
        }

        public void TrasladarDerecha(float paso = 5)
        {
            OffsetX += paso;
        }

        public void TrasladarArriba(float paso = 5)
        {
            OffsetY -= paso;
        }

        public void TrasladarAbajo(float paso = 5)
        {
            OffsetY += paso;
        }

        public void Reset()
        {
            Angulo = 0;
            OffsetX = 0;
            OffsetY = 0;
            Escala = 1.0;
        }
    }
}
