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
    internal class Simbolo_Afrodita
    {
        //Parámetros del círculo
        private int centrox;
        private int centroy;
        private float radio;

        //Parámetros del rectángulo
        private Rectangle rect;
        private int diferencialCentro;
        private int lado;

        //Parámetros de dibujo
        private Graphics g;
        private Pen pen = new Pen(Color.Blue, 2f);
        PictureBox picGrafico;

        //Vértices de los círculos que componen la figura principal (local coordinates)
        PointF[] verticesCirculo = new PointF[6];

        //Transformaciones
        float escalado=1.0f;
        float rotacionRad=0;
        float rotacionGrados = 0;
        float traslacionX=0;
        float traslacionY=0;

        public Simbolo_Afrodita(PictureBox picGrafico, float radio)
        {
            //Inicialización de parámetros
            this.centrox = picGrafico.Width/2;
            this.centroy = picGrafico.Height/2;
            this.radio = radio;
            this.diferencialCentro = (int)radio;
            this.lado = (int)radio*2;
            this.diferencialCentro = (int)radio;

            this.g = picGrafico.CreateGraphics();
            this.picGrafico = picGrafico;
            this.rect = new Rectangle(centrox - diferencialCentro, centroy - diferencialCentro, lado, lado);

            sacarVerticesLocal();
        }

        public void inicializarNuevamenteComponentesBase(float x, float y)
        {
            this.centrox = (int)x;
            this.centroy = (int)y;

            this.rect = new Rectangle(centrox - diferencialCentro, centroy - diferencialCentro, lado, lado);

            sacarVerticesLocal();
        }

        // Setters
        public void setRotacion(float grados)
        {
            float radianes = grados * (float)(Math.PI / 180);
            this.rotacionRad = radianes;
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

        // Calcula vértices en coordenadas locales (centro en 0,0)
        private void sacarVerticesLocal()
        {
            for (int i = 0; i < 6; i++)
            {
                double angulo = (i * 60 * Math.PI / 180) + (Math.PI/2);
                float x = (float)(radio * Math.Cos(angulo));
                float y = (float)(radio * Math.Sin(angulo));
                verticesCirculo[i] = new PointF(x, y);
            }
        }

        // Aplica transformaciones al objeto Graphics: translate a centro + traslación, luego rotar, luego escalar
        private GraphicsState ApplyTransforms()
        {
            GraphicsState state = g.Save();
            // Mover al centro donde se desea situar la figura (incluyendo traslación del usuario)
            g.TranslateTransform(centrox + traslacionX, centroy + traslacionY);
            // Aplicar rotación (grados)
            g.RotateTransform(rotacionGrados);
            // Aplicar escala (uniforme)
            g.ScaleTransform(escalado, escalado);

            return state;
        }

        private void RestoreTransforms(GraphicsState state)
        {
            g.Restore(state);
        }

        // Dibuja el círculo externo en coordenadas locales (centro en 0,0)
        private void DrawMainCircle()
        {
            g.DrawEllipse(pen, -radio, -radio, radio * 2, radio * 2);
        }

        // Dibuja pétalos alrededor de los vértices locales (verticesCirculo)
        private void DrawPetalos()
        {
            int ladoLocal = (int)(radio * 2);
            for (int i = 0; i < 6; i++)
            {
                var v = verticesCirculo[i];
                RectangleF petaloRect = new RectangleF(v.X - radio, v.Y - radio, radio * 2, radio * 2);
                // Al usar transform del Graphics, no sumamos rotacionGrados aquí
                g.DrawArc(pen, Rectangle.Round(petaloRect), 210 + (60 * i), 120);
            }
        }

        // Dibuja arcos faltantes con centros calculados localmente
        private void DrawArcosFaltantes()
        {
            PointF[] centros = new PointF[6];
            for (int i = 0; i < 6; i++)
            {
                // centro del subcírculo entre vértice i y vértice (i+1)
                int j = (i + 1) % 6;
                // vector desde origen al vértice j
                PointF vj = verticesCirculo[j];
                // centro relativo: verticesCirculo[i] + (vj normalized * radio)
                // pero vj already is at distance radio from origin; so unit vector of angle j is vj/radio
                PointF centro = new PointF(verticesCirculo[i].X + vj.X, verticesCirculo[i].Y + vj.Y);
                // El resultado tiene magnitud 2*radio*cos(30) etc, pero coincide con cálculo anterior
                centros[i] = centro;
            }

            for (int i = 0; i < 6; i++)
            {
                var c = centros[i];
                RectangleF rectArc = new RectangleF(c.X - radio, c.Y - radio, radio * 2, radio * 2);
                g.DrawArc(pen, Rectangle.Round(rectArc), 270 + (60 * i), 60);
            }
        }

        // Dibuja la "Flor de la Vida": en cada vértice dibuja un símbolo completo (círculo + pétalos + arcos faltantes)
        // Pero sin aplicar transforms adicionales; usamos las mismas funciones desplazando el origen temporalmente.
        private void DrawFlorDeLaVida()
        {
            foreach (var v in verticesCirculo)
            {
                GraphicsState s = g.Save();
                g.TranslateTransform(v.X, v.Y);
                // Dibuja el círculo y sus arcos/pétalos en este nuevo origen
                g.DrawEllipse(pen, -radio, -radio, radio * 2, radio * 2);

                // calcular vértices locales para subcírculo
                PointF[] subVerts = new PointF[6];
                for (int i = 0; i < 6; i++)
                {
                    double ang = (i * 60 * Math.PI / 180) + (Math.PI/2);
                    subVerts[i] = new PointF((float)(radio * Math.Cos(ang)), (float)(radio * Math.Sin(ang)));
                }

                // pétalos del subcírculo
                for (int i = 0; i < 6; i++)
                {
                    var sv = subVerts[i];
                    RectangleF petaloRect = new RectangleF(sv.X - radio, sv.Y - radio, radio * 2, radio * 2);
                    g.DrawArc(pen, Rectangle.Round(petaloRect), 210 + (60 * i), 120);
                }

                // arcos faltantes del subcírculo
                PointF[] centros = new PointF[6];
                for (int i = 0; i < 6; i++)
                {
                    int j = (i + 1) % 6;
                    PointF vj = subVerts[j];
                    centros[i] = new PointF(subVerts[i].X + vj.X, subVerts[i].Y + vj.Y);
                }
                for (int i = 0; i < 6; i++)
                {
                    var c = centros[i];
                    RectangleF rectArc = new RectangleF(c.X - radio, c.Y - radio, radio * 2, radio * 2);
                    g.DrawArc(pen, Rectangle.Round(rectArc), 270 + (60 * i), 60);
                }

                g.Restore(s);
            }
        }

        // Dibuja un círculo exterior discontinuo (dashed) que se adapta al tamaño de la figura
        private void DrawOuterDashedCircle()
        {
            using (Pen dashedPen = new Pen(Color.Blue, 3f))
            {
                dashedPen.DashStyle = DashStyle.Dot; // Estilo de línea discontinua
                g.DrawEllipse(dashedPen, (-2 * radio)-10, (-2 * radio)-10, (2 * radio * 2)+20, (2 * radio * 2)+20);
            }
        }

        public void dibujarSimboloAfrodita()
        {
            // Dibujo completo aplicando transform a Graphics para evitar deformaciones y superposiciones
            this.g.Clear(Color.White);

            GraphicsState state = ApplyTransforms();

            // Recalcular vértices locales por si radio cambió
            sacarVerticesLocal();

            DrawMainCircle();
            DrawPetalos();
            DrawArcosFaltantes();
            DrawFlorDeLaVida();
            DrawOuterDashedCircle(); // Llamar después de dibujar la flor, pero antes de restaurar transformación

            RestoreTransforms(state);

            // Dibujar etiquetas de coordenadas en color rojo (sin transformaciones en Graphics)
            DrawCoordinates();

            this.g.ResetClip();
        }

        // Construye una cadena con coordenadas transformadas (igual que las etiquetas en pantalla)
        public string GetCoordinatesString()
        {
            sacarVerticesLocal();

            // Clonar vértices locales
            PointF[] verts = (PointF[])verticesCirculo.Clone();
            PointF[] centros = new PointF[6];
            for (int i = 0; i < 6; i++)
            {
                int j = (i + 1) % 6;
                centros[i] = new PointF(verticesCirculo[i].X + verticesCirculo[j].X, verticesCirculo[i].Y + verticesCirculo[j].Y);
            }

            // Transform matrix (same order: translate -> rotate -> scale)
            using (Matrix m = new Matrix())
            {
                m.Translate(centrox + traslacionX, centroy + traslacionY);
                m.Rotate(rotacionGrados);
                m.Scale(escalado, escalado);
                m.TransformPoints(verts);
                m.TransformPoints(centros);
            }

            var sb = new StringBuilder();
            sb.AppendLine("Vertices circulo:");
            for (int i = 0; i < verts.Length; i++) sb.AppendLine($"V{i}: ({verts[i].X:F1}, {verts[i].Y:F1})");
            sb.AppendLine();
            sb.AppendLine("Centros subcirculos:");
            for (int i = 0; i < centros.Length; i++) sb.AppendLine($"C{i}: ({centros[i].X:F1}, {centros[i].Y:F1})");
            sb.AppendLine();
            sb.AppendLine($"Centro figura: ({centrox + traslacionX:F1}, {centroy + traslacionY:F1})");

            return sb.ToString();
        }

        // Dibuja las coordenadas (etiquetas) para los vértices y centros en color rojo, sin transformar el Graphics
        private void DrawCoordinates()
        {
            if (this.g == null) return;

            sacarVerticesLocal();

            PointF[] verts = (PointF[])verticesCirculo.Clone();
            PointF[] centros = new PointF[6];
            for (int i = 0; i < 6; i++)
            {
                int j = (i + 1) % 6;
                centros[i] = new PointF(verticesCirculo[i].X + verticesCirculo[j].X, verticesCirculo[i].Y + verticesCirculo[j].Y);
            }

            // Transform to device coordinates using same matrix as ApplyTransforms
            using (Matrix m = new Matrix())
            {
                m.Translate(centrox + traslacionX, centroy + traslacionY);
                m.Rotate(rotacionGrados);
                m.Scale(escalado, escalado);
                m.TransformPoints(verts);
                m.TransformPoints(centros);
            }

            PointF centroTrans = new PointF(centrox + traslacionX, centroy + traslacionY);

            using (Font fuente = new Font("Arial", 10, FontStyle.Bold))
            using (Brush brocha = new SolidBrush(Color.Black))
            {
                // Dibujar vértices del círculo
                for (int i = 0; i < verts.Length; i++)
                {
                    string text = $"V{i}: ({verts[i].X:F1}, {verts[i].Y:F1})";
                    float dx = verts[i].X - centroTrans.X;
                    float dy = verts[i].Y - centroTrans.Y;
                    float dist = (float)Math.Sqrt(dx * dx + dy * dy);
                    float ox = 0, oy = 0;
                    if (dist > 0) { ox = (dx / dist) * 20f; oy = (dy / dist) * 20f; }
                    g.DrawString(text, fuente, brocha, verts[i].X + ox, verts[i].Y + oy);
                }

                // Dibujar centros de subcírculos
                for (int i = 0; i < centros.Length; i++)
                {
                    string text = $"C{i}: ({centros[i].X:F1}, {centros[i].Y:F1})";
                    float dx = centros[i].X - centroTrans.X;
                    float dy = centros[i].Y - centroTrans.Y;
                    float dist = (float)Math.Sqrt(dx * dx + dy * dy);
                    float ox = 0, oy = 0;
                    if (dist > 0) { ox = (dx / dist) * 20f; oy = (dy / dist) * 20f; }
                    g.DrawString(text, fuente, brocha, centros[i].X + ox, centros[i].Y + oy);
                }

                // Dibujar centro de la figura
                string centroTxt = $"Cen: ({centroTrans.X:F1}, {centroTrans.Y:F1})";
                SizeF size = g.MeasureString(centroTxt, fuente);
                g.DrawString(centroTxt, fuente, brocha, centroTrans.X - size.Width / 2, centroTrans.Y - size.Height - 10);
            }
        }
    }
}
