using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace PoligonoEstrellado
{
    public partial class frmHexagono : Form
    {
        private static frmHexagono instancia;

        public static frmHexagono Instancia
        {
            get
            {
                if (instancia == null || instancia.IsDisposed)
                    instancia = new frmHexagono();
                return instancia;
            }
        }

        private Transformacion transformacion;
        private double altura;
        private Hexagono hexagono;
        private bool modoRotacion = false;
        private bool modoTraslacion = false;
        public frmHexagono()
        {
            InitializeComponent();
            transformacion = new Transformacion();
            this.KeyPreview = true;
            this.KeyDown += new KeyEventHandler(Form_KeyDown);
            transformacion = new Transformacion();

            btnDibujar.TabStop = false;
            btnRotar.TabStop = false;
            btnTrasladar.TabStop = false;
            btnDetener.TabStop = false;
            btnReset.TabStop = false;
        }

        public void dibujarHexagono()
        {
            Hexagono hexagono = new Hexagono();

            try
            {
                altura = double.Parse(txtAltura.Text);
            }
            catch
            {
                MessageBox.Show("Ingrese un número válido", "Mensaje de error");
            }

            double centroX = picCanvas.Width / 2;
            double centroY = picCanvas.Height / 2;

            picCanvas.Refresh();

            Graphics g = picCanvas.CreateGraphics();
            g.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.AntiAlias;

            hexagono.Dibujarhexagono(g, altura, transformacion, (float)centroX, (float)centroY,
                        checkCoordenadas.Checked);


            g.Dispose();
            double alturaEscalada = altura * transformacion.Escala;
            txtArea.Text = hexagono.CalcularArea(alturaEscalada).ToString("F2");
            txtPerimetro.Text = hexagono.CalcularPerimetro(alturaEscalada).ToString("F2");
        }
        private void btnDibujar_Click(object sender, EventArgs e)
        {
            dibujarHexagono();
        }

        private void checkCoordenadas_CheckedChanged(object sender, EventArgs e)
        {
            if (!string.IsNullOrEmpty(txtAltura.Text))
            {
                dibujarHexagono();
            }
        }

        private void tkbEscala_Scroll(object sender, EventArgs e)
        {
            if (picCanvas != null)
            {
                transformacion.Escala = tkbEscala.Value;
                dibujarHexagono();
            }
        }

        private void Form_KeyDown(object sender, KeyEventArgs e)
        {
            if (string.IsNullOrEmpty(txtAltura.Text))
                return;

            bool teclaProcesada = false;

            if (modoRotacion)
            {
                if (e.KeyCode == Keys.Left)
                {
                    transformacion.RotarIzquierda(5);
                    dibujarHexagono();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Right)
                {
                    transformacion.RotarDerecha(5);
                    dibujarHexagono();
                    teclaProcesada = true;
                }
            }
            else if (modoTraslacion)
            {
                if (e.KeyCode == Keys.Left)
                {
                    transformacion.TrasladarIzquierda(5);
                    dibujarHexagono();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Right)
                {
                    transformacion.TrasladarDerecha(5);
                    dibujarHexagono();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Up)
                {
                    transformacion.TrasladarArriba(5);
                    dibujarHexagono();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Down)
                {
                    transformacion.TrasladarAbajo(5);
                    dibujarHexagono();
                    teclaProcesada = true;
                }

                if (teclaProcesada)
                {
                    e.Handled = true;
                    e.SuppressKeyPress = true;
                }
            }
        }

        private void btnReset_Click(object sender, EventArgs e)
        {
            picCanvas.Refresh();
            txtAltura.Clear();
            transformacion.Reset();
            tkbEscala.Value = 1;
            modoRotacion = false;
            modoTraslacion = false;
            btnRotar.Enabled = true;
            btnTrasladar.Enabled = true;
            btnDibujar.Enabled = true;
            tkbEscala.Enabled = true;
        }

        private void btnRotar_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtAltura.Text))
            {
                MessageBox.Show("Primero dibuje la flor", "Aviso");
                return;
            }

            modoRotacion = true;
            modoTraslacion = false;

            tkbEscala.Enabled = false;
            btnTrasladar.Enabled = false;
            btnDibujar.Enabled = false;
            btnRotar.Enabled = false;
            this.Focus();

        }

        private void btnDetener_Click(object sender, EventArgs e)
        {
            modoRotacion = false;
            modoTraslacion = false;
            btnRotar.Enabled = true;
            btnTrasladar.Enabled = true;
            btnDibujar.Enabled = true;
            tkbEscala.Enabled = true;
        }

        private void btnTrasladar_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtAltura.Text))
            {
                MessageBox.Show("Primero dibuje la flor", "Aviso");
                return;
            }

            modoTraslacion = true;
            modoRotacion = false;

            tkbEscala.Enabled = false;
            btnRotar.Enabled = false;
            btnDibujar.Enabled = false;
            btnTrasladar.Enabled = false;
            this.Focus();

        }
    }
}
