<html>
	<head>
		<title>GUIA DE REMISION</title>
		<meta charset="utf-8"> 
		<script src="https://code.jquery.com/jquery-2.2.4.min.js?=<?=JS;?>" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Last-Modified" content="0">
		<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
		<meta http-equiv="Pragma" content="no-cache">
	</head>
	<script>
			$.ajax({
	            url : '<?=base_url()."GUIAREMISION.txt?=".date("Y-m-d-h:i:s");?>',
	            dataType: "text",
	            success : function (data){
	                $(".text").html("<pre>"+data+"</pre>");
			    	window.print();
	            }
	        });
	</script>
<body>
	<section class="text"></section>
</body>
</html>