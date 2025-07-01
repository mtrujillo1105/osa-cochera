<html>
<head>
	<title><?php echo TITULO;?></title>
	<link rel="stylesheet" href="<?php echo URL_CSS;?>estilos.css?=<?=CSS;?>" type="text/css">
	<link rel="stylesheet" href="<?php echo URL_CSS;?>theme.css?=<?=CSS;?>" type="text/css">		
	<script language="javaScript" src="<?php echo URL_JS;?>JSCookMenu.js?=<?=JS;?>"></script>
	<script language="javaScript" src="<?php echo URL_JS;?>theme.js?=<?=JS;?>"></script>	
	<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.js?=<?=JS;?>"></script>
	<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/comercialjs.js?=<?=JS;?>"></script>	
	<script type="text/javascript">
		$(function(){
		$('#mi_form').submit(function(event){
		event.preventDefault(); //Evitamos que el evento submit siga en ejecución, evitando que se recargue toda la página
		$.post("mi_controller/procesa_form",
			$("form#edit_form").serialize(), //Codificamos todo el formulario en formato de URL
			function (data) {
				$('div#sending_form').prepend(data); //Añadimos la respuesta AJAX a nuestro div de notificación de respuesta
			});
		});
		});
	</script>
</head>
<body>
<?php
$attr = 'id="mi_form"'; //Le damos el id mi_form al formulario
echo form_open('mi_controller/procesa_form',$attr);
 //Creamos un campo de tipo texto
 echo form_label('Campo1: ','campo1');
 $data = array(
 'name'        => 'campo1',
 'id'          => 'campo1',
 'value'       => ''
 );
 echo form_input($data);
 //Creamos una serie de checkboxes
 $data = array(
 'name'        => 'campo2',
 'id'          => 'campo2',
 'value'       => '2',
 'checked'     => false
 );
 echo form_checkbox($data);
 $data = array(
 'name'        => 'campo3',
 'id'          => 'campo3',
 'value'       => '3',
 'checked'     => false
 );
 echo form_checkbox($data);
 //Creamos unos radio
 $data = array(
 'name'        => 'radio',
 'id'          => 'radio1',
 'value'       => 'radio1',
 'checked'     => false
 );
 echo form_radio($data);
 $data = array(
 'name'        => 'radio',
 'id'          => 'radio2',
 'value'       => 'radio2',
 'checked'     => false
 );
 echo form_radio($data);
 //Un textarea
 echo form_label('Textarea: ','textarea');
 $data = array(
 'name'        => 'textarea',
 'id'          => 'textarea',
 'value'       => '',
 'rows'        => '2',
 'cols'        => '50'
);
 echo form_textarea($data);
 echo "<br><input type='submit' value='Enviar'>";
echo form_close();

?>
</body>
</html>