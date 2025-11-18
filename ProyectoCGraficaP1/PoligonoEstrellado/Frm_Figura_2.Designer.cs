namespace Figuras_Dos_Y_Seis
{
    partial class Frm_Figura_2
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.groupBox1 = new System.Windows.Forms.GroupBox();
            this.lblMover = new System.Windows.Forms.Label();
            this.lblEscalar = new System.Windows.Forms.Label();
            this.tkbEscalar2 = new System.Windows.Forms.TrackBar();
            this.tkbRotar2 = new System.Windows.Forms.TrackBar();
            this.lblRotar = new System.Windows.Forms.Label();
            this.groupBox2 = new System.Windows.Forms.GroupBox();
            this.picGrafico = new System.Windows.Forms.PictureBox();
            this.groupBox3 = new System.Windows.Forms.GroupBox();
            this.txtAltura = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.btnReset = new System.Windows.Forms.Button();
            this.btnDibujar = new System.Windows.Forms.Button();
            this.groupBox4 = new System.Windows.Forms.GroupBox();
            this.txtPerimetro = new System.Windows.Forms.TextBox();
            this.txtArea = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.txtCoordinates = new System.Windows.Forms.TextBox();
            this.groupBox1.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscalar2)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.tkbRotar2)).BeginInit();
            this.groupBox2.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.picGrafico)).BeginInit();
            this.groupBox3.SuspendLayout();
            this.groupBox4.SuspendLayout();
            this.SuspendLayout();
            // 
            // groupBox1
            // 
            this.groupBox1.Controls.Add(this.lblMover);
            this.groupBox1.Controls.Add(this.lblEscalar);
            this.groupBox1.Controls.Add(this.tkbEscalar2);
            this.groupBox1.Controls.Add(this.tkbRotar2);
            this.groupBox1.Controls.Add(this.lblRotar);
            this.groupBox1.Location = new System.Drawing.Point(12, 188);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.Size = new System.Drawing.Size(360, 248);
            this.groupBox1.TabIndex = 0;
            this.groupBox1.TabStop = false;
            this.groupBox1.Text = "Transformaciones";
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
            // lblEscalar
            // 
            this.lblEscalar.AutoSize = true;
            this.lblEscalar.Location = new System.Drawing.Point(35, 138);
            this.lblEscalar.Name = "lblEscalar";
            this.lblEscalar.Size = new System.Drawing.Size(56, 16);
            this.lblEscalar.TabIndex = 2;
            this.lblEscalar.Text = "Escalar:";
            // 
            // tkbEscalar2
            // 
            this.tkbEscalar2.Location = new System.Drawing.Point(124, 126);
            this.tkbEscalar2.Minimum = -10;
            this.tkbEscalar2.Name = "tkbEscalar2";
            this.tkbEscalar2.Size = new System.Drawing.Size(209, 56);
            this.tkbEscalar2.TabIndex = 1;
            this.tkbEscalar2.Scroll += new System.EventHandler(this.tkbEscalar2_Scroll);
            // 
            // tkbRotar2
            // 
            this.tkbRotar2.Location = new System.Drawing.Point(108, 44);
            this.tkbRotar2.Maximum = 360;
            this.tkbRotar2.Name = "tkbRotar2";
            this.tkbRotar2.Size = new System.Drawing.Size(225, 56);
            this.tkbRotar2.TabIndex = 1;
            this.tkbRotar2.Scroll += new System.EventHandler(this.tkbRotar2_Scroll);
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
            this.picGrafico.BackColor = System.Drawing.SystemColors.ButtonHighlight;
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
            // groupBox4
            // 
            this.groupBox4.Controls.Add(this.txtPerimetro);
            this.groupBox4.Controls.Add(this.txtArea);
            this.groupBox4.Controls.Add(this.label3);
            this.groupBox4.Controls.Add(this.label2);
            this.groupBox4.Controls.Add(this.txtCoordinates);
            this.groupBox4.Location = new System.Drawing.Point(12, 186);
            this.groupBox4.Name = "groupBox4";
            this.groupBox4.Size = new System.Drawing.Size(360, 300);
            this.groupBox4.TabIndex = 4;
            this.groupBox4.TabStop = false;
            this.groupBox4.Text = "Salidas";
            // 
            // txtPerimetro
            // 
            this.txtPerimetro.Enabled = false;
            this.txtPerimetro.Location = new System.Drawing.Point(124, 52);
            this.txtPerimetro.Name = "txtPerimetro";
            this.txtPerimetro.Size = new System.Drawing.Size(209, 22);
            this.txtPerimetro.TabIndex = 1;
            // 
            // txtArea
            // 
            this.txtArea.Enabled = false;
            this.txtArea.Location = new System.Drawing.Point(124, 19);
            this.txtArea.Name = "txtArea";
            this.txtArea.Size = new System.Drawing.Size(209, 22);
            this.txtArea.TabIndex = 0;
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Location = new System.Drawing.Point(35, 52);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(68, 16);
            this.label3.TabIndex = 3;
            this.label3.Text = "Perímetro:";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(35, 22);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(39, 16);
            this.label2.TabIndex = 2;
            this.label2.Text = "Área:";
            // 
            // txtCoordinates
            // 
            this.txtCoordinates.Location = new System.Drawing.Point(19, 90);
            this.txtCoordinates.Multiline = true;
            this.txtCoordinates.Name = "txtCoordinates";
            this.txtCoordinates.ReadOnly = true;
            this.txtCoordinates.ScrollBars = System.Windows.Forms.ScrollBars.Vertical;
            this.txtCoordinates.Size = new System.Drawing.Size(314, 192);
            this.txtCoordinates.TabIndex = 4;
            // 
            // Frm_Figura_2
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(8F, 16F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(985, 601);
            this.Controls.Add(this.groupBox3);
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Controls.Add(this.groupBox4);
            this.Name = "Frm_Figura_2";
            this.Text = "Frm_Figura_2";
            this.WindowState = System.Windows.Forms.FormWindowState.Maximized;
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscalar2)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.tkbRotar2)).EndInit();
            this.groupBox2.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.picGrafico)).EndInit();
            this.groupBox3.ResumeLayout(false);
            this.groupBox3.PerformLayout();
            this.groupBox4.ResumeLayout(false);
            this.groupBox4.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox groupBox1;
        private System.Windows.Forms.Label lblRotar;
        private System.Windows.Forms.TrackBar tkbRotar2;
        private System.Windows.Forms.Label lblEscalar;
        private System.Windows.Forms.TrackBar tkbEscalar2;
        private System.Windows.Forms.Label lblMover;
        private System.Windows.Forms.GroupBox groupBox2;
        private System.Windows.Forms.PictureBox picGrafico;
        private System.Windows.Forms.GroupBox groupBox3;
        private System.Windows.Forms.TextBox txtAltura;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Button btnReset;
        private System.Windows.Forms.Button btnDibujar;
        private System.Windows.Forms.GroupBox groupBox4;
        private System.Windows.Forms.TextBox txtPerimetro;
        private System.Windows.Forms.TextBox txtArea;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TextBox txtCoordinates;
    }
}