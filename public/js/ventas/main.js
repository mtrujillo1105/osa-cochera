function padHour(val){
	return (val<10)?'0'+val:val;
}

function clock() {
	var x = new Array("Domingo", "Lunes", "Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
	var y = new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	d = new Date();
	sec 	= padHour(d.getSeconds());
	dmonth 	= padHour(d.getDate());
	hour	= padHour(d.getHours());
	minute	= padHour(d.getMinutes());
	day 	= x[d.getUTCDay()];
	month 	= y[d.getMonth()];
	year 	= d.getFullYear();
	m	= d.getMonth()+1;
	nmonth	= padHour(m);
	//var outStr_d = '<div class="text-center"><h3>'+day+', '+dmonth+' de '+month+' de '+year+'</h3></div>';
	//var outStr_h = '<div class="text-center"><h1 class="text-primary">'+hour+':'+minute+':'+sec+'</h1></div>';
        var outStr_d = day+', '+dmonth+' de '+month+' de '+year;
	var outStr_h = hour+':'+minute+':'+sec;        
	document.getElementById('clockDiv').innerHTML=outStr_d+outStr_h;
	//$("#usr_date").val(dmonth+"/"+nmonth+"/"+year);
	$("#usr_date").val(year + "-" + nmonth + "-" + dmonth);
	$("#usr_hour").val(hour+':'+minute+':'+sec);
	document.getElementById('usr_hour_add').innerHTML=outStr_h;
        document.getElementById('usr_hour_edit').innerHTML=outStr_h;
	setTimeout('clock()',1000);
}


function send(type){
	var parameters = {
		"usr_dni" 	: $("#txt_dni").val(),
		"usr_hour" 	: $("#usr_hour").val(),
		"usr_date" 	: $("#usr_date").val(),
		"pro_type" 	: $("#txt_nocturno").val(),
		"usr_type" 	: type
    };
		
	$.ajax({
		data:  parameters,
        url:   BASE_PATH+'operations/setHours',
        type:  'post',
		error: function(xhr, textStatus, errorThrown){
            alert('AVISO : 0003 : Ha ocurrido un error de conexi�n, favor de comunicarse con el administrador del sistema.');
        },
        success:  function (response) {
			$.each($.parseJSON('['+response+']'), function(){
				if(this['status']){
					$("#"+this['data'].type).html(this['data'].value);
				}else{
					alert("AVISO : No se ha realizado el registro.");
				}	
			});
			setTimeout("clear()",REFRESH);
        }
    });
}

function sign(obj){
	if($(obj).val().length==8){
		var parameters = {
			"usr_dni" 	: $(obj).val(),
			"usr_hour" 	: $("#usr_hour").val(),
			"usr_date" 	: $("#usr_date").val()
        };
        $.ajax({
			data:  parameters,
            url:   BASE_PATH+'operations/getEmployee',
            type:  'post',
            beforeSend: function (e) {
			$(obj).removeAttr("onkeyup");
				//$("#result").html("Procesando, espere por favor...");
            },
			complete:function(jqXHR, textStatus) {
			$(obj).attr("onkeyup","sign(this)");
				//success - error
                //alert("request complete "+textStatus);
            },
            error: function(xhr, textStatus, errorThrown){
                alert('AVISO : 0001 : Ha ocurrido un error de conexi�n, favor de comunicarse con el administrador del sistema.');
            },
            success:  function (response) {
				$.each($.parseJSON('['+response+']'), function(){
				
					if(this['status']){
						$("#usr_lastname").html(this['data'].usr_lastname);
						$("#usr_name").html(this['data'].usr_name);
						$("#usr_photo").attr("src", BASE_PATH+this['image']);
						$('#txt_nocturno').val(this['data'].pro_type);
						verify($(obj).val(),this['data'].usr_turno,this['data'].pro_type);
					}else{
						setTimeout("clear()",REFRESH);
						alert("Aviso : El usuario no se encuentra o se encuentra desactivado.");
					}
				});
            }
        });
	}else{
		return true;
	}
	
	if($(obj).val().length>8){
		$(obj).val("");
	}
}


function verify(usr_dni,usr_turno,pro_type){
	var parameters = {
		"usr_dni" 	: usr_dni,
		"usr_hour" 	: $("#usr_hour").val(),
		"usr_date" 	: $("#usr_date").val(),
		"usr_turno" : usr_turno,
		"pro_type" 	: pro_type
    };

	$.ajax({
		data:  parameters,
        url:   BASE_PATH+'operations/getHours',
        type:  'post',
		error: function(xhr, textStatus, errorThrown){
            alert('AVISO : 0002 : Ha ocurrido un error de conexi�n, favor de comunicarse con el administrador del sistema.');
        },
        success:  function (response) {
			$.each($.parseJSON('['+response+']'), function(){
				if(this['mark']){
				
					$('#txt_date').val(this['mark_data'].rel_date);
					
					if(this['type']=='complete'){
						alert("Usted ya realiz� todas sus marcaciones.");
						setTimeout("clear()",REFRESH);
					}
					
					if(this['mark_data'].rel_1!=' '){
						$("#rel_1").html(this['mark_data'].rel_1);
						$(".div_movements_buttom").removeClass("div_movements_buttom_active");
						$("#btn_2").addClass("div_movements_buttom_active");
					}else{
						$(".div_movements_buttom").removeClass("div_movements_buttom_active");
						$("#btn_1").addClass("div_movements_buttom_active");
						$("#rel_1").html('-');
					}
						
					if(this['mark_data'].rel_2!=' '){
						$("#rel_2").html(this['mark_data'].rel_2);
						$(".div_movements_buttom").removeClass("div_movements_buttom_active");
						$("#btn_3").addClass("div_movements_buttom_active");
					}else{
						$("#rel_2").html('-');
					}
							
					if(this['mark_data'].rel_3!=' '){
						$("#rel_3").html(this['mark_data'].rel_3);	
						$(".div_movements_buttom").removeClass("div_movements_buttom_active");
						$("#btn_4").addClass("div_movements_buttom_active");
					}else{
						$("#rel_3").html('-');
					}
							
					if(this['mark_data'].rel_4!=' '){
						$("#rel_4").html(this['mark_data'].rel_4);	
						$(".div_movements_buttom").removeClass("div_movements_buttom_active");
					}else{
						$("#rel_4").html('-');
					}
					$('#'+this['type']).click();
				}else{
					setTimeout("clear()",REFRESH);
					alert("AVISO : Usted no cuenta con horario programado.");
				}
					
			});
        }
    });
}



function clear(){
	$("#usr_photo").attr("src", BASE_PATH+'assets/images/foto/no_disponible.jpg');
	$(".span_data").html("&nbsp;");
	$(".metadata").val("");
	$(".div_movements_data").html("-");
	$("#txt_nocturno").val(0);
	$("#txt_dni").val("");
	$(".div_movements_buttom").removeClass("div_movements_buttom_active");
	$("#txt_dni").focus();
}


function go_back(){
	$("#txt_dni").click();
	$("#txt_dni").focus();
	$("#txt_dni").css("background-color" ,"white");
}

function go_focus(){
	$("#txt_dni").css("background-color" ,"rgb(210,249,254)");
}