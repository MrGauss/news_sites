<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={charset}" />
        <meta charset="{charset}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="{title}">
        <meta name="keywords" content="CMS">
        <meta name="author" content="MrGauss">
        <meta name="application-name" content="Gauss CMS">
        <title>{title}</title>
        <base href="{HOME}">

        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.structure.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.theme.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/style.css" media="screen" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <script src="/tpl/js/lang-ua.js" type="text/javascript"></script>
        <script src="{SKINDIR}/js/admin.lib.js" type="text/javascript"></script>
        <script src="/tpl/js/uploader.js" type="text/javascript"></script>
        <script src="{SKINDIR}/js/bbcodes.lib.js" type="text/javascript"></script>
		
    </head>
    <body>
        <div id="page_frame">

            <!--
            <div class="mainbox">
                <div id="header">
                    <div id="logo" class="noselect">MrGauss's&nbsp;CMS</div>
                    <div id="sublogo" class="noselect">Просто й надійно</div>
                    <div id="whoareme" class="noselect">
                        <span>Ви ввійшли як <b>MrGauss</b> [192.168.2.1]</span>
                    </div>
                </div>
            </div>
            -->

            <div class="clear"></div>

            <div id="nav">
                <div class="mainbox">
                    {global:main_navigation}
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>

            <div id="content">
                <div class="mainbox">
                    {global:info}
                    <div class="clear"></div>
                    {global:page_item}
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <div id="overlay" class="dnone">
            <div class="overlay"></div>
            <div class="close"></div>
            <div id="progress"></div>
            <div id="maessage"></div>
        </div>

        <div id="ajax"></div>
        <div class="clear"></div>

    </body>
</html>