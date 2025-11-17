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
            this.grbOutputs = new System.Windows.Forms.GroupBox();
            this.checkCoordenadas = new System.Windows.Forms.CheckBox();
            this.txtPerimetro = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.txtArea = new System.Windows.Forms.TextBox();
            this.lblArea = new System.Windows.Forms.Label();
            this.grbTransformaciones = new System.Windows.Forms.GroupBox();
            this.tkbEscala = new System.Windows.Forms.TrackBar();
            this.btnDetener = new System.Windows.Forms.Button();
            this.lblEscala = new System.Windows.Forms.Label();
            this.btnTrasladar = new System.Windows.Forms.Button();
            this.btnRotar = new System.Windows.Forms.Button();
            this.grbCanvas.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.picBox)).BeginInit();
            this.grbInputs.SuspendLayout();
            this.grbOutputs.SuspendLayout();
            this.grbTransformaciones.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscala)).BeginInit();
            this.SuspendLayout();
            // 
            // grbCanvas
            // 
            this.grbCanvas.Controls.Add(this.picBox);
            this.grbCanvas.Location = new System.Drawing.Point(322, 26);
            this.grbCanvas.Margin = new System.Windows.Forms.Padding(2);
            this.grbCanvas.Name = "grbCanvas";
            this.grbCanvas.Padding = new System.Windows.Forms.Padding(2);
            this.grbCanvas.Size = new System.Drawing.Size(467, 393);
            this.grbCanvas.TabIndex = 13;
            this.grbCanvas.TabStop = false;
            this.grbCanvas.Text = "Gráfico";
            // 
            // picBox
            // 
            this.picBox.Location = new System.Drawing.Point(18, 17);
            this.picBox.Margin = new System.Windows.Forms.Padding(2);
            this.picBox.Name = "picBox";
            this.picBox.Size = new System.Drawing.Size(432, 354);
            this.picBox.TabIndex = 0;
            this.picBox.TabStop = false;
            // 
            // grbInputs
            // 
            this.grbInputs.Controls.Add(this.txtRadio);
            this.grbInputs.Controls.Add(this.lblAltura);
            this.grbInputs.Controls.Add(this.btnReset);
            this.grbInputs.Controls.Add(this.btnDibujar);
            this.grbInputs.Location = new System.Drawing.Point(11, 26);
            this.grbInputs.Margin = new System.Windows.Forms.Padding(2);
            this.grbInputs.Name = "grbInputs";
            this.grbInputs.Padding = new System.Windows.Forms.Padding(2);
            this.grbInputs.Size = new System.Drawing.Size(294, 92);
            this.grbInputs.TabIndex = 14;
            this.grbInputs.TabStop = false;
            this.grbInputs.Text = "Entradas";
            // 
            // txtRadio
            // 
            this.txtRadio.Location = new System.Drawing.Point(90, 29);
            this.txtRadio.Margin = new System.Windows.Forms.Padding(2);
            this.txtRadio.Name = "txtRadio";
            this.txtRadio.Size = new System.Drawing.Size(174, 20);
            this.txtRadio.TabIndex = 5;
            // 
            // lblAltura
            // 
            this.lblAltura.AutoSize = true;
            this.lblAltura.Location = new System.Drawing.Point(17, 33);
            this.lblAltura.Margin = new System.Windows.Forms.Padding(2, 0, 2, 0);
            this.lblAltura.Name = "lblAltura";
            this.lblAltura.Size = new System.Drawing.Size(69, 13);
            this.lblAltura.TabIndex = 4;
            this.lblAltura.Text = "Altura (radio):";
            // 
            // btnReset
            // 
            this.btnReset.Location = new System.Drawing.Point(160, 59);
            this.btnReset.Margin = new System.Windows.Forms.Padding(2);
            this.btnReset.Name = "btnReset";
            this.btnReset.Size = new System.Drawing.Size(61, 23);
            this.btnReset.TabIndex = 3;
            this.btnReset.Text = "Resetear";
            this.btnReset.UseVisualStyleBackColor = true;
            this.btnReset.Click += new System.EventHandler(this.btnReset_Click);
            // 
            // btnDibujar
            // 
            this.btnDibujar.Location = new System.Drawing.Point(63, 59);
            this.btnDibujar.Margin = new System.Windows.Forms.Padding(2);
            this.btnDibujar.Name = "btnDibujar";
            this.btnDibujar.Size = new System.Drawing.Size(61, 23);
            this.btnDibujar.TabIndex = 2;
            this.btnDibujar.Text = "Dibujar";
            this.btnDibujar.UseVisualStyleBackColor = true;
            this.btnDibujar.Click += new System.EventHandler(this.btnDibujar_Click);
            // 
            // grbOutputs
            // 
            this.grbOutputs.Controls.Add(this.checkCoordenadas);
            this.grbOutputs.Controls.Add(this.txtPerimetro);
            this.grbOutputs.Controls.Add(this.label1);
            this.grbOutputs.Controls.Add(this.txtArea);
            this.grbOutputs.Controls.Add(this.lblArea);
            this.grbOutputs.Location = new System.Drawing.Point(11, 145);
            this.grbOutputs.Margin = new System.Windows.Forms.Padding(2);
            this.grbOutputs.Name = "grbOutputs";
            this.grbOutputs.Padding = new System.Windows.Forms.Padding(2);
            this.grbOutputs.Size = new System.Drawing.Size(294, 101);
            this.grbOutputs.TabIndex = 15;
            this.grbOutputs.TabStop = false;
            this.grbOutputs.Text = "Salidas";
            // 
            // checkCoordenadas
            // 
            this.checkCoordenadas.AutoSize = true;
            this.checkCoordenadas.Location = new System.Drawing.Point(155, 19);
            this.checkCoordenadas.Margin = new System.Windows.Forms.Padding(2);
            this.checkCoordenadas.Name = "checkCoordenadas";
            this.checkCoordenadas.RightToLeft = System.Windows.Forms.RightToLeft.Yes;
            this.checkCoordenadas.Size = new System.Drawing.Size(127, 17);
            this.checkCoordenadas.TabIndex = 9;
            this.checkCoordenadas.Text = "Mostrar Coordenadas";
            this.checkCoordenadas.UseVisualStyleBackColor = true;
            // 
            // txtPerimetro
            // 
            this.txtPerimetro.Location = new System.Drawing.Point(67, 47);
            this.txtPerimetro.Margin = new System.Windows.Forms.Padding(2);
            this.txtPerimetro.Name = "txtPerimetro";
            this.txtPerimetro.ReadOnly = true;
            this.txtPerimetro.Size = new System.Drawing.Size(57, 20);
            this.txtPerimetro.TabIndex = 7;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(11, 51);
            this.label1.Margin = new System.Windows.Forms.Padding(2, 0, 2, 0);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(51, 13);
            this.label1.TabIndex = 6;
            this.label1.Text = "Perimetro";
            // 
            // txtArea
            // 
            this.txtArea.Location = new System.Drawing.Point(67, 19);
            this.txtArea.Margin = new System.Windows.Forms.Padding(2);
            this.txtArea.Name = "txtArea";
            this.txtArea.ReadOnly = true;
            this.txtArea.Size = new System.Drawing.Size(57, 20);
            this.txtArea.TabIndex = 5;
            // 
            // lblArea
            // 
            this.lblArea.AutoSize = true;
            this.lblArea.Location = new System.Drawing.Point(11, 23);
            this.lblArea.Margin = new System.Windows.Forms.Padding(2, 0, 2, 0);
            this.lblArea.Name = "lblArea";
            this.lblArea.Size = new System.Drawing.Size(29, 13);
            this.lblArea.TabIndex = 4;
            this.lblArea.Text = "Area";
            // 
            // grbTransformaciones
            // 
            this.grbTransformaciones.Controls.Add(this.tkbEscala);
            this.grbTransformaciones.Controls.Add(this.btnDetener);
            this.grbTransformaciones.Controls.Add(this.lblEscala);
            this.grbTransformaciones.Controls.Add(this.btnTrasladar);
            this.grbTransformaciones.Controls.Add(this.btnRotar);
            this.grbTransformaciones.Location = new System.Drawing.Point(11, 266);
            this.grbTransformaciones.Margin = new System.Windows.Forms.Padding(2);
            this.grbTransformaciones.Name = "grbTransformaciones";
            this.grbTransformaciones.Padding = new System.Windows.Forms.Padding(2);
            this.grbTransformaciones.Size = new System.Drawing.Size(294, 153);
            this.grbTransformaciones.TabIndex = 16;
            this.grbTransformaciones.TabStop = false;
            this.grbTransformaciones.Text = "Transformaciones";
            // 
            // tkbEscala
            // 
            this.tkbEscala.Location = new System.Drawing.Point(103, 33);
            this.tkbEscala.Margin = new System.Windows.Forms.Padding(2);
            this.tkbEscala.Name = "tkbEscala";
            this.tkbEscala.Size = new System.Drawing.Size(161, 45);
            this.tkbEscala.TabIndex = 7;
            this.tkbEscala.Value = 1;
            this.tkbEscala.Scroll += new System.EventHandler(this.tkbEscala_Scroll);
            // 
            // btnDetener
            // 
            this.btnDetener.Location = new System.Drawing.Point(108, 95);
            this.btnDetener.Margin = new System.Windows.Forms.Padding(2);
            this.btnDetener.Name = "btnDetener";
            this.btnDetener.Size = new System.Drawing.Size(61, 23);
            this.btnDetener.TabIndex = 6;
            this.btnDetener.Text = "Detener";
            this.btnDetener.UseVisualStyleBackColor = true;
            this.btnDetener.Click += new System.EventHandler(this.btnDetener_Click);
            // 
            // lblEscala
            // 
            this.lblEscala.AutoSize = true;
            this.lblEscala.Location = new System.Drawing.Point(40, 33);
            this.lblEscala.Margin = new System.Windows.Forms.Padding(2, 0, 2, 0);
            this.lblEscala.Name = "lblEscala";
            this.lblEscala.Size = new System.Drawing.Size(39, 13);
            this.lblEscala.TabIndex = 5;
            this.lblEscala.Text = "Escala";
            // 
            // btnTrasladar
            // 
            this.btnTrasladar.Location = new System.Drawing.Point(174, 95);
            this.btnTrasladar.Margin = new System.Windows.Forms.Padding(2);
            this.btnTrasladar.Name = "btnTrasladar";
            this.btnTrasladar.Size = new System.Drawing.Size(61, 23);
            this.btnTrasladar.TabIndex = 3;
            this.btnTrasladar.Text = "Trasladar";
            this.btnTrasladar.UseVisualStyleBackColor = true;
            this.btnTrasladar.Click += new System.EventHandler(this.btnTrasladar_Click);
            // 
            // btnRotar
            // 
            this.btnRotar.Location = new System.Drawing.Point(43, 95);
            this.btnRotar.Margin = new System.Windows.Forms.Padding(2);
            this.btnRotar.Name = "btnRotar";
            this.btnRotar.Size = new System.Drawing.Size(61, 23);
            this.btnRotar.TabIndex = 2;
            this.btnRotar.Text = "Rotar";
            this.btnRotar.UseVisualStyleBackColor = true;
            this.btnRotar.Click += new System.EventHandler(this.btnRotar_Click);
            // 
            // frmGema10Lados
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(800, 450);
            this.Controls.Add(this.grbTransformaciones);
            this.Controls.Add(this.grbOutputs);
            this.Controls.Add(this.grbInputs);
            this.Controls.Add(this.grbCanvas);
            this.Name = "frmGema10Lados";
            this.Text = "frmGema10Lados";
            this.KeyDown += new System.Windows.Forms.KeyEventHandler(this.frmGema10Lados_KeyDown);
            this.grbCanvas.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.picBox)).EndInit();
            this.grbInputs.ResumeLayout(false);
            this.grbInputs.PerformLayout();
            this.grbOutputs.ResumeLayout(false);
            this.grbOutputs.PerformLayout();
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
        private System.Windows.Forms.GroupBox grbOutputs;
        private System.Windows.Forms.CheckBox checkCoordenadas;
        private System.Windows.Forms.TextBox txtPerimetro;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox txtArea;
        private System.Windows.Forms.Label lblArea;
        private System.Windows.Forms.GroupBox grbTransformaciones;
        private System.Windows.Forms.TrackBar tkbEscala;
        private System.Windows.Forms.Button btnDetener;
        private System.Windows.Forms.Label lblEscala;
        private System.Windows.Forms.Button btnTrasladar;
        private System.Windows.Forms.Button btnRotar;
    }
}