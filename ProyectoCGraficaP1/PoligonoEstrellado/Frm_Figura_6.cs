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
    public partial class Frm_Figura_6 : Form
    {
        private Simbolo_Afrodita sa;
        private float trasX = 0f;
        private float trasY = 0f;
        private float escala = 1f;

        private static Frm_Figura_6 instancia;
        public static Frm_Figura_6 Instancia
        {
            get
            {
                if (instancia == null || instancia.IsDisposed)
                    instancia = new Frm_Figura_6();
                return instancia;
            }
        }
        public Frm_Figura_6()
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
            float radio = 0f;
            if (!float.TryParse(txtAltura.Text, out radio))
            {
                MessageBox.Show("Altura inválida. Ingrese un número.");
                return;
            }

            sa = new Simbolo_Afrodita(picGrafico, radio);

            // Aplicar transformaciones iniciales según controles
            sa.setRotacion(tkbRotar.Value);

            float s = 1f + (tkbEscalar.Value * 0.1f);
            if (s <= 0.1f) s = 0.1f;
            escala = s;
            sa.setEscalado(escala);

            sa.setTraslacion(trasX, trasY);

            sa.dibujarSimboloAfrodita();

            // Asegurar foco para capturar flechas
            this.Focus();
        }

        private void tkbRotar_Scroll(object sender, EventArgs e)
        {
            if (sa == null) return;
            sa.setRotacion(tkbRotar.Value);
            sa.dibujarSimboloAfrodita();
        }

        private void tkbEscalar_Scroll(object sender, EventArgs e)
        {
            if (sa == null) return;
            float s = 1f + (tkbEscalar.Value * 0.1f);
            if (s <= 0.1f) s = 0.1f;
            escala = s;
            sa.setEscalado(escala);
            sa.dibujarSimboloAfrodita();

            // Actualizar área y perímetro usando el radio actual en txtAltura
            
        }

        private void Form1_KeyDown(object sender, KeyEventArgs e)
        {
            if (sa == null) return;

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
                sa.setTraslacion(trasX, trasY);
                sa.dibujarSimboloAfrodita();
                e.Handled = true;
                e.SuppressKeyPress = true;
            }
        }

        private void btnReset_Click(object sender, EventArgs e)
        {
            // Reset trackbars to their default positions
            try
            {
                // Reset rotation to 0
                tkbRotar.Value = 0;
                // Reset scale trackbar to 0 which maps to scale = 1.0f
                tkbEscalar.Value = 0;

                // Update internal state
                escala = 1f;

                if (sa != null)
                {
                    sa.setRotacion(0);
                    sa.setEscalado(escala);
                    // Do not change traslacion unless desired
                    sa.dibujarSimboloAfrodita();

                    
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
                // If trackbars are not available or some error occurs, just refresh the picture box
                picGrafico.Refresh();
            }
        }

    }
}
