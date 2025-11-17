using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace PoligonoEstrellado
{
    internal class CGema10
    {
        private double mradio;

        public CGema10()
        {
            mradio = 0.0;
        }

        public void ReadData(TextBox txtRadio)
        {
            try
            {
                mradio = double.Parse(txtRadio.Text);
            }
            catch
            {
                MessageBox.Show("Ingreso no válido...", "Mensaje de error");
            }
        }
        // ================================
        //  CÁLCULO DE ÁREA Y PERÍMETRO
        // ================================
        public double CalcularPerimetro(double radioEscalado)
        {
            return 10 * radioEscalado; // si es decágono regular
        }

        public double CalcularArea(double radioEscalado)
        {
            return (10 * radioEscalado * radioEscalado) / (4 * Math.Tan(Math.PI / 10));
        }

        // -------------------------------
        // POLÍGONOS DEL BORDE - DECÁGONOS
        // -------------------------------
        private PointF[] CalcularPoligonoCircunscrito(
            Transformacion trans, double centroX, double centroY,
            int lados, double radio)
        {
            PointF[] pts = new PointF[lados];
            double radioEsc = radio * trans.Escala;
            double anguloBase = (2 * Math.PI) / lados;

            for (int i = 0; i < lados; i++)
            {
                double ang = anguloBase * i - Math.PI / 2.0;

                double x0 = radioEsc * Math.Cos(ang);
                double y0 = radioEsc * Math.Sin(ang);

                trans.AplicarTransformacionCompleta(x0, y0, centroX, centroY,
                    out double xFinal, out double yFinal);

                pts[i] = new PointF((float)xFinal, (float)yFinal);
            }
            return pts;
        }

        // -------------------------------
        // ESTRELLA DE 10 PUNTAS INTERNA
        // -------------------------------
        private PointF[] CalcularEstrella10(
            Transformacion trans, double centroX, double centroY,
            double R, double r)
        {
            int n = 20; // 10 puntos externos + 10 internos
            PointF[] pts = new PointF[n];

            double angBase = (2 * Math.PI) / n;
            double ang0 = -Math.PI / 2.0; 

            for (int i = 0; i < n; i++)
            {
                double ang = ang0 + angBase * i;
                double radioActual = (i % 2 == 0) ? R : r;

                double x0 = (radioActual * trans.Escala) * Math.Cos(ang);
                double y0 = (radioActual * trans.Escala) * Math.Sin(ang);

                trans.AplicarTransformacionCompleta(x0, y0, centroX, centroY,
                    out double xf, out double yf);

                pts[i] = new PointF((float)xf, (float)yf);
            }
            return pts;
        }

        private void DibujarEstrella(PointF[] ptsInternos, Graphics g, Pen lapiz)
        {
            for (int i = 0; i < ptsInternos.Length; i++)
            {
                PointF p1 = ptsInternos[i];
                PointF p2 = ptsInternos[(i + 1) % ptsInternos.Length];
                g.DrawLine(lapiz, p1, p2);
            }
        }

        // -----------------------------------
        // CONECTAR VÉRTICES INTERNOS DE LA ESTRELLA AL CÍRCULO INTERNO
        // -----------------------------------
        private void ConectarVerticesInternosAlCirculo(
            PointF[] ptsEstrella, Graphics g, Pen lapiz, PointF centro, float radio)
        {
            for (int i = 1; i < ptsEstrella.Length; i += 2)
            {
                PointF p = ptsEstrella[i];

                float dx = p.X - centro.X;
                float dy = p.Y - centro.Y;
                double dist = Math.Sqrt(dx * dx + dy * dy);
                if (dist < 0.0001) continue;

                float nx = (float)(dx / dist);
                float ny = (float)(dy / dist);

                PointF puntoEnBorde = new PointF(
                    centro.X + nx * radio,
                    centro.Y + ny * radio
                );

                g.DrawLine(lapiz, p, puntoEnBorde);
            }
        }

        // -----------------------------
        // DIBUJAR BORDES (2 DECÁGONOS)
        // -----------------------------
        private double DibujarBorde(Graphics g, Pen lapiz, Transformacion trans, PictureBox picBox)
        {
            int lados = 10;
            double radioExterior = mradio;
            double radioInterior = mradio * 0.90;

            double centroX = picBox.Width / 2.0;
            double centroY = picBox.Height / 2.0;

            PointF[] ptsExt = CalcularPoligonoCircunscrito(
                trans, centroX, centroY, lados, radioExterior);

            PointF[] ptsInt = CalcularPoligonoCircunscrito(
                trans, centroX, centroY, lados, radioInterior);

            // dibuja el borde exterior
            for (int i = 0; i < lados; i++)
                g.DrawLine(lapiz, ptsExt[i], ptsExt[(i + 1) % lados]);

            // borde interior
            for (int i = 0; i < lados; i++)
                g.DrawLine(lapiz, ptsInt[i], ptsInt[(i + 1) % lados]);

            // conexiones entre vértices de ambos bordes
            for (int i = 0; i < lados; i++)
                g.DrawLine(lapiz, ptsExt[i], ptsInt[i]);
            return radioInterior;

        }
        // DIBUJAR Y INFERIOR EN LAS SECCIONES PARES DE LA ESTRELLA (PARTIENDO DEL CÍRCULO INTERNO)
        private void DibujarLineasInternasEnV(PointF[] ptsEstrella, Graphics g, Pen lapiz,
            PointF centro, float radioCentro)
        {
            int n = ptsEstrella.Length;
            if (n < 4) return;

            int m = n / 2;
            PointF[] internos = new PointF[m];
            PointF[] puntosBorde = new PointF[m];
            int idx = 0;
            for (int i = 1; i < n; i += 2)
            {
                PointF p = ptsEstrella[i];
                float dx = p.X - centro.X;
                float dy = p.Y - centro.Y;
                double d = Math.Sqrt(dx * dx + dy * dy);
                if (d < 1e-6) d = 1e-6;
                float ux = (float)(dx / d);
                float uy = (float)(dy / d);
                internos[idx] = p;
                puntosBorde[idx] = new PointF(centro.X + ux * radioCentro, centro.Y + uy * radioCentro);
                idx++;
            }

            const float stemGapFrac = 0.5f;
            const float minApexOffset = 2f; 
            const float minLen2 = 0.5f * 0.5f;

            for (int i = 0; i < m; i++)
            {
                if ((i % 2) != 0) continue;

                int j = i;
                int k = (i + 1) % m;

                PointF pj = internos[j];
                PointF pk = internos[k];
                PointF pbj = puntosBorde[j];
                PointF pbk = puntosBorde[k];

                // vectores unitarios desde el centro hacia pj y pk
                float dxj = pj.X - centro.X;
                float dyj = pj.Y - centro.Y;
                float dxk = pk.X - centro.X;
                float dyk = pk.Y - centro.Y;
                double dj = Math.Sqrt(dxj * dxj + dyj * dyj);
                double dk = Math.Sqrt(dxk * dxk + dyk * dyk);
                if (dj < 1e-6 || dk < 1e-6) continue;
                float ujx = (float)(dxj / dj), ujy = (float)(dyj / dj);
                float ukx = (float)(dxk / dk), uky = (float)(dyk / dk);

                // bisector entre las dos direcciones internas (dirección del tallo)
                float bx = ujx + ukx;
                float by = ujy + uky;
                double bnorm = Math.Sqrt(bx * bx + by * by);
                if (bnorm < 1e-6)
                {
                    bx = -ujy;
                    by = ujx;
                    bnorm = Math.Sqrt(bx * bx + by * by);
                    if (bnorm < 1e-6) continue;
                }
                float ubx = (float)(bx / bnorm);
                float uby = (float)(by / bnorm);

                PointF puntoEnCirculo = new PointF(centro.X + ubx * radioCentro, centro.Y + uby * radioCentro);

                // puntos destino: punto medio de cada segmento (interno -> puntoEnBorde)
                PointF midJ = new PointF((pj.X + pbj.X) * 0.5f, (pj.Y + pbj.Y) * 0.5f);
                PointF midK = new PointF((pk.X + pbk.X) * 0.5f, (pk.Y + pbk.Y) * 0.5f);

                // distancia mínima entre midpoints y centro
                double midJDist = Math.Sqrt((midJ.X - centro.X) * (midJ.X - centro.X) + (midJ.Y - centro.Y) * (midJ.Y - centro.Y));
                double midKDist = Math.Sqrt((midK.X - centro.X) * (midK.X - centro.X) + (midK.Y - centro.Y) * (midK.Y - centro.Y));
                double minMidDist = Math.Min(midJDist, midKDist);

                // gap entre círculo y midpoints
                double gap = minMidDist - radioCentro;
                float actualStemLen;
                if (gap <= minApexOffset)
                {
                    // si no hay gap suficiente, usa un tallo corto proporcional al radioCentro
                    actualStemLen = Math.Max(minApexOffset, radioCentro * 0.12f);
                }
                else
                {
                    // posiciona el apex como una fracción del gap (escala con la estrella)
                    actualStemLen = (float)(gap * stemGapFrac);
                }

                // limitar apex para que no sobrepase los midpoints
                float apexRad = (float)Math.Min(minMidDist - 1.0, radioCentro + actualStemLen);
                if (apexRad < radioCentro + minApexOffset) apexRad = radioCentro + minApexOffset;

                PointF apex = new PointF(centro.X + ubx * apexRad, centro.Y + uby * apexRad);

                // dibujar tallo desde el perímetro hacia afuera hasta el apex
                g.DrawLine(lapiz, puntoEnCirculo, apex);

                // dibujar ramas de la Y desde el apex hacia los midpoints 
                float d1x = midJ.X - apex.X, d1y = midJ.Y - apex.Y;
                float d2x = midK.X - apex.X, d2y = midK.Y - apex.Y;
                if (d1x * d1x + d1y * d1y >= minLen2) g.DrawLine(lapiz, apex, midJ);
                if (d2x * d2x + d2y * d2y >= minLen2) g.DrawLine(lapiz, apex, midK);
            }
        }

        // ------------------------------------------------------
        // DIBUJAR ROMBOS DE LAS PUNTAS Y CONEXIONES HACIA EL CÍRCULO INTERNO
        // ------------------------------------------------------
        private void DibujarTrianguloYConexiones(Graphics g, Pen lapiz, PointF pExt,
            PointF pLeftInt, PointF pRightInt, PointF midLineaIntermedia,
            out PointF pTriLeft, out PointF pTriRight)
        {
            float anchoTriangulo = 0.5f;

            pTriLeft = new PointF(
                midLineaIntermedia.X + (pLeftInt.X - midLineaIntermedia.X) * anchoTriangulo,
                midLineaIntermedia.Y + (pLeftInt.Y - midLineaIntermedia.Y) * anchoTriangulo
            );

            pTriRight = new PointF(
                midLineaIntermedia.X + (pRightInt.X - midLineaIntermedia.X) * anchoTriangulo,
                midLineaIntermedia.Y + (pRightInt.Y - midLineaIntermedia.Y) * anchoTriangulo
            );

            // Líneas desde vértices internos a vértices externos del triángulo
            g.DrawLine(lapiz, pLeftInt, pTriLeft);
            g.DrawLine(lapiz, pRightInt, pTriRight);

            // Dibujar triángulo exterior
            g.DrawLine(lapiz, pExt, pTriLeft);
            g.DrawLine(lapiz, pExt, pTriRight);
        }


        private void DibujarVHaciaCentroYLineasCirculo(Graphics g, Pen lapiz,
            PointF pLeftInt, PointF pRightInt, PointF pTriLeft, PointF pTriRight,
            PointF centro, float radioCentro, float distPuntaALinea)
        {
            float dxLeft = centro.X - pLeftInt.X;
            float dyLeft = centro.Y - pLeftInt.Y;
            float distLeft = (float)Math.Sqrt(dxLeft * dxLeft + dyLeft * dyLeft);

            float dxRight = centro.X - pRightInt.X;
            float dyRight = centro.Y - pRightInt.Y;
            float distRight = (float)Math.Sqrt(dxRight * dxRight + dyRight * dyRight);

            if (distLeft > 1e-6 && distRight > 1e-6)
            {
                dxLeft /= distLeft; dyLeft /= distLeft;
                dxRight /= distRight; dyRight /= distRight;

                PointF pVLeftEnd = new PointF(
                    pLeftInt.X + dxLeft * distPuntaALinea,
                    pLeftInt.Y + dyLeft * distPuntaALinea
                );

                PointF pVRightEnd = new PointF(
                    pRightInt.X + dxRight * distPuntaALinea,
                    pRightInt.Y + dyRight * distPuntaALinea
                );

                PointF apexVCentro = new PointF(
                    (pVLeftEnd.X + pVRightEnd.X) * 0.5f,
                    (pVLeftEnd.Y + pVRightEnd.Y) * 0.5f
                );

                g.DrawLine(lapiz, pLeftInt, apexVCentro);
                g.DrawLine(lapiz, pRightInt, apexVCentro);
                g.DrawLine(lapiz, pTriLeft, apexVCentro);
                g.DrawLine(lapiz, pTriRight, apexVCentro);

                // Líneas desde apexVCentro hacia el círculo interno
                float dxApex = centro.X - apexVCentro.X;
                float dyApex = centro.Y - apexVCentro.Y;
                float distApex = (float)Math.Sqrt(dxApex * dxApex + dyApex * dyApex);

                if (distApex > 1e-6)
                {
                    dxApex /= distApex;
                    dyApex /= distApex;

                    // Punto en el borde del círculo
                    PointF puntoEnCirculo = new PointF(
                        centro.X - dxApex * radioCentro,
                        centro.Y - dyApex * radioCentro
                    );

                    // Línea principal desde apexVCentro hasta el círculo
                    g.DrawLine(lapiz, apexVCentro, puntoEnCirculo);

                    // Punto medio izquierdo (entre apexVCentro y pLeftInt)
                    PointF midLeft = new PointF(
                        (apexVCentro.X + pLeftInt.X) * 0.5f,
                        (apexVCentro.Y + pLeftInt.Y) * 0.5f
                    );

                    // Punto medio derecho (entre apexVCentro y pRightInt)
                    PointF midRight = new PointF(
                        (apexVCentro.X + pRightInt.X) * 0.5f,
                        (apexVCentro.Y + pRightInt.Y) * 0.5f
                    );

                    // Calcular puntos en el círculo para los puntos medios
                    float dxMidLeft = centro.X - midLeft.X;
                    float dyMidLeft = centro.Y - midLeft.Y;
                    float distMidLeft = (float)Math.Sqrt(dxMidLeft * dxMidLeft + dyMidLeft * dyMidLeft);

                    float dxMidRight = centro.X - midRight.X;
                    float dyMidRight = centro.Y - midRight.Y;
                    float distMidRight = (float)Math.Sqrt(dxMidRight * dxMidRight + dyMidRight * dyMidRight);

                    if (distMidLeft > 1e-6)
                    {
                        dxMidLeft /= distMidLeft;
                        dyMidLeft /= distMidLeft;
                        PointF puntoCirculoLeft = new PointF(
                            centro.X - dxMidLeft * radioCentro,
                            centro.Y - dyMidLeft * radioCentro
                        );
                        g.DrawLine(lapiz, midLeft, puntoCirculoLeft);
                    }

                    if (distMidRight > 1e-6)
                    {
                        dxMidRight /= distMidRight;
                        dyMidRight /= distMidRight;
                        PointF puntoCirculoRight = new PointF(
                            centro.X - dxMidRight * radioCentro,
                            centro.Y - dyMidRight * radioCentro
                        );
                        g.DrawLine(lapiz, midRight, puntoCirculoRight);
                    }
                }
            }
        }

        // DIBUJAR ROMBOS DOBLES
        private void DibujarRombosDobles(
            PointF[] ptsEstrella, Graphics g, Pen lapiz,
            PointF centro, float radioCentro)
        {
            int n = ptsEstrella.Length; // 20 puntos
            if (n < 4) return;

            int m = n / 2; // 10 secciones
            PointF[] internos = new PointF[m];
            PointF[] puntosBorde = new PointF[m];

            // Extraer puntos internos (índices impares de ptsEstrella)
            int idx = 0;
            for (int i = 1; i < n; i += 2)
            {
                PointF p = ptsEstrella[i];
                float dx = p.X - centro.X;
                float dy = p.Y - centro.Y;
                double d = Math.Sqrt(dx * dx + dy * dy);
                if (d < 1e-6) d = 1e-6;
                float ux = (float)(dx / d);
                float uy = (float)(dy / d);
                internos[idx] = p;
                puntosBorde[idx] = new PointF(centro.X + ux * radioCentro, centro.Y + uy * radioCentro);
                idx++;
            }

            // Dibujar estructuras solo en secciones pares (0, 2, 4, 6, 8)
            for (int i = 0; i < m; i++)
            {
                if ((i % 2) == 0) continue; 

                // Obtener la punta exterior de esta sección
                int extIndex = (2 * (i + 1)) % n;
                PointF pExt = ptsEstrella[extIndex];

                // Puntos internos adyacentes (vértices internos izquierdo y derecho)
                PointF pLeftInt = internos[i];
                PointF pRightInt = internos[(i + 1) % m];

                // Calcular punto medio (para referencia)
                PointF midLineaIntermedia = new PointF(
                    (pLeftInt.X + pRightInt.X) * 0.5f,
                    (pLeftInt.Y + pRightInt.Y) * 0.5f
                );

                // Dibujar triángulo y conexiones
                PointF pTriLeft, pTriRight;
                DibujarTrianguloYConexiones(g, lapiz, pExt, pLeftInt, pRightInt,
                    midLineaIntermedia, out pTriLeft, out pTriRight);

                // Calcular distancia de punta a línea intermedia
                float distPuntaALinea = (float)Math.Sqrt(
                    (pExt.X - midLineaIntermedia.X) * (pExt.X - midLineaIntermedia.X) +
                    (pExt.Y - midLineaIntermedia.Y) * (pExt.Y - midLineaIntermedia.Y)
                );

                // Dibujar V hacia el centro y líneas al círculo
                DibujarVHaciaCentroYLineasCirculo(g, lapiz, pLeftInt, pRightInt,
                    pTriLeft, pTriRight, centro, radioCentro, distPuntaALinea);
            }
        }

        private float Dist2(PointF a, PointF b)
        {
            float dx = a.X - b.X;
            float dy = a.Y - b.Y;
            return dx * dx + dy * dy;
        }

        // -------------------------------
        // DIBUJAR CONEXIONES DE LAS SECCIONES IMPARES DE LA ESTRELLA
        // -------------------------------
        private void DibujarVInvertidasDesdePunta(PointF[] ptsEstrella, Graphics g, Pen lapiz,
            PointF centro, float radioCentro)
        {
            int n = ptsEstrella.Length;
            if (n < 4) return;

            int m = n / 2;
            PointF[] internos = new PointF[m];
            PointF[] puntosBorde = new PointF[m];
            int idx = 0;
            for (int i = 1; i < n; i += 2)
            {
                PointF p = ptsEstrella[i];
                float dx = p.X - centro.X;
                float dy = p.Y - centro.Y;
                double d = Math.Sqrt(dx * dx + dy * dy);
                if (d < 1e-6) d = 1e-6;
                float ux = (float)(dx / d);
                float uy = (float)(dy / d);
                internos[idx] = p;
                puntosBorde[idx] = new PointF(centro.X + ux * radioCentro, centro.Y + uy * radioCentro);
                idx++;
            }
            const float downFrac = 0.18f;
            const float minLenSq = 0.5f * 0.5f;

            for (int i = 0; i < m; i++)
            {
                if ((i % 2) != 0) continue;
                PointF pj = internos[i];
                PointF pk = internos[(i + 1) % m];
                PointF pbj = puntosBorde[i];
                PointF pbk = puntosBorde[(i + 1) % m];

                int extIndex = (2 * (i + 1)) % n;
                PointF tip = ptsEstrella[extIndex];
                PointF targetJ = new PointF(
                    pj.X + (centro.X - pj.X) * downFrac,
                    pj.Y + (centro.Y - pj.Y) * downFrac
                );
                PointF targetK = new PointF(
                    pk.X + (centro.X - pk.X) * downFrac,
                    pk.Y + (centro.Y - pk.Y) * downFrac
                );
                if (Dist2(tip, targetJ) >= minLenSq) g.DrawLine(lapiz, tip, targetJ);
                if (Dist2(tip, targetK) >= minLenSq) g.DrawLine(lapiz, tip, targetK);

                PointF midJ = new PointF((pj.X + pbj.X) * 0.5f, (pj.Y + pbj.Y) * 0.5f);
                PointF midK = new PointF((pk.X + pbk.X) * 0.5f, (pk.Y + pbk.Y) * 0.5f);

                if (Dist2(tip, midJ) >= minLenSq) g.DrawLine(lapiz, tip, midJ);
                if (Dist2(tip, midK) >= minLenSq) g.DrawLine(lapiz, tip, midK);
            }
        }

        // ----------------------------
        // FUNCIÓN PRINCIPAL
        // ----------------------------
        public void DibujarFiguraGema(PictureBox picBox, Transformacion trans)
        {
            if (mradio <= 0)
            {
                MessageBox.Show("Ingrese un radio mayor a 0", "Aviso");
                return;
            }

            double cx = picBox.Width / 2.0;
            double cy = picBox.Height / 2.0;

            picBox.Refresh();

            using (Graphics g = picBox.CreateGraphics())
            {
                g.SmoothingMode = SmoothingMode.AntiAlias;
                g.InterpolationMode = InterpolationMode.HighQualityBicubic;
                g.PixelOffsetMode = PixelOffsetMode.HighQuality;

                g.Clear(Color.White);

                // --- 1. Estrella
                double radioInteriorEstrella = mradio * 0.75;
                PointF[] ptsEstrella = null;

                using (Pen lapiz = new Pen(Color.HotPink, 1.7f))
                {
                    // Dibujar el borde primero y obtener su radio interior
                    double radioInteriorDelBorde = DibujarBorde(g, lapiz, trans, picBox);
                    ptsEstrella = CalcularEstrella10(trans, cx, cy, radioInteriorDelBorde, radioInteriorEstrella);
                    // Dibujar la estrella encima del borde
                    DibujarEstrella(ptsEstrella, g, lapiz);
                }

                // --- 2. círculo interno
                float radioCentro = 12f * (float)trans.Escala;
                PointF centro = new PointF(
                    (float)(cx + trans.OffsetX),
                    (float)(cy + trans.OffsetY)
                );

                g.FillEllipse(Brushes.White,
                    centro.X - radioCentro,
                    centro.Y - radioCentro,
                    radioCentro * 2,
                    radioCentro * 2
                );

                g.DrawEllipse(Pens.HotPink,
                    centro.X - radioCentro,
                    centro.Y - radioCentro,
                    radioCentro * 2,
                    radioCentro * 2
                );

                // --- 3. conectar internos al círculo
                using (Pen lapiz2 = new Pen(Color.HotPink, 1.7f))
                {
                    ConectarVerticesInternosAlCirculo(
                        ptsEstrella, g, lapiz2, centro, radioCentro);
                }
                // --- 4. dibujar lineas de conexión y diseño gema
                using (Pen lapiz3 = new Pen(Color.HotPink, 1.7f))
                {
                    DibujarLineasInternasEnV(
                        ptsEstrella, g, lapiz3, centro, radioCentro);
                    DibujarVInvertidasDesdePunta(
                        ptsEstrella, g, lapiz3, centro, radioCentro);
                    DibujarRombosDobles(ptsEstrella, g, lapiz3, centro, radioCentro);

                }

            }

        }
    }
}