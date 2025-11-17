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
    public partial class frmGema10Lados : Form
    {
        private Transformacion transformacion;
        private CGema10 CGema10;
        private bool modoRotacion = false;
        private bool modoTraslacion = false;
        private static frmGema10Lados instancia;
        public static frmGema10Lados Instancia
        {
            get
            {
                if (instancia == null || instancia.IsDisposed)
                    instancia = new frmGema10Lados();
                return instancia;
            }
        }

        public frmGema10Lados()
        {
            InitializeComponent();
            this.KeyPreview = true;
            this.KeyDown += new KeyEventHandler(frmGema10Lados_KeyDown);
            transformacion = new Transformacion();
            CGema10 = new CGema10();

            btnDibujar.TabStop = false;
            btnRotar.TabStop = false;
            btnTrasladar.TabStop = false;
            btnDetener.TabStop = false;
            btnReset.TabStop = false;
        }

        private void DibujarFigura()
        {
            CGema10.DibujarFiguraGema(picBox, transformacion);

        }

        private void btnRotar_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtRadio.Text))
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

        private void btnDibujar_Click(object sender, EventArgs e)
        {

            CGema10.ReadData(txtRadio);
            CGema10.DibujarFiguraGema(picBox, transformacion);

        }

        private void frmGema10Lados_KeyDown(object sender, KeyEventArgs e)
        {
            if (string.IsNullOrEmpty(txtRadio.Text))
                return;

            bool teclaProcesada = false;

            if (modoRotacion)
            {
                if (e.KeyCode == Keys.Left)
                {
                    transformacion.RotarIzquierda(5);
                    DibujarFigura();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Right)
                {
                    transformacion.RotarDerecha(5);
                    DibujarFigura();
                    teclaProcesada = true;
                }
            }
            else if (modoTraslacion)
            {
                if (e.KeyCode == Keys.Left)
                {
                    transformacion.TrasladarIzquierda(5);
                    DibujarFigura();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Right)
                {
                    transformacion.TrasladarDerecha(5);
                    DibujarFigura();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Up)
                {
                    transformacion.TrasladarArriba(5);
                    DibujarFigura();
                    teclaProcesada = true;
                }
                else if (e.KeyCode == Keys.Down)
                {
                    transformacion.TrasladarAbajo(5);
                    DibujarFigura();
                    teclaProcesada = true;
                }
            }

            if (teclaProcesada)
            {
                e.Handled = true;
                e.SuppressKeyPress = true;
            }

        }

        private void btnReset_Click(object sender, EventArgs e)
        {
            picBox.Refresh();
            txtRadio.Clear();
            transformacion.Reset();
            tkbEscala.Value = 1;
            modoRotacion = false;
            modoTraslacion = false;
            btnRotar.Enabled = true;
            btnTrasladar.Enabled = true;
            btnDibujar.Enabled = true;
            tkbEscala.Enabled = true;

            CGema10 = new CGema10();
        }

        private void tkbEscala_Scroll(object sender, EventArgs e)
        {
            if (picBox != null && !string.IsNullOrEmpty(txtRadio.Text))
            {
                transformacion.Escala = tkbEscala.Value / 10.0;
                DibujarFigura();
            }
        }

        private void btnTrasladar_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtRadio.Text))
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
            btnDetener.Enabled = true;
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
    }
}
