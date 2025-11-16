namespace PoligonoEstrellado
{
    partial class frmPoligonoEstrellado
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
            this.grbInputs = new System.Windows.Forms.GroupBox();
            this.txtAltura = new System.Windows.Forms.TextBox();
            this.lblAltura = new System.Windows.Forms.Label();
            this.btnReset = new System.Windows.Forms.Button();
            this.btnDibujar = new System.Windows.Forms.Button();
            this.grbCanvas = new System.Windows.Forms.GroupBox();
            this.picCanvas = new System.Windows.Forms.PictureBox();
            this.grbTransformaciones = new System.Windows.Forms.GroupBox();
            this.tkbEscala = new System.Windows.Forms.TrackBar();
            this.btnDetener = new System.Windows.Forms.Button();
            this.lblEscala = new System.Windows.Forms.Label();
            this.btnTrasladar = new System.Windows.Forms.Button();
            this.btnRotar = new System.Windows.Forms.Button();
            this.grbOutputs = new System.Windows.Forms.GroupBox();
            this.txtPerimetro = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.txtArea = new System.Windows.Forms.TextBox();
            this.lblArea = new System.Windows.Forms.Label();
            this.checkCoordenadas = new System.Windows.Forms.CheckBox();
            this.grbInputs.SuspendLayout();
            this.grbCanvas.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.picCanvas)).BeginInit();
            this.grbTransformaciones.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscala)).BeginInit();
            this.grbOutputs.SuspendLayout();
            this.SuspendLayout();
            // 
            // grbInputs
            // 
            this.grbInputs.Controls.Add(this.txtAltura);
            this.grbInputs.Controls.Add(this.lblAltura);
            this.grbInputs.Controls.Add(this.btnReset);
            this.grbInputs.Controls.Add(this.btnDibujar);
            this.grbInputs.Location = new System.Drawing.Point(44, 20);
            this.grbInputs.Name = "grbInputs";
            this.grbInputs.Size = new System.Drawing.Size(351, 142);
            this.grbInputs.TabIndex = 7;
            this.grbInputs.TabStop = false;
            this.grbInputs.Text = "Entradas";
            // 
            // txtAltura
            // 
            this.txtAltura.Location = new System.Drawing.Point(135, 45);
            this.txtAltura.Name = "txtAltura";
            this.txtAltura.Size = new System.Drawing.Size(186, 26);
            this.txtAltura.TabIndex = 5;
            // 
            // lblAltura
            // 
            this.lblAltura.AutoSize = true;
            this.lblAltura.Location = new System.Drawing.Point(25, 51);
            this.lblAltura.Name = "lblAltura";
            this.lblAltura.Size = new System.Drawing.Size(104, 20);
            this.lblAltura.TabIndex = 4;
            this.lblAltura.Text = "Altura (radio):";
            // 
            // btnReset
            // 
            this.btnReset.Location = new System.Drawing.Point(195, 91);
            this.btnReset.Name = "btnReset";
            this.btnReset.Size = new System.Drawing.Size(92, 35);
            this.btnReset.TabIndex = 3;
            this.btnReset.Text = "Resetear";
            this.btnReset.UseVisualStyleBackColor = true;
            this.btnReset.Click += new System.EventHandler(this.btnReset_Click);
            // 
            // btnDibujar
            // 
            this.btnDibujar.Location = new System.Drawing.Point(68, 91);
            this.btnDibujar.Name = "btnDibujar";
            this.btnDibujar.Size = new System.Drawing.Size(92, 35);
            this.btnDibujar.TabIndex = 2;
            this.btnDibujar.Text = "Dibujar";
            this.btnDibujar.UseVisualStyleBackColor = true;
            this.btnDibujar.Click += new System.EventHandler(this.btnDibujar_Click);
            // 
            // grbCanvas
            // 
            this.grbCanvas.Controls.Add(this.picCanvas);
            this.grbCanvas.Location = new System.Drawing.Point(424, 20);
            this.grbCanvas.Name = "grbCanvas";
            this.grbCanvas.Size = new System.Drawing.Size(491, 497);
            this.grbCanvas.TabIndex = 8;
            this.grbCanvas.TabStop = false;
            this.grbCanvas.Text = "Gráfico";
            // 
            // picCanvas
            // 
            this.picCanvas.Location = new System.Drawing.Point(19, 34);
            this.picCanvas.Name = "picCanvas";
            this.picCanvas.Size = new System.Drawing.Size(454, 423);
            this.picCanvas.TabIndex = 0;
            this.picCanvas.TabStop = false;
            // 
            // grbTransformaciones
            // 
            this.grbTransformaciones.Controls.Add(this.tkbEscala);
            this.grbTransformaciones.Controls.Add(this.btnDetener);
            this.grbTransformaciones.Controls.Add(this.lblEscala);
            this.grbTransformaciones.Controls.Add(this.btnTrasladar);
            this.grbTransformaciones.Controls.Add(this.btnRotar);
            this.grbTransformaciones.Location = new System.Drawing.Point(44, 352);
            this.grbTransformaciones.Name = "grbTransformaciones";
            this.grbTransformaciones.Size = new System.Drawing.Size(351, 165);
            this.grbTransformaciones.TabIndex = 8;
            this.grbTransformaciones.TabStop = false;
            this.grbTransformaciones.Text = "Transformaciones";
            // 
            // tkbEscala
            // 
            this.tkbEscala.Location = new System.Drawing.Point(88, 44);
            this.tkbEscala.Name = "tkbEscala";
            this.tkbEscala.Size = new System.Drawing.Size(242, 69);
            this.tkbEscala.TabIndex = 7;
            this.tkbEscala.Value = 1;
            this.tkbEscala.Scroll += new System.EventHandler(this.tkbEscala_Scroll);
            // 
            // btnDetener
            // 
            this.btnDetener.Location = new System.Drawing.Point(119, 119);
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
            this.lblEscala.Location = new System.Drawing.Point(25, 65);
            this.lblEscala.Name = "lblEscala";
            this.lblEscala.Size = new System.Drawing.Size(57, 20);
            this.lblEscala.TabIndex = 5;
            this.lblEscala.Text = "Escala";
            // 
            // btnTrasladar
            // 
            this.btnTrasladar.Location = new System.Drawing.Point(217, 119);
            this.btnTrasladar.Name = "btnTrasladar";
            this.btnTrasladar.Size = new System.Drawing.Size(92, 35);
            this.btnTrasladar.TabIndex = 3;
            this.btnTrasladar.Text = "Trasladar";
            this.btnTrasladar.UseVisualStyleBackColor = true;
            this.btnTrasladar.Click += new System.EventHandler(this.btnTrasladar_Click);
            // 
            // btnRotar
            // 
            this.btnRotar.Location = new System.Drawing.Point(21, 119);
            this.btnRotar.Name = "btnRotar";
            this.btnRotar.Size = new System.Drawing.Size(92, 35);
            this.btnRotar.TabIndex = 2;
            this.btnRotar.Text = "Rotar";
            this.btnRotar.UseVisualStyleBackColor = true;
            this.btnRotar.Click += new System.EventHandler(this.btnRotar_Click);
            // 
            // grbOutputs
            // 
            this.grbOutputs.Controls.Add(this.checkCoordenadas);
            this.grbOutputs.Controls.Add(this.txtPerimetro);
            this.grbOutputs.Controls.Add(this.label1);
            this.grbOutputs.Controls.Add(this.txtArea);
            this.grbOutputs.Controls.Add(this.lblArea);
            this.grbOutputs.Location = new System.Drawing.Point(44, 181);
            this.grbOutputs.Name = "grbOutputs";
            this.grbOutputs.Size = new System.Drawing.Size(351, 165);
            this.grbOutputs.TabIndex = 9;
            this.grbOutputs.TabStop = false;
            this.grbOutputs.Text = "Salidas";
            // 
            // txtPerimetro
            // 
            this.txtPerimetro.Location = new System.Drawing.Point(100, 72);
            this.txtPerimetro.Name = "txtPerimetro";
            this.txtPerimetro.ReadOnly = true;
            this.txtPerimetro.Size = new System.Drawing.Size(84, 26);
            this.txtPerimetro.TabIndex = 7;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(17, 78);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(77, 20);
            this.label1.TabIndex = 6;
            this.label1.Text = "Perimetro";
            // 
            // txtArea
            // 
            this.txtArea.Location = new System.Drawing.Point(100, 29);
            this.txtArea.Name = "txtArea";
            this.txtArea.ReadOnly = true;
            this.txtArea.Size = new System.Drawing.Size(84, 26);
            this.txtArea.TabIndex = 5;
            // 
            // lblArea
            // 
            this.lblArea.AutoSize = true;
            this.lblArea.Location = new System.Drawing.Point(17, 35);
            this.lblArea.Name = "lblArea";
            this.lblArea.Size = new System.Drawing.Size(43, 20);
            this.lblArea.TabIndex = 4;
            this.lblArea.Text = "Area";
            // 
            // checkCoordenadas
            // 
            this.checkCoordenadas.AutoSize = true;
            this.checkCoordenadas.Location = new System.Drawing.Point(21, 124);
            this.checkCoordenadas.Name = "checkCoordenadas";
            this.checkCoordenadas.RightToLeft = System.Windows.Forms.RightToLeft.Yes;
            this.checkCoordenadas.Size = new System.Drawing.Size(189, 24);
            this.checkCoordenadas.TabIndex = 10;
            this.checkCoordenadas.Text = "Mostrar Coordenadas";
            this.checkCoordenadas.UseVisualStyleBackColor = true;
            this.checkCoordenadas.CheckedChanged += new System.EventHandler(this.checkCoordenadas_CheckedChanged);
            // 
            // frmPoligonoEstrellado
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(9F, 20F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.BackColor = System.Drawing.Color.MintCream;
            this.ClientSize = new System.Drawing.Size(962, 538);
            this.Controls.Add(this.grbOutputs);
            this.Controls.Add(this.grbTransformaciones);
            this.Controls.Add(this.grbInputs);
            this.Controls.Add(this.grbCanvas);
            this.Name = "frmPoligonoEstrellado";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.Text = "Polígono Estrellado";
            this.grbInputs.ResumeLayout(false);
            this.grbInputs.PerformLayout();
            this.grbCanvas.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.picCanvas)).EndInit();
            this.grbTransformaciones.ResumeLayout(false);
            this.grbTransformaciones.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.tkbEscala)).EndInit();
            this.grbOutputs.ResumeLayout(false);
            this.grbOutputs.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox grbInputs;
        private System.Windows.Forms.TextBox txtAltura;
        private System.Windows.Forms.Label lblAltura;
        private System.Windows.Forms.Button btnReset;
        private System.Windows.Forms.Button btnDibujar;
        private System.Windows.Forms.GroupBox grbCanvas;
        private System.Windows.Forms.PictureBox picCanvas;
        private System.Windows.Forms.GroupBox grbTransformaciones;
        private System.Windows.Forms.Button btnTrasladar;
        private System.Windows.Forms.Button btnRotar;
        private System.Windows.Forms.GroupBox grbOutputs;
        private System.Windows.Forms.TextBox txtArea;
        private System.Windows.Forms.Label lblArea;
        private System.Windows.Forms.Label lblEscala;
        private System.Windows.Forms.Button btnDetener;
        private System.Windows.Forms.TrackBar tkbEscala;
        private System.Windows.Forms.TextBox txtPerimetro;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.CheckBox checkCoordenadas;
    }
}

