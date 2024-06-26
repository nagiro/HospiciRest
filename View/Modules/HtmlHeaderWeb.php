<!DOCTYPE html>
<html lang="ca" xmlns="http://www.w3.org/1999/xhtml">
<head>
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-11029946-1"></script>
    <script> window.dataLayer = window.dataLayer || [];  function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'UA-11029946-1'); </script>

    <meta charset="utf-8" />    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />    
    <meta name="description" content="Web de la Fundació Casa de Cultura de Girona. Aquí podràs trobar tota la informació relativa a les nostres activitats i funcionament." /> 
    <meta name="keywords" content="Casa de cultura, divulgació, ciència, petita casa, cicles, música, exposicions, activitats, girona" />
    <meta name="author" content="Casa de Cultura de Girona" />
    <meta name="copyright" content="Casa de Cultura de Girona" />
    <meta name="robots" content="index" />
    <meta name="robots" content="follow"/>
    
    <title><?php echo $Data['HeaderData']['Nom']; ?></title>        
    <link rel="icon" href="<?php echo $Data['HeaderData']['ImgUrl']; ?>" />
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.js"></script>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="/View/Styles/bootstrap_4.3.1.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-axios@2.1.5/dist/vue-axios.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/@braid/vue-formulate@2.5.0/dist/formulate.min.js"></script>    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@braid/vue-formulate@2.5.0/dist/snow.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@braid/vue-formulate-i18n@1.14.0/dist/locales.umd.min.js"></script>

    <script src="https://kit.fontawesome.com/4479587b76.js" crossorigin="anonymous"></script>

    
    <link href="https://cdn.jsdelivr.net/npm/vuejs-dialog@1.4.1/dist/vuejs-dialog.min.css" rel="stylesheet">    
    <script  src="https://cdn.jsdelivr.net/npm/vuejs-dialog@1.4.1/dist/vuejs-dialog.min.js"></script>        

    <script src="https://unpkg.com/simple-jscalendar@1.4.4/source/jsCalendar.min.js" integrity="sha384-0LaRLH/U5g8eCAwewLGQRyC/O+g0kXh8P+5pWpzijxwYczD3nKETIqUyhuA8B/UB" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://unpkg.com/simple-jscalendar@1.4.4/source/jsCalendar.min.css" integrity="sha384-44GnAqZy9yUojzFPjdcUpP822DGm1ebORKY8pe6TkHuqJ038FANyfBYBpRvw8O9w" crossorigin="anonymous">
    

    <script>CookieBoxConfig = { 
        content: { 
            title: 'Usem les cookies per a millorar la vostra experiència d\'usuari', 
            content: '<p>El web de la Casa de Cultura de Girona utilitza cookies pròpies i de tercers amb finalitats analítiques i tècniques.</p>', 
            accept: 'Accepto', 
            learnMore: 'Si desitja més informació accedeixi a la nostra política de cookies' }, 
            backgroundColor: '#000000', 
            url: '/pagina/192/politica-de-cookies' }</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cookie-consent-box@2.3.1/dist/cookie-consent-box.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/cookie-consent-box@2.3.1/dist/cookie-consent-box.min.js"></script>    

    <!-- <script src='https://unpkg.com/v-calendar'></script>    -->
    <script src="https://cdn.jsdelivr.net/npm/v-calendar@2.2.2/lib/v-calendar.umd.min.js"></script>

    <script  src="/View/ModulesVue/Web/const_and_helpers.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/FormUtils/FormUtils.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/barraSuperior/barraSuperior.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/SingleImage/SingleImage.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/SingleList/SingleList.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/BannerCarrousel/BannerCarrousel.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/Breadcumb/Breadcumb.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/Filters/Filters.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/ListNodes/ListNodes.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/NoticiesCarrousel/NoticiesCarrousel.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/FormUsuariAuth/FormUsuariAuth.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/FormInscripcioSimple/FormInscripcioSimple.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/ShowErrors/showErrors.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/FormInscripcioEspai/FormInscripcioEspai.js?<?php echo date('U', time()) ?>"></script>    
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    
    
    <link href="/View/ModulesVue/Web/FormUtils/FormUtils.css?<?php echo date('U', time()) ?>"  rel="stylesheet" />   
    <link href="/View/ModulesVue/Web/barraSuperior/barraSuperior.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/SingleImage/SingleImage.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/SingleList/SingleList.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/BannerCarrousel/BannerCarrousel.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/Breadcumb/Breadcumb.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/Filters/Filters.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/ListNodes/ListNodes.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/NoticiesCarrousel/NoticiesCarrousel.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/FormInscripcioSimple/FormInscripcioSimple.css?<?php echo date('U', time()) ?>" rel="stylesheet" />    
    <link href="/View/ModulesVue/Web/FormUsuariAuth/FormUsuariAuth.css?<?php echo date('U', time()) ?>" rel="stylesheet" />    
    <link href="/View/ModulesVue/Web/ShowErrors/showErrors.css?<?php echo date('U', time()) ?>" rel="stylesheet" />    
    <link href="/View/ModulesVue/Web/FormInscripcioEspai/FormInscripcioEspai.css?<?php echo date('U', time()) ?>" rel="stylesheet" />    

    <link href="/View/ModulesVue/Web/General.css?<?php echo date('U', time()) ?>" rel="stylesheet" />

    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/View/Styles/General.css?<?php echo date('U', time()) ?>">    

    <!-- CROOPER -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha/cropper.min.js" > </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha/cropper.min.css" rel="stylesheet">

    <!-- QR --> 
    <script src="https://unpkg.com/vue-qrcode-reader/dist/VueQrcodeReader.umd.min.js"></script>


    <!-- <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-vue@1.0.1/dist/ckeditor.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@17.0.0/build/ckeditor.min.js"> </script> -->    



<!--    <script src="/View/Scripts/cropper.js"></script>
    <link rel="stylesheet" href="/View/Styles/cropper.css"> -->
    <script>
        
        const axiosInstance = axios.create({baseURL: ''});
        Vue.use(VueFormulate, {
            plugins: [ VueFormulateI18n.ca ],
            locale: 'ca',
            validationNameStrategy: ['validationName', 'label', 'name', 'type'],
            classes: {
                inputHasErrors: 'has-errors',
                inputHasValue: 'has-value'
            },
            uploader: axiosInstance,
            uploadUrl: '/upload',
        });         
        
        
    </script>
