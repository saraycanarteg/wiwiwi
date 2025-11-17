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
    public partial class frmTrianguloEstrella : Form
    {
        private Transformacion transformacion;
        private CTrianguloEstrella CTrianguloEstrella;
        private bool modoRotacion = false;
        private bool modoTraslacion = false;
        private static frmTrianguloEstrella instancia;
        public static frmTrianguloEstrella Instancia
        {
            get
            {
                if (instancia == null || instancia.IsDisposed)
                    instancia = new frmTrianguloEstrella();
                return instancia;
            }
        }
        public frmTrianguloEstrella()
        {
            InitializeComponent();
            this.KeyPreview = true;
            this.KeyDown += new KeyEventHandler(frmTrianguloEstrella_KeyDown);
            transformacion = new Transformacion();
            CTrianguloEstrella = new CTrianguloEstrella();

            btnDibujar.TabStop = false;
            btnRotar.TabStop = false;
            btnTrasladar.TabStop = false;
            btnDetener.TabStop = false;
            btnReset.TabStop = false;
        }
        private void DibujarFigura()
        {
            CTrianguloEstrella.DibujarTrianguloEstrella(picBox, transformacion);

        }

        private void btnDibujar_Click(object sender, EventArgs e)
        {
            CTrianguloEstrella.ReadData(txtRadio);
            CTrianguloEstrella.DibujarTrianguloEstrella(picBox, transformacion);
        }

        private void frmTrianguloEstrella_KeyDown(object sender, KeyEventArgs e)
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

            CTrianguloEstrella = new CTrianguloEstrella();
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

        private void tkbEscala_Scroll(object sender, EventArgs e)
        {
            if (picBox != null && !string.IsNullOrEmpty(txtRadio.Text))
            {
                transformacion.Escala = tkbEscala.Value / 10.0;
                DibujarFigura();
            }
        }

        private void checkCoordenadas_CheckedChanged(object sender, EventArgs e)
        {
            if (!string.IsNullOrEmpty(txtRadio.Text))
            {
                DibujarFigura();
            }

        }
    }
}
