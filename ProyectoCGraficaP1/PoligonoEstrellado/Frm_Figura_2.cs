using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Figuras_Dos_Y_Seis
{
    public partial class Frm_Figura_2 : Form
    {
        private Figura_2 figura;
        private float trasX = 0f;
        private float trasY = 0f;
        private float escala = 1f;

        private static Frm_Figura_2 instancia;

        public static Frm_Figura_2 Instancia
        {
            get
            {
                if (instancia == null || instancia.IsDisposed)
                    instancia = new Frm_Figura_2();
                return instancia;
            }
        }

        public Frm_Figura_2()
        {
            InitializeComponent();
            groupBox1.Anchor = (AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Bottom);
            groupBox2.Anchor = (AnchorStyles.Top | AnchorStyles.Right | AnchorStyles.Left | AnchorStyles.Bottom);
            picGrafico.Anchor = (AnchorStyles.Top | AnchorStyles.Right | AnchorStyles.Left | AnchorStyles.Bottom);

            // Recibir eventos de teclado en el formulario
            this.KeyPreview = true;
            this.KeyDown += Form1_KeyDown;
        }

        private void btnDibujar_Click(object sender, EventArgs e)
        {
            double altura = 0;
            if (!double.TryParse(txtAltura.Text, out altura))
            {
                MessageBox.Show("Altura inválida. Ingrese un número.");
                return;
            }

            figura = new Figura_2(picGrafico);
            figura.setAux(altura);

            // Aplicar transformaciones iniciales según controles
            figura.setRotacion(tkbRotar2.Value);

            float s = 1f + (tkbEscalar2.Value * 0.1f);
            if (s <= 0.1f) s = 0.1f;
            escala = s;
            figura.setEscalado(escala);

            figura.setTraslacion(trasX, trasY);

            // Dibujar figura y sus coordenadas (mostradas usando la misma transform)
            figura.dibujarFigura(true);

            // Mostrar coordenadas transformadas en el textbox
            if (txtCoordinates != null && figura != null)
            {
                txtCoordinates.Text = figura.GetCoordinatesString();
            }

            // Asegurar foco para capturar flechas
            this.Focus();
        }

        private void tkbRotar2_Scroll(object sender, EventArgs e)
        {
            if (figura == null) return;
            figura.setRotacion(tkbRotar2.Value);
            figura.dibujarFigura(true);

            if (txtCoordinates != null)
            {
                txtCoordinates.Text = figura.GetCoordinatesString();
            }
        }

        private void tkbEscalar2_Scroll(object sender, EventArgs e)
        {
            if (figura == null) return;
            float s = 1f + (tkbEscalar2.Value * 0.1f);
            if (s <= 0.1f) s = 0.1f;
            escala = s;
            figura.setEscalado(escala);
            figura.dibujarFigura(true);

            if (txtCoordinates != null)
            {
                txtCoordinates.Text = figura.GetCoordinatesString();
            }
        }

        private void Form1_KeyDown(object sender, KeyEventArgs e)
        {
            if (figura == null) return;

            const int paso = 10; // pixeles por pulsación
            bool manejado = false;

            if (e.KeyCode == Keys.Left)
            {
                trasX -= paso; manejado = true;
            }
            else if (e.KeyCode == Keys.Right)
            {
                trasX += paso; manejado = true;
            }
            else if (e.KeyCode == Keys.Up)
            {
                trasY -= paso; manejado = true;
            }
            else if (e.KeyCode == Keys.Down)
            {
                trasY += paso; manejado = true;
            }

            if (manejado)
            {
                figura.setTraslacion(trasX, trasY);
                figura.dibujarFigura(true);

                if (txtCoordinates != null)
                {
                    txtCoordinates.Text = figura.GetCoordinatesString();
                }

                e.Handled = true;
                e.SuppressKeyPress = true;
            }
        }

        private void btnReset_Click(object sender, EventArgs e)
        {
            try
            {
                tkbRotar2.Value = 0;
                tkbEscalar2.Value = 0;
                escala = 1f;

                if (figura != null)
                {
                    figura.setRotacion(0);
                    figura.setEscalado(escala);
                    figura.dibujarFigura(true);

                    if (txtCoordinates != null)
                    {
                        txtCoordinates.Text = figura.GetCoordinatesString();
                    }

                    picGrafico.Refresh();
                    picGrafico.BackColor = Color.White;
                }
                else
                {
                    picGrafico.Refresh();
                    picGrafico.BackColor = Color.White;
                }
            }
            catch
            {
                picGrafico.Refresh();
                picGrafico.BackColor = Color.White;
            }
        }

    }
}
