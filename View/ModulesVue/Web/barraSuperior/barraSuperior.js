
Vue.component('barra-superior', {
    props: {},          
    data: function() { return { FormulariButlleti: false  }},
    computed: {},
    watch: {},
    methods: {},
    template: `        
    
    <section>
            <div v-if="FormulariButlleti" class="divFormButlleti">
                <form action="http://servidor.casadecultura.cat/subscribe" method="POST" accept-charset="utf-8">
                    <label for="name">Nom</label><br/>
                    <input type="text" name="name" id="name"/>
                    <br/>
                    <label for="email">Correu electrònic</label><br/>
                    <input type="email" name="email" id="email"/><br/><br/>
                    <input type="checkbox" name="gdpr" id="gdpr"/>
                    <span><b>Permís per a comunicacions</b>: Dono el meu consentiment perquè m'envieu informació, notícies i novetats al correu electrònic que us he facilitat.</span>
                    <br/><br/>
                    <span><b>Revocació</b>: Si vol revocar el seu consentiment i no seguir rebent els nostres missatges, podrà fer-ho clicant el botó que trobarà a la banda inferior de tots els nostres enviaments o bé pot posar-se en contacte amb nosaltres enviant un correu a albert@casadecultura.cat. Nosaltres volem tenir el màxim rigor amb les seves dades personals. Per veure la nostra política de privacitat pot visitar el nostre web. Un cop enviat aquest formulari, vostè accepta que nosaltres guardem les seves dades en concordança amb la nostra política.</span>
                    <br/><br/>
                    <div style="display:none;">
                    <label for="hp">HP</label><br/>
                    <input type="text" name="hp" id="hp"/>
                    </div>
                    <input type="hidden" name="list" value="ltF6jkfaGp8J6VOZo9w2hg"/>
                    <input type="hidden" name="subform" value="yes"/>
                    <input type="submit" name="submit" id="submit" value="Subscriu-me a la llista" />
                </form>
            </div>

        <nav class="barra_superior" role="navigation" aria-label="Enllaços ràpids">
            <a target="_new" href="https://twitter.com/casadeculturagi" aria-label="Enllaç al Twitter"> <i class="fab fa-twitter"></i> </a>                        
            <a target="_new" href="https://www.facebook.com/casadeculturadegirona" aria-label="Enllaç al facebook"> <i class="fab fa-facebook-square"></i> </a>
            <a target="_new" href="https://www.youtube.com/user/casadeculturagi" aria-label="Enllaç al Youtube"> <i class="fab fa-youtube"></i> </a>
            <a target="_new" href="https://www.instagram.com/casadeculturagi" aria-label="Enllaç a instagram"> <i class="fab fa-instagram"></i> </a>                                    
            <a target="_new" href="mailto:info@casadecultura.cat" aria-label="Enllaç al Email"> <i class="fas fa-at"></i>&nbsp; info@casadecultura.cat </a>
            <a target="_new" href="tel:+0034972202013" aria-label="Enllaç al telèfon"> <i class="fas fa-phone"></i>&nbsp; 972 20 20 13 </a>                        
            <!-- <a target="_new" href="whatsapp://send?text=Click a Whatsapp de la web&phone=+34691217259&abid=+34691217259" aria-label="Enllaç al Whatsapp"> <i class="fab fa-whatsapp"></i>&nbsp; 691 217 259 </a>       -->
            <a target="_new" href="https://casadecultura.wufoo.com/forms/alta-al-butlleta/"  aria-label="Enllaç a alta butlletí"> <i class="fas fa-envelope-open-text"></i>&nbsp; Butlletí </a>                 
            <a target="_new" href="/pagina/141/reserva-d-espais" aria-label="Enllaç a reserva d'espais"> <i class="far fa-file-alt"></i>&nbsp; Reserva espais </a>                        
            <a target="_new" href="/pagina/208/mapa-del-web" aria-label="Enllaç al mapa del web"> <i class="fa fa-map-signs" aria-hidden="true"></i>&nbsp; Mapa web </a>                        
        </nav>
    </section>
    

                `,
});