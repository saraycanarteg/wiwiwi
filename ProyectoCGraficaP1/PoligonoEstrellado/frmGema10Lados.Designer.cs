namespace PoligonoEstrellado
{
    partial class frmGema10Lados
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
            this.grbCanvas = new System.Windows.Forms.GroupBox();
            this.picBox = new System.Windows.Forms.PictureBox();
            this.grbInputs = new System.Windows.Forms.GroupBox();
            this.txtRadio = new System.Windows.Forms.TextBox();
            this.lblAltura = new System.Windows.Forms.Label();
            this.btnReset = new System.Windows.Forms.Button();
            this.btnDibujar = new System.Windows.Forms.Button();
            this.grbTransformaciones = new System.Windows.Forms.GroupBox();
            this.tkbEscala = new System.Windows.Forms.TrackBar();
            this.btnDetener = new System.Windows.Forms.Button();
            this.lblEscala = new System.Windows.Forms.Label();
            this.btnTrasladar = new System.Windows.Forms.Button();
            this.btnRotar = new System.Windows.Forms.Button();
            this.grbCanvas.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.picBox)).BeginInit();
            this.grbInputs.SuspendLayout();
            this.grbTransformaciones.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscala)).BeginInit();
            this.SuspendLayout();
            // 
            // grbCanvas
            // 
            this.grbCanvas.Controls.Add(this.picBox);
            this.grbCanvas.Location = new System.Drawing.Point(483, 40);
            this.grbCanvas.Name = "grbCanvas";
            this.grbCanvas.Size = new System.Drawing.Size(700, 605);
            this.grbCanvas.TabIndex = 13;
            this.grbCanvas.TabStop = false;
            this.grbCanvas.Text = "Gráfico";
            // 
            // picBox
            // 
            this.picBox.Location = new System.Drawing.Point(27, 26);
            this.picBox.Name = "picBox";
            this.picBox.Size = new System.Drawing.Size(648, 545);
            this.picBox.TabIndex = 0;
            this.picBox.TabStop = false;
            // 
            // grbInputs
            // 
            this.grbInputs.Controls.Add(this.txtRadio);
            this.grbInputs.Controls.Add(this.lblAltura);
            this.grbInputs.Controls.Add(this.btnReset);
            this.grbInputs.Controls.Add(this.btnDibujar);
            this.grbInputs.Location = new System.Drawing.Point(16, 40);
            this.grbInputs.Name = "grbInputs";
            this.grbInputs.Size = new System.Drawing.Size(441, 189);
            this.grbInputs.TabIndex = 14;
            this.grbInputs.TabStop = false;
            this.grbInputs.Text = "Entradas";
            // 
            // txtRadio
            // 
            this.txtRadio.Location = new System.Drawing.Point(135, 45);
            this.txtRadio.Name = "txtRadio";
            this.txtRadio.Size = new System.Drawing.Size(259, 26);
            this.txtRadio.TabIndex = 5;
            // 
            // lblAltura
            // 
            this.lblAltura.AutoSize = true;
            this.lblAltura.Location = new System.Drawing.Point(26, 51);
            this.lblAltura.Name = "lblAltura";
            this.lblAltura.Size = new System.Drawing.Size(104, 20);
            this.lblAltura.TabIndex = 4;
            this.lblAltura.Text = "Altura (radio):";
            // 
            // btnReset
            // 
            this.btnReset.Location = new System.Drawing.Point(240, 91);
            this.btnReset.Name = "btnReset";
            this.btnReset.Size = new System.Drawing.Size(92, 35);
            this.btnReset.TabIndex = 3;
            this.btnReset.Text = "Resetear";
            this.btnReset.UseVisualStyleBackColor = true;
            this.btnReset.Click += new System.EventHandler(this.btnReset_Click);
            // 
            // btnDibujar
            // 
            this.btnDibujar.Location = new System.Drawing.Point(94, 91);
            this.btnDibujar.Name = "btnDibujar";
            this.btnDibujar.Size = new System.Drawing.Size(92, 35);
            this.btnDibujar.TabIndex = 2;
            this.btnDibujar.Text = "Dibujar";
            this.btnDibujar.UseVisualStyleBackColor = true;
            this.btnDibujar.Click += new System.EventHandler(this.btnDibujar_Click);
            // 
            // grbTransformaciones
            // 
            this.grbTransformaciones.Controls.Add(this.tkbEscala);
            this.grbTransformaciones.Controls.Add(this.btnDetener);
            this.grbTransformaciones.Controls.Add(this.lblEscala);
            this.grbTransformaciones.Controls.Add(this.btnTrasladar);
            this.grbTransformaciones.Controls.Add(this.btnRotar);
            this.grbTransformaciones.Location = new System.Drawing.Point(16, 292);
            this.grbTransformaciones.Name = "grbTransformaciones";
            this.grbTransformaciones.Size = new System.Drawing.Size(441, 352);
            this.grbTransformaciones.TabIndex = 16;
            this.grbTransformaciones.TabStop = false;
            this.grbTransformaciones.Text = "Transformaciones";
            // 
            // tkbEscala
            // 
            this.tkbEscala.Location = new System.Drawing.Point(154, 51);
            this.tkbEscala.Name = "tkbEscala";
            this.tkbEscala.Size = new System.Drawing.Size(242, 69);
            this.tkbEscala.TabIndex = 7;
            this.tkbEscala.Value = 1;
            this.tkbEscala.Scroll += new System.EventHandler(this.tkbEscala_Scroll);
            // 
            // btnDetener
            // 
            this.btnDetener.Location = new System.Drawing.Point(162, 146);
            this.btnDetener.Name = "btnDetener";
            this.btnDetener.Size = new System.Drawing.Size(92, 35);
            this.btnDetener.TabIndex = 6;
            this.btnDetener.Text = "Detener";
            this.btnDetener.UseVisualStyleBackColor = true;
            this.btnDetener.Click += new System.EventHandler(this.btnDetener_Click);
            // 
            // lblEscala
            // 
            this.lblEscala.AutoSize = true;
            this.lblEscala.Location = new System.Drawing.Point(60, 51);
            this.lblEscala.Name = "lblEscala";
            this.lblEscala.Size = new System.Drawing.Size(57, 20);
            this.lblEscala.TabIndex = 5;
            this.lblEscala.Text = "Escala";
            // 
            // btnTrasladar
            // 
            this.btnTrasladar.Location = new System.Drawing.Point(261, 146);
            this.btnTrasladar.Name = "btnTrasladar";
            this.btnTrasladar.Size = new System.Drawing.Size(92, 35);
            this.btnTrasladar.TabIndex = 3;
            this.btnTrasladar.Text = "Trasladar";
            this.btnTrasladar.UseVisualStyleBackColor = true;
            this.btnTrasladar.Click += new System.EventHandler(this.btnTrasladar_Click);
            // 
            // btnRotar
            // 
            this.btnRotar.Location = new System.Drawing.Point(64, 146);
            this.btnRotar.Name = "btnRotar";
            this.btnRotar.Size = new System.Drawing.Size(92, 35);
            this.btnRotar.TabIndex = 2;
            this.btnRotar.Text = "Rotar";
            this.btnRotar.UseVisualStyleBackColor = true;
            this.btnRotar.Click += new System.EventHandler(this.btnRotar_Click);
            // 
            // frmGema10Lados
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(9F, 20F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(1200, 692);
            this.Controls.Add(this.grbTransformaciones);
            this.Controls.Add(this.grbInputs);
            this.Controls.Add(this.grbCanvas);
            this.Margin = new System.Windows.Forms.Padding(4, 5, 4, 5);
            this.Name = "frmGema10Lados";
            this.Text = "frmGema10Lados";
            this.KeyDown += new System.Windows.Forms.KeyEventHandler(this.frmGema10Lados_KeyDown);
            this.grbCanvas.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.picBox)).EndInit();
            this.grbInputs.ResumeLayout(false);
            this.grbInputs.PerformLayout();
            this.grbTransformaciones.ResumeLayout(false);
            this.grbTransformaciones.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscala)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox grbCanvas;
        private System.Windows.Forms.PictureBox picBox;
        private System.Windows.Forms.GroupBox grbInputs;
        private System.Windows.Forms.TextBox txtRadio;
        private System.Windows.Forms.Label lblAltura;
        private System.Windows.Forms.Button btnReset;
        private System.Windows.Forms.Button btnDibujar;
        private System.Windows.Forms.GroupBox grbTransformaciones;
        private System.Windows.Forms.TrackBar tkbEscala;
        private System.Windows.Forms.Button btnDetener;
        private System.Windows.Forms.Label lblEscala;
        private System.Windows.Forms.Button btnTrasladar;
        private System.Windows.Forms.Button btnRotar;
    }
}