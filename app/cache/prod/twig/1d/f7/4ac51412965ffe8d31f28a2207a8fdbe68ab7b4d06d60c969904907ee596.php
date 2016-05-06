<?php

/* OptimusOptimusBundle:Graph:main_3.html.twig */
class __TwigTemplate_1df74ac51412965ffe8d31f28a2207a8fdbe68ab7b4d06d60c969904907ee596 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        try {
            $this->parent = $this->env->loadTemplate("OptimusOptimusBundle:Layouts:layout.html.twig");
        } catch (Twig_Error_Loader $e) {
            $e->setTemplateFile($this->getTemplateName());
            $e->setTemplateLine(1);

            throw $e;
        }

        $this->blocks = array(
            'javascripts' => array($this, 'block_javascripts'),
            'clase_historicData' => array($this, 'block_clase_historicData'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "OptimusOptimusBundle:Layouts:layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_javascripts($context, array $blocks = array())
    {
        // line 4
        echo "\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/jquery-1.11.1.min.js"), "html", null, true);
        echo "\"></script>
\t<script  type=\"text/javascript\" src=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/jquery-ui-1.10.3.custom.min.js"), "html", null, true);
        echo "\"></script>
\t\t
\t<script type=\"text/javascript\" src=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.js"), "html", null, true);
        echo "\"></script>\t
\t<script type=\"text/javascript\" src=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.navigate.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.time.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.symbol.js"), "html", null, true);
        echo "\"></script>
\t\t
\t<script type=\"text/javascript\" src=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.axislabelsV2.js"), "html", null, true);
        echo "\"></script>\t
\t
\t<script type=\"text/javascript\" src=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.pie.js"), "html", null, true);
        echo "\"></script>\t
\t<script type=\"text/javascript\" src=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.fillbetween.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.selection.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 19
    public function block_clase_historicData($context, array $blocks = array())
    {
        echo "activo";
    }

    // line 22
    public function block_content($context, array $blocks = array())
    {
        // line 23
        echo "
<script type=\"text/javascript\">
\tvar timeSelected='";
        // line 25
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()), "html", null, true);
        echo "';
\t
\t\$(function(){\t\t
\t\t\$(\"#datepicker\").hide();
\t\t
\t\tvar startDate;
\t\tvar endDate;
\t\t
\t\tvar selectCurrentWeek = function() {
\t\t\twindow.setTimeout(function () {
\t\t\t\t\$('#datepicker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
\t\t\t}, 1);
\t\t}
\t\t
\t\t\$('#datepicker').datepicker( {
\t\t\t/*showWeek: true,
\t\t\tfirstDay: 1,*/
\t\t\tshowOtherMonths: true,
\t\t\tselectOtherMonths: true,
\t\t\tonSelect: function(dateText, inst) { 
\t\t\t\tvar date = \$(this).datepicker('getDate');
\t\t\t\t
\t\t\t\tconsole.log( date.getDate());
\t\t\t\tconsole.log( date.getDay());\t\t\t
\t\t\t\t
\t\t\t\tif(timeSelected=='day'){
\t\t\t\t\tstartDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
\t\t\t\t\tendDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
\t\t\t\t\tconsole.log( startDate);
\t\t\t\t\tconsole.log( endDate);
\t\t\t\t\t
\t\t\t\t}else if(timeSelected=='week'){
\t\t\t\t\tstartDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
\t\t\t\t\tendDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
\t\t\t\t}else if(timeSelected=='month'){
\t\t\t\t\tstartDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
\t\t\t\t\tendDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 30);
\t\t\t\t}
\t\t\t\t
\t\t\t\t//endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
\t\t\t\t\t\t
\t\t\t\tvar dateFormat = inst.settings.dateFormat || \$.datepicker._defaults.dateFormat;
\t\t\t\t\$('#startDate').text(\$.datepicker.formatDate(\"yy-mm-dd\" , startDate, inst.settings )); //dateFormat
\t\t\t\t\$('#endDate').text(\$.datepicker.formatDate( \"yy-mm-dd\", endDate, inst.settings ));
\t\t\t\t
\t\t\t\tselectCurrentWeek();
\t\t\t\t
\t\t\t\tlocation.href=\"";
        // line 72
        echo $this->env->getExtension('routing')->getPath("homepage");
        echo "/\"+\$('#startDate').html()+\"/\"+\$('#endDate').html()+\"/\"+timeSelected;
\t\t\t},
\t\t\tbeforeShowDay: function(date) {
\t\t\t\tvar cssClass = '';
\t\t\t\tif((date >= startDate && date <= endDate) || (date>=new Date('";
        // line 76
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "html", null, true);
        echo "') && date <=new Date('";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "html", null, true);
        echo "')))
\t\t\t\t\tcssClass = 'ui-datepicker-current-day';
\t\t\t\treturn [true, cssClass];
\t\t\t},
\t\t\tonChangeMonthYear: function(year, month, inst) {
\t\t\t\tselectCurrentWeek();
\t\t\t}
\t\t});
\t\t
\t\t\$(document).on('mousemove', '#datepicker .ui-datepicker-calendar tr', function() { \$(this).find('td a').addClass('ui-state-hover');    });
\t\t\$(document).on('mouseleave','#datepicker .ui-datepicker-calendar tr', function() { \$(this).find('td a').removeClass('ui-state-hover'); });
\t\t\t
\t\t\$(\"#placeholder3\").bind(\"plothover\", function (event, pos, item) {
\t\t\tif (item) {
\t\t\t\tdocument.body.style.cursor = 'pointer';
\t\t\t\tif (previousPoint != item.dataIndex) {
\t\t\t\t\tpreviousPoint = item.dataIndex;

\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\tvar x = item.datapoint[0],
\t\t\t\t\t\ty = item.datapoint[1];
\t\t\t\t\tshowTooltip(item.pageX, item.pageY,y);
\t\t\t\t}
\t\t\t}else{
\t\t\t\tdocument.body.style.cursor = 'default';
\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\tpreviousPoint = null;
\t\t\t}
\t\t});

\t\t//Diferentes gráficas para los diferentes valores 
\t\t";
        // line 107
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 108
            echo "\t\t\t";
            $context["lp_i"] = $this->getAttribute($context["loop"], "index0", array());
            echo "\t\t
\t\t\t\tvar plot= \$.plot(\$(\"#placeholder_";
            // line 109
            echo twig_escape_filter($this->env, (isset($context["lp_i"]) ? $context["lp_i"] : null), "html", null, true);
            echo "\"), [ \t\t
\t\t\t\t";
            // line 110
            $context["maxY"] = 0;
            // line 111
            echo "\t\t\t\t";
            $context["minY"] = 0;
            echo "\t\t\t\t
\t\t\t{data: [\t
\t\t\t
\t\t\t\t";
            // line 114
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["j"]) {
                // line 115
                echo "\t\t\t\t\t";
                $context["lp_j"] = $this->getAttribute($context["loop"], "index0", array());
                // line 116
                echo "\t\t\t\t\t
\t\t\t\t\t";
                // line 117
                if (((isset($context["lp_j"]) ? $context["lp_j"] : null) == 0)) {
                    echo " 
\t\t\t\t\t\t";
                    // line 118
                    $context["minY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array());
                    // line 119
                    echo "\t\t\t\t\t";
                } elseif (((isset($context["minY"]) ? $context["minY"] : null) > $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array()))) {
                    echo " 
\t\t\t\t\t\t";
                    // line 120
                    $context["minY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array());
                    // line 121
                    echo "\t\t\t\t\t";
                }
                // line 122
                echo "\t\t\t\t\t
\t\t\t\t\t";
                // line 123
                if (((isset($context["maxY"]) ? $context["maxY"] : null) < $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array()))) {
                    echo " 
\t\t\t\t\t\t";
                    // line 124
                    $context["maxY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array());
                    // line 125
                    echo "\t\t\t\t\t";
                }
                echo "\t\t\t
\t\t\t\t
\t\t\t\t\t";
                // line 127
                $context["date"] = twig_date_format_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "datetime", array()), "F j, Y H:i:s");
                // line 128
                echo "\t\t
\t\t\t\t
\t\t\t\t\t[new Date(\"";
                // line 130
                echo twig_escape_filter($this->env, (isset($context["date"]) ? $context["date"] : null), "html", null, true);
                echo "\"), ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array()), "html", null, true);
                echo " ],\t
\t\t\t\t";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['j'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 131
            echo "\t\t\t\t 
\t\t\t\t], color:'";
            // line 132
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "color", array()), "html", null, true);
            echo "', lines:{show:true, lineWidth:0.5, fillColor:'#000000'},\t\t\t
\t\t\t\t
\t\t\t\t";
            // line 134
            if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "type", array()) == "Panel")) {
                // line 135
                echo "\t\t\t\t\tpoints: { show: true, symbol:'cross' }\t
\t\t\t\t\t
\t\t\t\t";
            }
            // line 138
            echo "\t\t\t\t\t},
\t\t\t\t";
            // line 139
            $context["panYmin"] = (0.1 + ((0.1 / 100) * 100));
            // line 140
            echo "\t\t\t\t";
            $context["panYmax"] = ((isset($context["maxY"]) ? $context["maxY"] : null) + (((isset($context["maxY"]) ? $context["maxY"] : null) / 100) * 20));
            echo "\t\t\t
\t\t\t],
\t\t\t{
\t\t\t\tgrid: {
\t\t\t\t\tcanvasText:{show:true},
\t\t\t\t\thoverable: true,
\t\t\t\t\tclickable: true,
\t\t\t\t\tborderWidth: 1,
\t\t\t\t\tborderColor:\"#cacaca\",
\t\t\t\t\tminBorderMargin: 0.5,
\t\t\t\t\taxisMargin: 1,
\t\t\t\t\tbackgroundColor:'#fff'
\t\t\t\t},
\t\t\t\t\t\t\t
\t\t\t\txaxis:{\t\t\t\t\t
\t\t\t\t\tmode: \"time\", min: new Date(\"";
            // line 155
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "F j, Y H:i:s"), "html", null, true);
            echo "\"), max: new Date(\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "F j, Y H:i:s"), "html", null, true);
            echo "\"), tickLength: 5, font:{size: 11, family: \"sans-serif\", color:\"#626262\"}, 
\t\t\t\t\t";
            // line 156
            if (((isset($context["lp_i"]) ? $context["lp_i"] : null) == 0)) {
                echo "  
\t\t\t\t\t\tposition:'top', 
\t\t\t\t\t";
            }
            // line 158
            echo "\t\t\t\t\t
\t\t\t\t\t";
            // line 159
            if ((((isset($context["lp_i"]) ? $context["lp_i"] : null) < (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) - 1)) && ((isset($context["lp_i"]) ? $context["lp_i"] : null) != 0))) {
                echo " \t\t\t\t\t
\t\t\t\t\t\tshow:false, 
\t\t\t\t\t";
            } else {
                // line 162
                echo "\t\t\t\t\t\tshow:true,
\t\t\t\t\t\ttimeformat: \"%a \\n %d.%m\",
\t\t\t\t\t\tlabelWidth: 50,
\t\t\t\t\t\tminTickSize: [1, \"day\"], reserveSpace:true\t\t\t\t
\t\t\t\t\t";
            }
            // line 166
            echo " 
\t\t\t\t},
\t\t\t\tyaxis:{min:";
            // line 168
            echo twig_escape_filter($this->env, (isset($context["minY"]) ? $context["minY"] : null), "html", null, true);
            echo ", max:";
            echo twig_escape_filter($this->env, (isset($context["maxY"]) ? $context["maxY"] : null), "html", null, true);
            echo ", tickDecimals: 2,  panRange:[";
            echo twig_escape_filter($this->env, (isset($context["panYmin"]) ? $context["panYmin"] : null), "html", null, true);
            echo ",";
            echo twig_escape_filter($this->env, (isset($context["panYmax"]) ? $context["panYmax"] : null), "html", null, true);
            echo "], labelWidth: 50, labelHeight: 50, reserveSpace: true,  ticks:[";
            echo twig_escape_filter($this->env, (isset($context["minY"]) ? $context["minY"] : null), "html", null, true);
            echo ", ";
            echo twig_escape_filter($this->env, (isset($context["maxY"]) ? $context["maxY"] : null), "html", null, true);
            echo "], font:{size: 9, family: \"sans-serif\", color:\"#626262\"}},
\t\t\t\tpan:{interactive:true, cursor: \"move\", frameRate: 20}
\t\t\t});
\t\t\t
\t\t\tvar previousPoint = null;
\t\t\t\$(\"#placeholder_";
            // line 173
            echo twig_escape_filter($this->env, (isset($context["lp_i"]) ? $context["lp_i"] : null), "html", null, true);
            echo "\").bind(\"plothover\", function (event, pos, item) {
\t\t\t\tif (item) {\t\t\t\t\t
\t\t\t\t\tdocument.body.style.cursor = 'pointer';
\t\t\t\t\tif (previousPoint != item.dataIndex) {
\t\t\t\t\t\tpreviousPoint = item.dataIndex;

\t\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\t\tvar x = item.datapoint[0],
\t\t\t\t\t\t\ty = item.datapoint[1];
\t\t\t\t\t\tshowTooltip(item.pageX, item.pageY,y);
\t\t\t\t\t}
\t\t\t\t}else{
\t\t\t\t\tdocument.body.style.cursor = 'default';
\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\tpreviousPoint = null;
\t\t\t\t}
\t\t\t});
\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 191
        echo "\t//});\t\t
\t\t
\t\tfunction showTooltip(x, y, contents)
\t\t{
\t\t\t\$('<div id=\"tooltip\" style=\"font-size:10px;\">' + contents + '</div>').css( {
\t\t\t\tposition: 'absolute',
\t\t\t\tdisplay: 'none',
\t\t\t\ttop: y-20,
\t\t\t\tleft: x + 10,
\t\t\t\tborder: '1px solid #595959',
\t\t\t\tpadding: '2px',\t\t\t\t
\t\t\t\t'background-color': '#DADADA',
\t\t\t\topacity: 0.80
\t\t\t}).appendTo(\"body\").fadeIn(200);
\t\t}
\t\t
\t\t
\t});
\t
\tfunction displayCalendar()
\t{
\t\t\$(\"#datepicker\").slideToggle();
\t}
\t
\tfunction changeTo(time)
\t{
\t\tif(time=='day'){
\t\t\t\$(\"#selectDay\").css({'background-color':'#2e75b6', 'color':'#fff' });
\t\t\t\$(\"#selectWeek\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectMonth\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\ttimeSelected='day';
\t\t}else if(time=='week'){ 
\t\t\t\$(\"#selectDay\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectWeek\").css({'background-color':'#2e75b6', 'color':'#fff' });
\t\t\t\$(\"#selectMonth\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\ttimeSelected='week';
\t\t}else if(time=='month'){
\t\t\t\$(\"#selectDay\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectWeek\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectMonth\").css({'background-color':'#2e75b6', 'color':'#fff' });
\t\t\ttimeSelected='month';
\t\t}
\t}

\t
</script>
    <div id=\"centerRight\">
\t\t\t<div id=\"indicators\" style=\"background-color:#d0cece; width:100%;  overflow:hidden; border-bottom:1px solid #7f7f7f; border-top:1px solid #7f7f7f;\">
\t\t\t\t<p style=\"width:847px; border-right:1px solid #7f7f7f; float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px;\"><strong>Indicators</strong></p>
\t\t\t\t<p style=\"background-color:#e8eef8; float:left; margin:0px; width:40px; height:35px; text-align:center; font-size:16px; line-height:2.3;\">+</p>
\t\t\t</div>\t
\t\t\t
\t\t\t<div id=\"indicators\" style=\"background-color:#d0cece; width:100%;  overflow:hidden; border-bottom:1px solid #7f7f7f;\">
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px; width:60px; border-right:1px solid #7f7f7f;\"><strong>Dates</strong></p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px; width:160px; border-right:1px solid #7f7f7f; text-align:center; background-color: #e8eef8;\">
\t\t\t\t\t<span id=\"startDate\">";
        // line 247
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "html", null, true);
        echo "</span> / 
\t\t\t\t\t<span id=\"endDate\">";
        // line 248
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "html", null, true);
        echo "</span>
\t\t\t\t</p>
\t\t\t\t<p style=\"background-color:#e8eef8; float:left; margin:0px; width:40px; height:35px; text-align:center; font-size:16px; line-height:2.3; border-right:1px solid #7f7f7f; cursor:pointer;\" onclick=\"displayCalendar();\">+</p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 0px; margin:0px; width:160px; border-right:1px solid #7f7f7f; text-align:right;\"><strong>Time scope: </strong></p>
\t\t\t\t
\t\t\t\t";
        // line 254
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "day")) {
            // line 255
            echo "\t\t\t\t\t";
            $context["stileDay"] = "background-color: #2e75b6; color:#fff;";
            // line 256
            echo "\t\t\t\t";
        } else {
            // line 257
            echo "\t\t\t\t\t";
            $context["stileDay"] = "background-color: #e8eef8; color:#44546a;";
            // line 258
            echo "\t\t\t\t";
        }
        // line 259
        echo "\t\t\t\t
\t\t\t\t";
        // line 260
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "week")) {
            // line 261
            echo "\t\t\t\t\t";
            $context["stileWeek"] = "background-color: #2e75b6; color:#fff;";
            // line 262
            echo "\t\t\t\t";
        } else {
            // line 263
            echo "\t\t\t\t\t";
            $context["stileWeek"] = "background-color: #e8eef8; color:#44546a;";
            // line 264
            echo "\t\t\t\t";
        }
        // line 265
        echo "\t\t\t\t
\t\t\t\t";
        // line 266
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "month")) {
            // line 267
            echo "\t\t\t\t\t";
            $context["stileMonth"] = "background-color: #2e75b6; color:#fff; ";
            // line 268
            echo "\t\t\t\t";
        } else {
            // line 269
            echo "\t\t\t\t\t";
            $context["stileMonth"] = "background-color: #e8eef8; color:#44546a;";
            // line 270
            echo "\t\t\t\t";
        }
        // line 271
        echo "\t\t\t\t
\t\t\t\t<p id=\"selectDay\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 272
        echo twig_escape_filter($this->env, (isset($context["stileDay"]) ? $context["stileDay"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('day');\">day</p>
\t\t\t\t
\t\t\t\t<p id=\"selectWeek\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 274
        echo twig_escape_filter($this->env, (isset($context["stileWeek"]) ? $context["stileWeek"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('week');\">week</p>
\t\t\t\t
\t\t\t\t<p id=\"selectMonth\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 276
        echo twig_escape_filter($this->env, (isset($context["stileMonth"]) ? $context["stileMonth"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('month');\">month</p>
\t\t\t\t
\t\t\t\t
\t\t\t</div>
\t\t\t
\t\t\t<div id=\"contentGraficas\" style=\"overflow:hidden; float:left; padding-top:25px; background-color:#f2f2f2;\">
\t\t\t\t
\t\t\t\t<div id=\"datepicker\" style=\" margin-bottom:40px; float:left; background-color:#d0cece;\"></div>
\t\t
\t\t\t\t";
        // line 285
        $context["mitad"] = (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) / 2);
        // line 286
        echo "\t\t\t\t
\t\t\t\t";
        // line 287
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["sensor"]) {
            // line 288
            echo "\t\t\t\t\t
\t\t\t\t\t";
            // line 289
            $context["loop_i"] = $this->getAttribute($context["loop"], "index0", array());
            // line 290
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t";
            // line 292
            if (((isset($context["loop_i"]) ? $context["loop_i"] : null) < (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) - 1))) {
                // line 293
                echo "\t\t\t\t\t\t";
                $context["stilo"] = " height:60px;";
                // line 294
                echo "\t\t\t\t\t";
            } else {
                // line 295
                echo "\t\t\t\t\t\t";
                $context["stilo"] = "min-height:60px;";
                // line 296
                echo "\t\t\t\t\t";
            }
            // line 297
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t";
            // line 299
            if ((((isset($context["loop_i"]) ? $context["loop_i"] : null) == 0) || ((isset($context["loop_i"]) ? $context["loop_i"] : null) == (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) - 1)))) {
                // line 300
                echo "\t\t\t\t\t\t";
                $context["stilo2"] = "width:500px;";
                echo "  
\t\t\t\t\t";
            } else {
                // line 302
                echo "\t\t\t\t\t\t";
                $context["stilo2"] = "width:475px;";
                echo " 
\t\t\t\t\t";
            }
            // line 304
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t<div style=\"float:left; ";
            // line 306
            echo twig_escape_filter($this->env, (isset($context["stilo"]) ? $context["stilo"] : null), "html", null, true);
            echo "\">
\t\t\t\t\t\t<p style=\"font-size:11px; font-weight:bold; text-transform:capitalize; float:left; width:100px; margin-right:10px;\">";
            // line 307
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["loop_i"]) ? $context["loop_i"] : null), array(), "array"), "name", array()), "html", null, true);
            echo "</p>
\t\t\t\t\t\t
\t\t\t\t\t\t<div id=\"placeholder_";
            // line 309
            echo twig_escape_filter($this->env, (isset($context["loop_i"]) ? $context["loop_i"] : null), "html", null, true);
            echo "\" style=\" height:100px; float:left; top:-20px; ";
            echo twig_escape_filter($this->env, (isset($context["stilo2"]) ? $context["stilo2"] : null), "html", null, true);
            echo "\">\t</div>
\t\t\t\t\t</div>\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t
\t\t\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sensor'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 313
        echo "\t\t\t</div>\t\t\t
\t</div>  



";
    }

    public function getTemplateName()
    {
        return "OptimusOptimusBundle:Graph:main_3.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  667 => 313,  647 => 309,  642 => 307,  638 => 306,  634 => 304,  628 => 302,  622 => 300,  620 => 299,  616 => 297,  613 => 296,  610 => 295,  607 => 294,  604 => 293,  602 => 292,  598 => 290,  596 => 289,  593 => 288,  576 => 287,  573 => 286,  571 => 285,  559 => 276,  554 => 274,  549 => 272,  546 => 271,  543 => 270,  540 => 269,  537 => 268,  534 => 267,  532 => 266,  529 => 265,  526 => 264,  523 => 263,  520 => 262,  517 => 261,  515 => 260,  512 => 259,  509 => 258,  506 => 257,  503 => 256,  500 => 255,  498 => 254,  489 => 248,  485 => 247,  427 => 191,  395 => 173,  377 => 168,  373 => 166,  366 => 162,  360 => 159,  357 => 158,  351 => 156,  345 => 155,  326 => 140,  324 => 139,  321 => 138,  316 => 135,  314 => 134,  309 => 132,  306 => 131,  288 => 130,  284 => 128,  282 => 127,  276 => 125,  274 => 124,  270 => 123,  267 => 122,  264 => 121,  262 => 120,  257 => 119,  255 => 118,  251 => 117,  248 => 116,  245 => 115,  228 => 114,  221 => 111,  219 => 110,  215 => 109,  210 => 108,  193 => 107,  157 => 76,  150 => 72,  100 => 25,  96 => 23,  93 => 22,  87 => 19,  81 => 16,  77 => 15,  73 => 14,  68 => 12,  63 => 10,  59 => 9,  55 => 8,  51 => 7,  46 => 5,  41 => 4,  38 => 3,  11 => 1,);
    }
}
