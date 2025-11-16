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
    }
}
