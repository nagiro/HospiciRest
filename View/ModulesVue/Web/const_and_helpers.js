const CONST_url_activitats_img = '/images/activitats/A-';
const CONST_url_cicles_img = '/images/cicles/C-';
const CONST_api_web = '/apiweb/';
const CONST_url_front_img = '/WebFiles/Imatges/Nodes/';

// CURSOS

const CONST_RESTRINGIT_EXCEL = '1';
const CONST_RESTRINGIT_NOMES_UNA = '2';
const CONST_RESTRINGIT_NOMES_UN_COP = '3';

// Matricules

const CONST_PAGAMENT_CAP = '0';
const CONST_PAGAMENT_METALIC = '21';
const CONST_PAGAMENT_TARGETA= '20';
const CONST_PAGAMENT_TELEFON = '23';
const CONST_PAGAMENT_TRANSFERENCIA = '24'; 
const CONST_PAGAMENT_DOMICILIACIO = '33';
const CONST_PAGAMENT_CODI_DE_BARRES = '34';
const CONST_PAGAMENT_RESERVA = '35';
const CONST_PAGAMENT_LLISTA_ESPERA = '36';    
const CONST_PAGAMENT_INVITACIO = '60';  
const CONST_PAGAMENT_DATAFON = '61';

const CONST_ESTAT_LLISTA_ESPERA = '14';

var normalize = (function() {
    var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç.", 
        to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc_",
        mapping = {};
    
    for(var i = 0, j = from.length; i < j; i++ )
        mapping[ from.charAt( i ) ] = to.charAt( i );
    
    return function( str ) {
        var ret = [];
        for( var i = 0, j = str.length; i < j; i++ ) {
            var c = str.charAt( i );
            if( mapping.hasOwnProperty( str.charAt( i ) ) )
                ret.push( mapping[ c ] );
            else
                ret.push( c );
        }      
        return ret.join( '' ).replace( /[^-A-Za-z0-9]+/g, '-' ).toLowerCase();
    }
    
    })();

function EsCapDeSetmana( ObjecteData ) {
    const dt = new Date(ObjecteData.any, (ObjecteData.mes - 1), ObjecteData.dia, 12, 00,00);    
    return ( dt.getDay() == 6 || dt.getDay() == 0 );
}

// Objecte data el retorna la funció ConvertirData(Data, 'Object')
function DiaSetmana( ObjecteData ){
    var dias=["diumenge", "dilluns", "dimarts", "dimecres", "dijous", "divendres", "dissabte"];
    var dt = new Date(ObjecteData.any, (ObjecteData.mes - 1), ObjecteData.dia, 12, 00,00);    
    return dias[dt.getUTCDay()];
};

// Objecte data el retorna la funció ConvertirData(Data, 'Object')
function MesNom( ObjecteData , $NomesMes = false){
    let mesos=["de gener", "de febrer", "de març", "d'abril", "de maig", "de juny", "de juliol", "d'agost", "de setembre", "d'octubre", "de novembre", "de desembre"];        
    let mesosSols = ["gener", "febrer", "març", "abril", "maig", "juny", "juliol", "agost", "setembre", "octubre", "novembre", "desembre"];        
    let RespostaMesos = []; 

    if($NomesMes) RespostaMesos = mesosSols[(parseInt(ObjecteData.mes) - 1)];
    else RespostaMesos = mesos[(parseInt(ObjecteData.mes) - 1)];
    return RespostaMesos;
};


function ConvertirData( DataBDD, ReturnType = 'A' ) {
    const DataArray = DataBDD.split('-');
    const Any = DataArray[0];
    const Mes = DataArray[1];
    const Dia = DataArray[2];

    if(ReturnType == 'A') return DataArray;
    else if(ReturnType == 'TDMA') {
        return Dia + '/' + Mes + '/' + Any;
    } else if(ReturnType == 'TDM') {
        return Dia + '/' + Mes;
    } else if(ReturnType == 'TD') {
        return Dia;
    } else if(ReturnType == 'Object') {
        return {'dia': Dia, 'mes': Mes, 'any': Any };
    } else if(ReturnType == 'Javascript') {
        return new Date(Any, (Mes - 1), Dia, 0, 0, 0);
    } else if(ReturnType == 'Text') {        
        return String(Dia) + ' ' + MesNom({'dia': Dia, 'mes': Mes, 'any': Any });
    } 


}

function ConvertirHora( HoraBDD , ReturnType = 'THM' ) {
    const HoraArray = HoraBDD.split(':');

    if( ReturnType == 'THM' ){
        return HoraArray[0] + '.' + HoraArray[1];
    }
}

function ValidaTelefon(valor) {
    return (/^([0-9]+){9}$/).test(valor);
}

function ValidaEmail(valor) {
    const test = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    return test.test(valor);
  }


function ValidaDNI(value, NoDni = false) {
    
    // Si no tenim DNI, enviem el passaport I VALIDEM automàticament amb més de 8 caràcters    
    if (NoDni && value.length > 8) return true;

    var validChars = 'TRWAGMYFPDXBNJZSQVHLCKET';
    var nifRexp = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
    var nieRexp = /^[XYZ]{1}[0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
    var str = value.toString().toUpperCase();
  
    // Si el format no és nif o nie, pot ser cif... sinó és cif retornem false
    if (!nifRexp.test(str) && !nieRexp.test(str)) return ValidaCIF(str);
  
    var nie = str
      .replace(/^[X]/, '0')
      .replace(/^[Y]/, '1')
      .replace(/^[Z]/, '2');
  
    var letter = str.substr(-1);
    var charIndex = parseInt(nie.substr(0, 8)) % 23;
  
    if (validChars.charAt(charIndex) === letter) return true;        
    else return false;

  }


  function ValidaCIF( cif ) {
    
    var CIF_REGEX = /^([ABCDEFGHJKLMNPQRSUVW])(\d{7})([0-9A-J])$/;    
    var match = cif.match( CIF_REGEX );

    if( !match ) return false;

    var letter  = match[1],
        number  = match[2],
        control = match[3];

    var even_sum = 0;
    var odd_sum = 0;
    var n;

    for ( var i = 0; i < number.length; i++) {
      n = parseInt( number[i], 10 );

      // Odd positions (Even index equals to odd position. i=0 equals first position)
      if ( i % 2 === 0 ) {
        // Odd positions are multiplied first.
        n *= 2;

        // If the multiplication is bigger than 10 we need to adjust
        odd_sum += n < 10 ? n : n - 9;

      // Even positions
      // Just sum them
      } else {
        even_sum += n;
      }

    }

    var control_digit = (10 - (even_sum + odd_sum).toString().substr(-1) );
    var control_letter = 'JABCDEFGHI'.substr( control_digit, 1 );

    // Control must be a digit
    if ( letter.match( /[ABEH]/ ) ) {
      return control == control_digit;

    // Control must be a letter
    } else if ( letter.match( /[KPQS]/ ) ) {
      return control == control_letter;

    // Can be either
    } else {
      return control == control_digit || control == control_letter;
    }

  }

// Horaris[] = { DIA, HORA, ESPAI }
function ResumDates(Horaris){

    let ResumDates = '';
    let $PrimerDia = '';
    let $UltimDia = '';                
    let $HoraInici = '';
    let $Espai = '';

    Horaris.forEach( H => {
        $PrimerDia = ( $PrimerDia.length == 0 ) ? H.DIA : $PrimerDia ;
        $HoraInici = ( $HoraInici.length == 0 ) ? H.HORA : $HoraInici ;
        $Espai = ( $Espai.length == 0 ) ? H.ESPAI : $Espai ;
        $UltimDia = H.DIA;
    });

    //Miro quin dia de la setmana és
    let PrimerDiaO = ConvertirData( $PrimerDia , 'Object');        
    let UltimDiaO = ConvertirData( $UltimDia, 'Object');
    let DiaSetmanaNom = DiaSetmana( PrimerDiaO );                

    if($PrimerDia == $UltimDia ) { ResumDates = 'El ' + DiaSetmanaNom + ' ' + PrimerDiaO.dia + ' ' + MesNom(PrimerDiaO) + ' de ' + PrimerDiaO.any }
    else { ResumDates = 'Del ' + PrimerDiaO.dia + ' de ' + MesNom(PrimerDiaO) + ' ' + PrimerDiaO.any + ' al ' + UltimDiaO.dia + ' ' + MesNom(UltimDiaO) + ' de ' + UltimDiaO.any  }
    
    ResumDates = '<p>' + ResumDates + '</p><p>' + ConvertirHora($HoraInici) + ' h - ' + $Espai + '</p>'; 
    
    return ResumDates;
}

/********************************* */
/*** GESTIÓ D'ERRORS ************* */

// Gestió d'errors als formularis
function const_and_helpers_iniciaErrors(form) {
    for(O of Object.keys(form)) {
        form[O] = true;
    }
    return form;
}

function const_and_helpers_isFormValid(form, field) {    
    for( key of Object.keys(form)) {
        if(!form[key]) { return false; }
    }
    return true;            
}