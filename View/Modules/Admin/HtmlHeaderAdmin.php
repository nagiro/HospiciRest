<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    
    <title>Gestor cultural de la província</title>        
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
    <script src="/View/ModulesVue/vue_2_0.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-axios@2.1.5/dist/vue-axios.min.js"></script>

    <script src="https://kit.fontawesome.com/4479587b76.js"></script>
    
    <!-- CROPPERS -->
    <script src="https://unpkg.com/vue-advanced-cropper@latest/dist/index.umd.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha/cropper.min.js" > </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha/cropper.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/vuejs-dialog@1.4.1/dist/vuejs-dialog.min.css" rel="stylesheet">    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vuejs-dialog@1.4.1/dist/vuejs-dialog.min.js"></script>    
    
    <script type="text/javascript" src="/View/ModulesVue/ImageHelperForm.js?<?php echo date('U', time()) ?>"></script>
    <script type="text/javascript" src="/View/ModulesVue/inputHelperForm.js?<?php echo date('U', time()) ?>"></script>
    <script type="text/javascript" src="/View/ModulesVue/selectHelperForm.js?<?php echo date('U', time()) ?>"></script>
    <script type="text/javascript" src="/View/ModulesVue/ckeditorHelperForm.js?<?php echo date('U', time()) ?>"></script>
    <script type="text/javascript" src="/View/ModulesVue/datepickerHelperForm.js?<?php echo date('U', time()) ?>"></script>
    <script type="text/javascript" src="/View/ModulesVue/calendarHelper.js?<?php echo date('U', time()) ?>"></script>
    <script type="text/javascript" src="/View/ModulesVue/llistatActivitatsHelper.js?<?php echo date('U', time()) ?>"></script>
    
    <link rel="stylesheet" href="/View/Styles/General.css?<?php echo date('U', time()) ?>">    

    <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-vue@1.0.1/dist/ckeditor.min.js"></script>
<!--    <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@17.0.0/build/ckeditor.min.js"> </script> -->

    <script src='https://unpkg.com/v-calendar@next'></script>

    <script>
        window.Vue.use(VuejsDialog.main.default)        
        window.Vue.use(CKEditor);        
    </script>

<!--    <script src="/View/Scripts/cropper.js"></script>
    <link rel="stylesheet" href="/View/Styles/cropper.css"> -->
</head>

<body id="adminBody">
<div class="container" id="app">
    <div class="row">
        <div class="col-12" style="background-color: black; padding: 15px; color: white; font-weight: bold; ">Casa de Cultura</div>
    </div>
    <div class="row">        
        <div class="col" style="background-color: white;">
            <div class="row">
