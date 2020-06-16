<!DOCTYPE html>
<html lang="ca" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />    
    <meta name="description" content="Web de la Fundació Casa de Cultura de Girona. Aquí podràs trobar tota la informació relativa a les nostres activitats i funcionament." /> 
    <meta name="keywords" content="Casa de cultura, divulgació, ciència, petita casa, cicles, música, exposicions, activitats, girona" />
    <meta name="author" content="Casa de Cultura de Girona" />
    <meta name="copyright" content="Casa de Cultura de Girona" />
    <meta name="robots" content="index" />
    <meta name="robots" content="follow"/>
    
    <title>Casa de Cultura de Girona</title>        
    <link rel="icon" href="/WebFiles/Web/img/LogoCCG.jpg" />
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-axios@2.1.5/dist/vue-axios.min.js"></script>

    <script src="https://kit.fontawesome.com/4479587b76.js" crossorigin="anonymous"></script>

    
    <link href="https://cdn.jsdelivr.net/npm/vuejs-dialog@1.4.1/dist/vuejs-dialog.min.css" rel="stylesheet">    
    <script  src="https://cdn.jsdelivr.net/npm/vuejs-dialog@1.4.1/dist/vuejs-dialog.min.js"></script>        
    

    <script>CookieBoxConfig = { 
        content: { 
            title: 'Usem les cookies per a millorar la vostra experiència d\'usuari', 
            content: 'En fer clic a qualsevol enllaç del lloc web, doneu el vostre consentiment explícit per a que les utilitzem.', 
            accept: 'Accepto', 
            learnMore: 'Més informació' }, 
            backgroundColor: '#000000', 
            url: '/pagina/115/cookies' }</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cookie-consent-box@2.3.1/dist/cookie-consent-box.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/cookie-consent-box@2.3.1/dist/cookie-consent-box.min.js"></script>    

    <script  src="/View/ModulesVue/Web/const_and_helpers.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/barraSuperior/barraSuperior.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/SingleImage/SingleImage.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/SingleList/SingleList.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/BannerCarrousel/BannerCarrousel.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/Breadcumb/Breadcumb.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/Filters/Filters.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/ListNodes/ListNodes.js?<?php echo date('U', time()) ?>"></script>
    <script  src="/View/ModulesVue/Web/NoticiesCarrousel/NoticiesCarrousel.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/FormInscripcioSimple/FormInscripcioSimple.js?<?php echo date('U', time()) ?>"></script>    
    <script  src="/View/ModulesVue/Web/ShowErrors/showErrors.js?<?php echo date('U', time()) ?>"></script>    
    
    
    <link href="/View/ModulesVue/Web/barraSuperior/barraSuperior.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/SingleImage/SingleImage.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/SingleList/SingleList.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/BannerCarrousel/BannerCarrousel.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/Breadcumb/Breadcumb.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/Filters/Filters.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/ListNodes/ListNodes.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/NoticiesCarrousel/NoticiesCarrousel.css?<?php echo date('U', time()) ?>" rel="stylesheet" />
    <link href="/View/ModulesVue/Web/FormInscripcioSimple/FormInscripcioSimple.css?<?php echo date('U', time()) ?>" rel="stylesheet" />    
    <link href="/View/ModulesVue/Web/ShowErrors/showErrors.css?<?php echo date('U', time()) ?>" rel="stylesheet" />    

    <link href="/View/ModulesVue/Web/General.css?<?php echo date('U', time()) ?>" rel="stylesheet" />

    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/View/Styles/General.css?<?php echo date('U', time()) ?>">    

    <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-vue@1.0.1/dist/ckeditor.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@17.0.0/build/ckeditor.min.js"> </script>

    <script src='https://unpkg.com/v-calendar@next'></script>

    <script>
        window.Vue.use(VuejsDialog.main.default)        
        window.Vue.use(CKEditor);        
    </script>

<!--    <script src="/View/Scripts/cropper.js"></script>
    <link rel="stylesheet" href="/View/Styles/cropper.css"> -->
