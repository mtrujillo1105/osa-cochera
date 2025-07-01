var base_url;
var url_image;
var img_url;
var flagBS;
jQuery(document).ready(function(){
    flagBS    = $('#flagBS').val();
    base_url  = $('#base_url').val();
    url_image = $("#url_image").val();
    img_url   = base_url+"system/application/views/images/";
    	
    $("#seleccionarFamilia").click(function(){
        
        var idfamilia=$('#idfamilia').val()
        n     	   = (document.getElementById('tablaFamilia2').rows.length);
        var codfamilia = $("#codfamilia").val()+".";
        var nombre = '';
        
       
        for(i=0;i<n;i++){
            j     = i+1;
            a     = "nivel["+i+"]";
            nivel = document.getElementById(a).value;
            index = document.getElementById(a).selectedIndex;
            nombre= nombre + ' - ' +document.getElementById(a).options[index].text;
        }
        if(nivel!=''){
            nombre=nombre.substr(3);
            parent.cargar_familia(nivel,nombre,codfamilia,idfamilia);
            parent.$.fancybox.close();
        }else{
            k=j-2;
            nombre1="";
            b= "nivel["+k+"]";
            for(x=0;x<=k;x++){
                y="nivel["+x+"]"
                index1 = document.getElementById(y).selectedIndex;
                nombre1=nombre1+' - '+document.getElementById(y).options[index1].text;
            }
            nombre1=nombre1.substr(3);
                    
                    
            nivel1 = document.getElementById(b).value;
            parent.cargar_familia(nivel1,nombre1,codfamilia,idfamilia);
            parent.$.fancybox.close();
        }
    });
    $("#cancelarFamilia").click(function(){
        parent.$.fancybox.close(); 
    });
        
});

