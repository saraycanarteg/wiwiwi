namespace Figuras_Dos_Y_Seis
{
    partial class Frm_Figura_6
    {
        /// <summary>
        /// Variable del diseñador necesaria.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Limpiar los recursos que se estén usando.
        /// </summary>
        /// <param name="disposing">true si los recursos administrados se deben desechar; false en caso contrario.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Código generado por el Diseñador de Windows Forms

        /// <summary>
        /// Método necesario para admitir el Diseñador. No se puede modificar
        /// el contenido de este método con el editor de código.
        /// </summary>
        private void InitializeComponent()
        {
            this.btnDibujar = new System.Windows.Forms.Button();
            this.groupBox2 = new System.Windows.Forms.GroupBox();
            this.picGrafico = new System.Windows.Forms.PictureBox();
            this.groupBox3 = new System.Windows.Forms.GroupBox();
            this.txtAltura = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.btnReset = new System.Windows.Forms.Button();
            this.lblRotar = new System.Windows.Forms.Label();
            this.tkbRotar = new System.Windows.Forms.TrackBar();
            this.tkbEscalar = new System.Windows.Forms.TrackBar();
            this.lblEscalar = new System.Windows.Forms.Label();
            this.lblMover = new System.Windows.Forms.Label();
            this.groupBox1 = new System.Windows.Forms.GroupBox();
            this.groupBox2.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.picGrafico)).BeginInit();
            this.groupBox3.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbRotar)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscalar)).BeginInit();
            this.groupBox1.SuspendLayout();
            this.SuspendLayout();
            // 
            // btnDibujar
            // 
            this.btnDibujar.Location = new System.Drawing.Point(80, 121);
            this.btnDibujar.Name = "btnDibujar";
            this.btnDibujar.Size = new System.Drawing.Size(75, 23);
            this.btnDibujar.TabIndex = 4;
            this.btnDibujar.Text = "Dibujar";
            this.btnDibujar.UseVisualStyleBackColor = true;
            this.btnDibujar.Click += new System.EventHandler(this.btnDibujar_Click);
            // 
            // groupBox2
            // 
            this.groupBox2.Controls.Add(this.picGrafico);
            this.groupBox2.Location = new System.Drawing.Point(378, 12);
            this.groupBox2.Name = "groupBox2";
            this.groupBox2.Size = new System.Drawing.Size(595, 585);
            this.groupBox2.TabIndex = 2;
            this.groupBox2.TabStop = false;
            this.groupBox2.Text = "Gráfico";
            // 
            // picGrafico
            // 
            this.picGrafico.Location = new System.Drawing.Point(6, 21);
            this.picGrafico.Name = "picGrafico";
            this.picGrafico.Size = new System.Drawing.Size(592, 556);
            this.picGrafico.TabIndex = 0;
            this.picGrafico.TabStop = false;
            // 
            // groupBox3
            // 
            this.groupBox3.Controls.Add(this.txtAltura);
            this.groupBox3.Controls.Add(this.label1);
            this.groupBox3.Controls.Add(this.btnReset);
            this.groupBox3.Controls.Add(this.btnDibujar);
            this.groupBox3.Location = new System.Drawing.Point(12, 12);
            this.groupBox3.Name = "groupBox3";
            this.groupBox3.Size = new System.Drawing.Size(360, 170);
            this.groupBox3.TabIndex = 3;
            this.groupBox3.TabStop = false;
            this.groupBox3.Text = "Entradas";
            // 
            // txtAltura
            // 
            this.txtAltura.Location = new System.Drawing.Point(173, 65);
            this.txtAltura.Name = "txtAltura";
            this.txtAltura.Size = new System.Drawing.Size(160, 22);
            this.txtAltura.TabIndex = 7;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(15, 65);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(140, 16);
            this.label1.TabIndex = 6;
            this.label1.Text = "Altura desde el centro:";
            // 
            // btnReset
            // 
            this.btnReset.Location = new System.Drawing.Point(208, 121);
            this.btnReset.Name = "btnReset";
            this.btnReset.Size = new System.Drawing.Size(75, 23);
            this.btnReset.TabIndex = 5;
            this.btnReset.Text = "Resetear";
            this.btnReset.UseVisualStyleBackColor = true;
            this.btnReset.Click += new System.EventHandler(this.btnReset_Click);
            // 
            // lblRotar
            // 
            this.lblRotar.AutoSize = true;
            this.lblRotar.Location = new System.Drawing.Point(35, 44);
            this.lblRotar.MaximumSize = new System.Drawing.Size(0, 360);
            this.lblRotar.Name = "lblRotar";
            this.lblRotar.Size = new System.Drawing.Size(43, 16);
            this.lblRotar.TabIndex = 0;
            this.lblRotar.Text = "Rotar:";
            // 
            // tkbRotar
            // 
            this.tkbRotar.Location = new System.Drawing.Point(108, 44);
            this.tkbRotar.Maximum = 360;
            this.tkbRotar.Name = "tkbRotar";
            this.tkbRotar.Size = new System.Drawing.Size(225, 56);
            this.tkbRotar.TabIndex = 0;
            this.tkbRotar.Scroll += new System.EventHandler(this.tkbRotar_Scroll);
            // 
            // tkbEscalar
            // 
            this.tkbEscalar.Location = new System.Drawing.Point(124, 126);
            this.tkbEscalar.Minimum = -10;
            this.tkbEscalar.Name = "tkbEscalar";
            this.tkbEscalar.Size = new System.Drawing.Size(209, 56);
            this.tkbEscalar.TabIndex = 1;
            this.tkbEscalar.Scroll += new System.EventHandler(this.tkbEscalar_Scroll);
            // 
            // lblEscalar
            // 
            this.lblEscalar.AutoSize = true;
            this.lblEscalar.Location = new System.Drawing.Point(35, 138);
            this.lblEscalar.Name = "lblEscalar";
            this.lblEscalar.Size = new System.Drawing.Size(56, 16);
            this.lblEscalar.TabIndex = 2;
            this.lblEscalar.Text = "Escalar:";
            // 
            // lblMover
            // 
            this.lblMover.AutoSize = true;
            this.lblMover.Location = new System.Drawing.Point(46, 199);
            this.lblMover.Name = "lblMover";
            this.lblMover.Size = new System.Drawing.Size(260, 16);
            this.lblMover.TabIndex = 3;
            this.lblMover.Text = "Mueva la figura con las flechas del teclado";
            // 
            // groupBox1
            // 
            this.groupBox1.Controls.Add(this.lblMover);
            this.groupBox1.Controls.Add(this.lblEscalar);
            this.groupBox1.Controls.Add(this.tkbEscalar);
            this.groupBox1.Controls.Add(this.tkbRotar);
            this.groupBox1.Controls.Add(this.lblRotar);
            this.groupBox1.Location = new System.Drawing.Point(12, 188);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.Size = new System.Drawing.Size(360, 248);
            this.groupBox1.TabIndex = 1;
            this.groupBox1.TabStop = false;
            this.groupBox1.Text = "Transformaciones";
            // 
            // Frm_Figura_6
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(8F, 16F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(985, 601);
            this.Controls.Add(this.groupBox3);
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Name = "Frm_Figura_6";
            this.Text = "Form1";
            this.WindowState = System.Windows.Forms.FormWindowState.Maximized;
            this.groupBox2.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.picGrafico)).EndInit();
            this.groupBox3.ResumeLayout(false);
            this.groupBox3.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbRotar)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscalar)).EndInit();
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion
        private System.Windows.Forms.GroupBox groupBox2;
        private System.Windows.Forms.PictureBox picGrafico;
        private System.Windows.Forms.Button btnDibujar;
        private System.Windows.Forms.GroupBox groupBox3;
        private System.Windows.Forms.TextBox txtAltura;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Button btnReset;
        private System.Windows.Forms.Label lblRotar;
        private System.Windows.Forms.TrackBar tkbRotar;
        private System.Windows.Forms.TrackBar tkbEscalar;
        private System.Windows.Forms.Label lblEscalar;
        private System.Windows.Forms.Label lblMover;
        private System.Windows.Forms.GroupBox groupBox1;
    }
}

