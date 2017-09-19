<?php

// foreach($res['parents'] AS $parent) {
//     $user = $parent->secondryUser()->first();
//     $name = $user->first_name . ' ' . $user->last_name;
//     $items[] = array('id'=>$user->id, 'title'=>'Parent', 'description'=> $name, 'image'=> "demo/images/photos/m.png");
// }


//     $name = $res['user']->first_name . ' ' . $res['user']->last_name;
//     $items[] = array('id'=>$user->id, 'parents'=>"[30,11]", 'title'=>$name, 'image'=> "demo/images/photos/m.png");


// foreach($res['spouse'] AS $spouse) {
//     $s = $spouse->secondryUser()->first();
//     $name = $s->first_name . ' ' . $s->last_name;
//     $items[] = array('id'=>$s->id, 'title'=>$name, 'image'=> "demo/images/photos/m.png");
// }

// foreach($res['childs'] AS $child) {
//     $c = $child->primaryUser()->first();
//     $name = $c->first_name . ' ' . $c->last_name;
//     $items[] = array('id'=>$child->id, 'parents'=>"[11,12]", 'title'=>$name, 'image'=> "demo/images/photos/m.png");
// }

// echo '<pre>';
// echo json_encode($items);
// echo '</pre>';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>The very first family chart</title>

    <link rel="stylesheet" href="demo/js/jquery/ui-lightness/jquery-ui-1.10.2.custom.css" />
    <script type="text/javascript" src="demo/js/jquery/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="demo/js/jquery/jquery-ui-1.10.2.custom.min.js"></script>

    <script  type="text/javascript" src="demo/js/primitives.min.js?219"></script>
    <link href="demo/css/primitives.latest.css?219" media="screen" rel="stylesheet" type="text/css" />

    <script type='text/javascript'>//<![CDATA[
        $(window).load(function () {
            var options = new primitives.famdiagram.Config();

            var items = <?php echo json_encode($items); ?>

            var buttons = [];

            buttons.push(new primitives.orgdiagram.ButtonConfig("home", "ui-icon-search", "Search"));
            options.buttons = buttons;
            options.hasButtons = primitives.common.Enabled.True;
            options.onButtonClick = function (e, data) {
                var message = "User clicked '" + data.name + "' button for item '" + data.context.title + "'.";
                alert(message);
            };

            options.pageFitMode = 0;
            options.items = items;
            options.cursorItem = 2;
            options.linesWidth = 1;
            options.linesColor = "black";
            options.hasSelectorCheckbox = primitives.common.Enabled.False;
            options.normalLevelShift = 20;
            options.dotLevelShift = 20;
            options.lineLevelShift = 20;
            options.normalItemsInterval = 10;
            options.dotItemsInterval = 10;
            options.lineItemsInterval = 10;

            options.defaultTemplateName = "info";
            options.templates = [getInfoTemplate()];
            options.onItemRender = onTemplateRender;

            jQuery("#basicdiagram").famDiagram(options);
        });//]]>


        function onTemplateRender(event, data) {
            switch (data.renderingMode) {
                case primitives.common.RenderingMode.Create:
                    /* Initialize widgets here */
                    break;
                case primitives.common.RenderingMode.Update:
                    /* Update widgets here */
                    break;
            }

            var itemConfig = data.context;

            if (data.templateName == "info") {
                data.element.find("[name=title]").text(itemConfig.title);
            }
        }

        function getInfoTemplate() {
            var result = new primitives.orgdiagram.TemplateConfig();
            result.name = "info";

            // result.itemSize = new primitives.common.Size(80, 36);
            // result.minimizedItemSize = new primitives.common.Size(3, 3);
            // result.highlightPadding = new primitives.common.Thickness(4, 4, 4, 4);


            // var itemTemplate = jQuery(
            //   '<div class="bp-item bp-corner-all bt-item-frame">'
            //     + '<div name="title" class="bp-item" style="top: 3px; left: 6px; width: 68px; height: 28px; font-size: 12px;">'
            //     + '</div>'
            // + '</div>'
            // ).css({
            //     width: result.itemSize.width + "px",
            //     height: result.itemSize.height + "px"
            // }).addClass("bp-item bp-corner-all bt-item-frame");
            // result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

            return result;
        }
    </script>
</head>
<body>
    <center>
     <!--<div id="basicdiagram" />-->
    <div id="basicdiagram" style="width: 640px; height: 800px; border-width: 1px;" />
    </center>
</body>
</html>
