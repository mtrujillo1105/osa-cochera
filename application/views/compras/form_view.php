<html>
<head>
<style type="text/css">
	label,input{
		display:block;
		padding:5px;
	}
</style>
</head>
<body>
<?php echo validation_errors();?>
	<?php echo form_open('formulario');?>
	<?php echo form_label('Nombre:','nombre');?>
	<?php echo form_input(array('name'=>'nombre','id'=>'nombre','size'=>'12'));?>
	<?php echo form_label('Email:','email');?>
	<?php echo form_input(array('name'=>'email','id'=>'email','size'=>'12'));?>
	<?php echo form_submit('enviar','Enviar');?>
	<?php echo form_close();?>
</body>
</html>