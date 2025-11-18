using Figuras_Dos_Y_Seis;
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
    public partial class MenuProyecto : Form
    {
        public MenuProyecto()
        {
            InitializeComponent();
        }

        private void CerrarFormulariosHijos()
        {
            foreach (Form frm in this.MdiChildren)
            {
                frm.Close();
            }
        }

        private void y8PuntasToolStripMenuItem_Click(object sender, EventArgs e)
        {
            CerrarFormulariosHijos();
            frmPoligonoEstrellado frmPoligonoEstrellado = frmPoligonoEstrellado.Instancia;
            frmPoligonoEstrellado.MdiParent = this;
            frmPoligonoEstrellado.Show();

        }

        private void pentagonoToolStripMenuItem_Click(object sender, EventArgs e)
        {
            CerrarFormulariosHijos();
            frmHexagono frmHexagono = frmHexagono.Instancia;
            frmHexagono.MdiParent = this;
            frmHexagono.Show();
        }

        private void puntasYGema10LadosToolStripMenuItem_Click(object sender, EventArgs e)
        {
            CerrarFormulariosHijos();
            frmGema10Lados frmGema10Lados= frmGema10Lados.Instancia;
            frmGema10Lados.MdiParent = this;
            frmGema10Lados.Show();
        }

        private void puntasToolStripMenuItem_Click(object sender, EventArgs e)
        {
            CerrarFormulariosHijos();
            frmTrianguloEstrella frmTrianguloEstrella = frmTrianguloEstrella.Instancia;
            frmTrianguloEstrella.MdiParent = this;
            frmTrianguloEstrella.Show();
        }

        private void puntasYPentagonosToolStripMenuItem_Click(object sender, EventArgs e)
        {
            CerrarFormulariosHijos();
            Frm_Figura_2 frm_Figura_2 = Frm_Figura_2.Instancia;
            frm_Figura_2.MdiParent = this;
            frm_Figura_2.Show();
        }

        private void floresToolStripMenuItem_Click(object sender, EventArgs e)
        {
            CerrarFormulariosHijos();
            Frm_Figura_6 frm_Figura_6 = Frm_Figura_6.Instancia;
            frm_Figura_6.MdiParent = this;
            frm_Figura_6.Show();
        }
    }
}
