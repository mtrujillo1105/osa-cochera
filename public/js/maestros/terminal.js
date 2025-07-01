var base_url
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();       
    $("#imgGuardarTerminal").click(function(){
		dataString = $('#frmProyecto').serialize();
		$("#container").show();
		$("#frmProyecto").submit();
    });
    $("#buscarProyecto").click(function(){
		$("#form_busqueda").submit();
    });	
    $("#nuevoTerminal").click(function(){
		url = base_url+"index.php/maestros/terminal/terminal_nuevo";
		$("#zonaContenido").load(url);
    });
    $("#limpiarProyecto").click(function(){
        url = base_url+"index.php/maestros/terminal/terminales";
        location.href=url;
    });
    $("#imgCancelarProyecto").click(function(){
        base_url = $("#base_url").val();
        location.href = base_url+"index.php/maestros/terminal/terminales";
    });
    
});


function nuevo_terminal(direccion){
    var url = base_url+"index.php/maestros/terminal/nuevo_terminal/"+direccion;
$("#zonaContenido").load(url);
}



function ver_direccion(proyecto){
	url = base_url+"index.php/maestros/terminal/ver_direccion/"+proyecto;
	$("#zonaContenido").load(url);
}

function atras_proyecto(){
	location.href = base_url+"index.php/maestros/terminal/terminales";
}


function agregar_terminal_direccion() {
	terminalCodigo  = null;
	proyecto	    = $("#proyecto").val();
	direccionCodigo = $("#direccionCodigo").val();
    terminalNombre  = $("#nombreTerminal").val();
    terminalModelo  = $("#modeloTerminal").val();
    terminalSerie   = $("#numeroSerie").val();
    terminalLed     = $("#numeroLed").val();  
    n = document.getElementById('tblDetalleTerminalDireccion').rows.length;   
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }    
    fila = '<tr id="' + n + '" class="' + clase + '" >';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_terminal(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';;
    fila += '<td width="2%">';
    fila += ' '+j;
    fila += '</td>';
    fila += '<input type="hidden" value="" name="terminalCodigo[' + n + ']" id="terminalCodigo[' + n + ']">';
    fila += '<input type="hidden" value="' + proyecto + '" name="proyecto[' + n + ']" id="proyecto[' + n + ']">';
    fila += '<input type="hidden" value="' + direccionCodigo + '" name="direccionCodigo[' + n + ']" id="direccionCodigo[' + n + ']">';
    fila += '<td><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + terminalNombre + '" name="terminalNombre[' + n + ']" id="terminalNombre[' + n + ']"></div></td>'
    fila += '<td><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + terminalModelo + '" name="terminalModelo[' + n + ']" id="terminalModelo[' + n + ']"></div></td>'
    fila += '<td><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + terminalSerie + '" name="terminalSerie[' + n + ']" id="terminalSerie[' + n + ']"></div></td>'
    fila += '<td><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + terminalLed + '" name="terminalLed[' + n + ']" id="terminalLed[' + n + ']"></div></td>'
    fila += '<input type="hidden" class="cajaMinima" name="teraccion[' + n + ']" id="teraccion[' + n + ']" value="n">';
    fila += '</tr>';
    $("#tblDetalleTerminalDireccion").append(fila);
    $("#nombreTerminal").focus();
}

function eliminar_terminal(n) {
    if (confirm('Esta seguro que desea eliminar este Terminal ?')) {
    	a = "terminalCodigo[" + n + "]";
    	b = "teraccion[" + n + "]";
        fila = document.getElementById(a).parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
    }
}






