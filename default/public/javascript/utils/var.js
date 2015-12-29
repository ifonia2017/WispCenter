var ah = $("#mkc-shell-load").innerHeight();
var aw = $("#mkc-shell-load").innerWidth();  
var MkcDiv = 'mkc-shell-content';
var prevHash = false;
/** Función para mostrar los mensajes del sistema manualmente */
function MkcMessage(message, type, appendTo) {  type = (type==null) ? 'warning' : type; container = (appendTo==null) ? 'mkc-message' : appendTo; $('#'+container).append('<div class="alert alert-block alert-'+type+'"><button class="close" data-dismiss="alert" type="button">×</button>'+message+'</div>'); }
/** Ajax Cursor **/
$("body").ajaxStart(function() { $("body").css("cursor", "wait"); }).ajaxStop(function() { $("body").css("cursor", "default"); });
/** Message **/
$(function() { $("div.mkc-message").live({ mouseenter: function(){ $(this).addClass("mkc-blur mkc-opacity"); }, mouseleave: function(){ $(this).removeClass("mkc-blur mkc-opacity"); } }); });
/** Buttons forward y back **/
$(function() { $("body").on('click', '.btn-back', function(event) { history.back();}); $("body").on('click', '.btn-forward', function(event) { history.forward();});   });
/** Enlazo la url **/
$(document).ready(function() { 
    if (typeof window.history.pushState == 'function') { 
        MkcPushState(); 
    } else {
        MkcCheckHash(); MkcHashChange(); 
    } 
});
$(function(){ 
    $('body').on('click', '.btn-list-phone', function(){ 
        if($('.nav-list-phone').height() == 0) {
            setTimeout(function(){ $('.nav-list-phone').css('height', 'auto') }, 100);
        }        
    });
})
$(function() {
    $('body').on('click', '.mkc-ajax', function(e) {        
        e.preventDefault();
        var este = $(this);
        if(este.hasClass('no-ajax')) {
            if(este.attr('href') != '#' && este.attr('href') != '#/' && este.attr('href') != '#!/') {
                location.href = ""+este.attr('href')+"";                
            }            
        }
        if(este.hasClass('no-load') || este.hasClass('mkc-confirm') || este.hasClass('mkc-dialog') || este.hasClass('js-confirm')) {
            return false;
        }        
        var val = true;
        var capa = (este.attr('data-div')!=undefined) ? este.attr('data-div') : 'mkc-shell-content';
        var spinner = este.hasClass('mkc-spinner') ? true : false;        
        var change_url = este.hasClass('mkc-no-change') ? false : true;
        var message = este.hasClass('mkc-no-message') ? false : true;
        var url = este.attr('href');
        var before_load = este.attr('before-load');//Callback antes de enviar
        var after_load = este.attr('after-load');//Callback después de enviar        
        if(before_load!=null) {
            try { val = eval(before_load); } catch(e) { }
        }               
        if(val) {
            prevHash = true;//Por si se utiliza el hashbang
            //@TODO Revisar la seguridad acá
            if(url!=$.KumbiaPHP.publicPath+'#' && url!=$.KumbiaPHP.publicPath+'#/' && url!='#' && url!='#/') {                         
                options = { capa: capa, spinner: spinner, msg: message, url: url, change_url: change_url};            
                if($.mkcload(options)) {                     
                    if(after_load!=null) {                        
                        try { eval(after_load); } catch(e) { }                    
                    }                     
                }
            }            
        } 
        return true;
    });    
});

/** Mustra/Oculta el spinner **/
function MkcSpinner(action, target) {     
    if(target==null) { 
        target='mkc-spinner'; 
    }                 
    if(action=='show') {         
        $("#mkc-spinner").attr('style','top: 50%; left:50%; margin-left:-50px; margin-top:-50px;'); 
        $("#mkc-shell-load").addClass('mkc-blur'); 
        $("#mkc-loading-content").show(); 
        $("#"+target).show().spin('large', 'white'); 
    } else { 
        $("#mkc-loading-content").hide(); 
        $("#mkc-shell-load").removeClass('mkc-blur');
        $("#"+target).hide().spin(false); 
    } 
}

/**
* Función que actualiza la url con popstate, hashbang o normal
*/
function MkcUpdateUrl(url) { 
    /** Se quita el public path de la url */
    if($.KumbiaPHP.publicPath != '/') {
        url = url.split($.KumbiaPHP.publicPath); 
        url = (url.length > 1) ? url[1] : url[0];    
    } else  {
        url = ltrim(url, '/');
    }    
    if(typeof window.history.pushState == 'function') { 
        url = $.KumbiaPHP.publicPath+url;        
        history.pushState({ path: url }, url, url); 
    } else {         
        window.location.hash = "#!/"+url;
    }
    return true; 
}

/**
 * Función que cambia la url, si el navegador lo soporta
 */
function MkcPushState(){             
    // Función para enlazar cuando cambia la url de la página.
    $(window).bind('popstate', function(event) {                   
        if (!event.originalEvent.state)//Para Chrome
            return;          
        $.mkcload({url: location.pathname});        
    });
}

/**
 * Función que verifica el hash, se utiliza cuando no soporta el popstate
 */
function MkcCheckHash(){   
    var direccion = ""+window.location+"";
    var nombre = direccion.split("#!/");
    if(nombre.length > 1){ 
        direccion = '/'+ltrim(nombre[1], '/');
        $.mkcload({url: direccion});
    }
}
/**
 * Función que cambia actualiza el content cuando cambia el hash
 */
function MkcHashChange() {     
    // Función para determinar cuando cambia el hash de la página.        
    $(window).bind("hashchange",function(event) {   
        if(prevHash) {
            prevHash = false;
            return;
        }
        console.log(event.originalEvent.oldURL)
        console.log(event.originalEvent.newURL)        
        var hash = ""+window.location.hash+"";
        hash = hash.replace("#!/","");
        if(hash && hash!="") {
            $.mkcload({url: hash});
        }
    });
}
function MkcConsole(text) { if($("#mkc-console").length == 0) { $("#mkc-shell-load").prepend('<div id="mkc-console" class="container"></div>'); } $("#mkc-console").append('<p>'+text+'</p>'); }
function MkcCheckLength(contenedor,campo, texto, min) { if (campo.val().length <= min ) { campo.addClass('error'); MkcUpdateTips(contenedor, texto+' debe tener mínimo '+(min+1)+' caracteres.'); campo.focus(); return false; } else { return true; } }
function MkcCheckRegexp(contenedor,campo,regexp,texto) { if ( !( regexp.test(campo.val() ) ) ) { campo.addClass('error'); MkcUpdateTips(contenedor, texto); campo.focus(); return false; } else { return true; } }
function MkcUpdateTips(contenedor,texto) { contenedor.html('<span class="label label-important">'+texto+'</span>');  }
function MkcUcWords(string){ var arrayWords; var returnString = ""; var len; arrayWords = string.split(" "); len = arrayWords.length; for(i=0;i < len ;i++){ if(i != (len-1)){ returnString = returnString+ucFirst(arrayWords[i])+" "; } else{ returnString = returnString+ucFirst(arrayWords[i]); } } return returnString; }
function MkcUcFirst(string){ return string.substr(0,1).toUpperCase()+string.substr(1,string.length).toLowerCase(); }
function MkcPopupReport(url) { var report = window.open(url , 'impresion', "width=800,height=500,left=50,top=50,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO"); report.focus(); }
function MkcPopupTicket(url) { var ticket = window.open(url , 'ticket', "width=800,height=500,left=50,top=50,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO"); ticket.focus(); }
function MkcDatePicker() { var dp = document.createElement("input"); dp.setAttribute("type", "date"); if(dp.type == 'date') { return true; } else { var inputs = $('input.js-datepicker'); if(!inputs.is('input')) { return true; } inputs.datepicker({format: 'yyyy-mm-dd'}); inputs.datepicker().on('changeDate', function(ev){ $(this).datepicker('hide'); }); } return true; }

/**
 * Funciones para limpiar caracteres al igual que el trim de php
 */
function ltrim(str, opt){
    if(opt) {
        while (str.charAt(0) == opt) 
            str = str.substr(1, str.length - 1); 
    } else {
        while (str.charAt(0) == " ") 
            str = str.substr(1, str.length - 1); 
    }    
    return str;
}
function rtrim(str, opt){ 
    if(opt) {
        while (str.charAt(str.length - 1) == opt) 
            str = str.substr(0, str.length - 1); 
    } else {
        while (str.charAt(str.length - 1) == " ") 
            str = str.substr(0, str.length - 1); 
    }    
    return str;
}
function trim(str, opt){ return rtrim(ltrim(str, opt), opt); }
