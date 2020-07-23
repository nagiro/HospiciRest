    <style>
        #detall_bloc { width: 100%; padding: 2vw; padding-top: 4vw; }
        #detall_franja_titol { text-align: center; width: 100%; display: block; cursor: default; }    
        #detall_dates > p { font-size: 1.5rem; }
        #detall_requadre_detall { border: 1px solid black; padding: 2vw; margin-top: 4vw; font-size: 1rem }        
        #detall_requadre_info { border: 1px solid black; padding: 2vw; margin-top: 2vw; font-size: 1rem; }
        #detall_bloc h1 { font-size: 3rem; margin-bottom: 2vw; margin-top: 4vw; }
        #detall_bloc h2 { font-size: 1.5rem; margin-bottom: 1vw; margin-top: 3vw; }
            
        #detall_horaris { display: relative; margin-top: 1.5vw; text-align: center; }
        #detall_horaris > summary { margin-bottom:2vw; }
        .detall_horaris_mesos { display: inline-flex; width: 53vw; }
        .detall_horaris_mesos_titol { font-weight: bold; width: 7vw; padding: 0.5vw 0vw;  }
        .detall_horaris_dia, .detall_horaris_dia_festa { display: block; width: 1.5vw; padding: 0.5vw 0vw; }
        .detall_horaris_dia_festa { background-color: grey; }
        .detall_horaris_dia_A { text-decoration: underline; font-weight: bold; }
        .detall_horaris_dia_A:hover { cursor: pointer; }
        .detall_horaris_dia_NA { color: rgba(0,0,0,0.2); }
        .cartellLlocHora { padding: 1vw; width: 20vw; background-color: black; color: white; margin: 2vw auto; }
        
        .cal-info-text { margin-top: 2vw; }
    </style>

</head>
<body>
    
    <div id="detall" class="page">
  
        <barra-superior></barra-superior>

        <banner-carrousel v-if="Loaded" :input-dades="WebStructure.Promocions" :input-menu="WebStructure.Menu" :with-title="false"></banner-carrousel>  

        <breadcumb-div v-if="Loaded && !Errors" :breadcumb-data = 'WebStructure.Breadcumb'></breadcumb-div>

        <show-errors v-if="Errors" :errors="WebStructure.Errors"></show-errors>

        <section id="detall_bloc" v-if="Loaded && !Errors">
            <div id="detall_franja_titol">
                <h1 id="detall_titol"> {{ DetallActivitat.ACTIVITATS_TitolMig }} </h1>
                <time id="detall_dates" v-html="ResumDates()"></time>                
                <details id="detall_horaris">
                    <summary>+ veure en detall dates</summary>
                    <ul v-for="M of carregaDetallHoraris()" class="detall_horaris_mesos">
                        <div class="detall_horaris_mesos_titol">{{M.Nom}}</div>
                        <li v-for="D of M.Dies" :class="getClassDia(D)">                            
                            <div v-if="!D.HIHAACTIVITAT" class="detall_horaris_dia_NA">{{D.DIA}}</div>
                            <a v-if="D.HIHAACTIVITAT" @click="mostraDetallDia(D)" class="detall_horaris_dia_A">{{D.DIA}}</a>
                        </li>
                    
                    </ul>
                    
                    <div class="cartellLlocHora" v-html="Horaris_i_llocs"></div>
                </details>
                
            </div>
            <article id="detall_requadre_detall">            
                <h2 class="titol_text">DESCRIPCIÓ DE L'ACTIVITAT</h2>
                <p v-if="DetallActivitat.ACTIVITATS_Pdf.length > 0">
                    [<a :href="DetallActivitat.ACTIVITATS_Pdf" target="_NEW">{{NomEnllacPDF}}</a>]
                </p>
                <div class="text" v-html="DetallActivitat.ACTIVITATS_DescripcioMig">  </div>                
                <div v-if="DetallCurs.length == 1">
                    <form-inscripcio-simple 
                        :activitat-id="DetallActivitat.ACTIVITATS_ActivitatId" 
                        :cicle-id="DetallActivitat.ACTIVITATS_CiclesCicleId"
                        :detall-curs = "DetallCurs[0]"
                        :detall-descomptes = "DetallDescomptes"
                        :detall-teatre = "DetallTeatre"
                        :seients-ocupats = "SeientsOcupats"
                        :url-actual = "UrlActual"
                    >
                    </form-inscripcio-simple>
                </div>
            </article>
            <article id="detall_requadre_info">
                <h2 class="titol_text">INFORMACIÓ PRÀCTICA</h2>
                <div class="text" v-html="DetallActivitat.ACTIVITATS_InformacioPractica"></div>
            </article>
        </section>        

        <single-list v-if="Loaded && !Errors" :input-titol="'ACTIVITATS RELACIONADES'" :input-color="'#F4A261'" :input-dades="WebStructure.ActivitatsRelacionades" :amb-titol="true"></single-list>

        <div style="margin-bottom: 2vw">&nbsp;</div>
                


<!--

        <app-banners *ngIf="WebStructure.Mode.banners"></app-banners>
        <div *ngIf="WebStructure.Mode.banners">&nbsp;</div>

        <app-footer></app-footer>

        <div style="height:30px; clear:both;"></div>
    </div>
-->
  </div>


  <script>
        var vm2 = new Vue({
        
            el: '#detall',        
            data: { 
                Loaded: false,
                Errors: false,
                WebStructure: <?php echo $Data ?>,                
                DetallActivitat: {},    //Objecte activitat
                DetallCurs: {},         //Objecte inscripció                
                DetallDescomptes: {},
                DetallTeatre: {},
                SeientsOcupats: [],
                MostraDetall: false,         
                Horaris_i_llocs: '',
                Anys: [], 
                MesosAny: [],
                Dies: [],
                DiesMes: [],
                UrlActual: ''         //Url actual de la finestra

            },            
            created: function() {
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {
                    this.Loaded = true;                
                    this.Errors = true;                    
                } else {                    
                    this.Loaded = true;
                    this.DetallActivitat = this.WebStructure.Activitat[0];
                    this.DetallCurs = this.WebStructure.Curs;    
                    this.DetallDescomptes = this.WebStructure.Descomptes;            
                    this.DetallTeatre = this.WebStructure.Teatre;                                        
                    this.SeientsOcupats = this.WebStructure.SeientsOcupats;
                    this.UrlActual = window.location.href;                    
                }
                
            },
            computed: {
                NomEnllacPDF: function() {
                    let Ret = " Descarrega el pdf ";
                    for(C of this.DetallActivitat.ACTIVITATS_Categories.split("@")) {

                        switch(C) {
                            case '56': Ret = " Descarrega el programa de mà "; break;
                            case '46': Ret = " Descarrega el catàleg "; break;                            
                        }
                        
                    }
                    return Ret;
                }
            },
            methods: {                                                            
                veureDetallHoraris: function() {
                    this.MostraDetall = !this.MostraDetall;                
                },                
                CreaCalendari: function() {

                },
                ResumDates: function() {
                    return ResumDates(this.WebStructure.Horaris);                
                },
                carregaDetallHoraris: function() {

                    //He de saber quins són els horaris ( data màxima i mínima )
                    const HorarisActivitat = this.WebStructure.Horaris;
                    const DiaInicial = ConvertirData(HorarisActivitat[0].DIA, 'Object');
                    const DiaFinal = ConvertirData( HorarisActivitat[ HorarisActivitat.length - 1 ].DIA , 'Object');
                    let Mesos = []; // Tots els dies que hi ha al mes                
                    
                    //Genero els dies que hi hagi pel mig. 
                    let MesInicial = DiaInicial.mes
                    for(let A = DiaInicial.any; A <= DiaFinal.any; A++){

                        for(let M = MesInicial; M <= ((A == DiaFinal.any) ? DiaFinal.mes : 12) ; M++) {
                            
                            let MesDescripcio = {'Nom': '', Dies: []};

                            MesDescripcio.Nom = MesNom({dia:'1', mes: M, any: A}, true) + ' - ' + A;

                            for(let D = 1; D < this.QuantsDiesTeElMes(A, M); D++){
                                let DiaSetmana = new Date( A , M, D).getDay();
                                let CapDeSetmana = (DiaSetmana == 6 || DiaSetmana == 0);
                                let HiHaActivitat = HorarisActivitat.findIndex( (Dh) => {
                                        let DiaHorari = ConvertirData(Dh.DIA, 'Object');
                                        return (DiaHorari.any == A && DiaHorari.mes == M && DiaHorari.dia == D);
                                    });

                                MesDescripcio.Dies.push({   DIA: D, 
                                                            CAPSETMANA: CapDeSetmana, 
                                                            HIHAACTIVITAT: (HiHaActivitat > -1),
                                                            HORARI: HorarisActivitat[HiHaActivitat]
                                                        });
                            }
                                                    
                            Mesos.push(MesDescripcio);
                            
                        }

                        MesInicial = 1;

                    }
                    
                    return Mesos;
                },
            
                QuantsDiesTeElMes(any, mes) {
                    return new Date(any, mes, 0).getDate();
                },

                DiaSetmana(DiaSetmana) {
                    switch (DiaSetmana) {
                    case 0:
                        return 'Dg';
                        break;
                    case 1:
                        return 'Dl';
                        break;
                    case 2:
                        return 'Dt';
                        break;
                    case 3:
                        return 'Dc';
                        break;
                    case 4:
                        return 'Dj';
                        break;
                    case 5:
                        return 'Dv';
                        break;
                    case 6:
                        return 'Ds';
                        break;
                    }
                },

                Mesos(Mes) {
                    switch (Mes) {
                    case 0:
                        return 'Gener';
                    case 1:
                        return 'Febrer';
                    case 2:
                        return 'Març';
                    case 3:
                        return 'Abril';
                    case 4:
                        return 'Maig';
                    case 5:
                        return 'Juny';
                    case 6:
                        return 'Juliol';
                    case 7:
                        return 'Agost';
                    case 8:
                        return 'Setembre';
                    case 9:
                        return 'Octubre';
                    case 10:
                        return 'Novembre';
                    case 11:
                        return 'Desembre';
                    }            
                },
                getClassDia(D){                
                    return (D.CAPSETMANA) ? 'detall_horaris_dia_festa' : 'detall_horaris_dia';
                },
                mostraDetallDia(D = null) {                
                    if(D !== null ) this.Horaris_i_llocs = ResumDates([D.HORARI]);
                    else this.Horaris_i_llocs = '<p>clica un dia per veure el detall</p>';
                }
            }
        });

    </script>

</body>
