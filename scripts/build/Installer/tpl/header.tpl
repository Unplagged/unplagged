<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Unplagged Installation Wizard.</title>    
    <link href="/style/install.css" rel="stylesheet" >    
    <!-- Modernizr needs to stay in the head to work properly -->
    <script src="/js/libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
    <div class="well">
        <h1><img class="logo" src="/images/logo-blue.png" /> {$welcome.title}</h1>
        
        <div class="wizard-steps" id="navigation">
            <a class="current" data-tab-id="1" href="#"><span class="badge">1</span>Connection</a>
            <a data-tab-id="2" href="#"><span class="badge">2</span>General</a>
            <a class="disabled" data-tab-id="3" href="#"><span class="badge">3</span>Dependencies</a>
            <a class="disabled" data-tab-id="4" href="#"><span class="badge">4</span>Check</a>
            <a class="disabled" data-tab-id="5" href="#"><span class="badge">5</span>Finish</a>
        </div>
    </div>