<?php

/* OptimusOptimusBundle:Layouts:layout.html.twig */
class __TwigTemplate_0093e0168a962d4db605a7b6d94016db23413a155621af24344fcee633bfcb5f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        try {
            $this->parent = $this->env->loadTemplate("::base.html.twig");
        } catch (Twig_Error_Loader $e) {
            $e->setTemplateFile($this->getTemplateName());
            $e->setTemplateLine(1);

            throw $e;
        }

        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'clase_historicData' => array($this, 'block_clase_historicData'),
            'clase_ForecastedData' => array($this, 'block_clase_ForecastedData'),
            'clase_ActionPlanData' => array($this, 'block_clase_ActionPlanData'),
            'javascripts' => array($this, 'block_javascripts'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_body($context, array $blocks = array())
    {
        // line 5
        echo "
   
\t<div id=\"container\">
\t\t<div id=\"header\">
\t\t\t<h3>OPTIMUS DSS - Sant Cugat ";
        // line 9
        echo $this->env->getExtension('translator')->getTranslator()->trans("Hola", array(), "messages");
        echo "</h3>\t
\t\t</div>
\t\t
  

\t\t<div id=\"main\">
\t\t\t<div id=\"left\">
\t\t\t\t<p class=\"titleContentDescription\"><strong>Town Hall</strong></p>
\t\t\t\t<div id=\"imagesContentDescription\">
\t\t\t\t\t<img class=\"marginImgRight\" src=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/img/Imagen5.png"), "html", null, true);
        echo "\"/>
\t\t\t\t\t<img src=\"";
        // line 19
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/img/Imagen7.png"), "html", null, true);
        echo "\"/>\t\t\t
\t\t\t\t</div>
\t\t\t\t<div id=\"attributesContentDescription\">
\t\t\t\t\t<p><span>Building use:</span> Office</p>
\t\t\t\t\t<p><span>Address:</span> Plaça de la Vila, 1</p>
\t\t\t\t\t<p><span>Year of construction:</span> 2007</p>
\t\t\t\t\t<p><span>Surface:</span> 8.593 m2 </p>
\t\t\t\t\t<p><span>Floors:</span> 6</p>
\t\t\t\t\t<p class=\"marginBContentDescription\"><span>Occupation:</span> 350 employees, 400 visitors</p>

\t\t\t\t\t<p><span>Energy rating:</span> D</p>
\t\t\t\t\t<p><span>Renewable generation:</span> PV plant (23 MWh)</p>
\t\t\t\t\t<p><span>Thermal power: </span>850 kW</p>
\t\t\t\t\t<p><span>Electrical power: </span>440 kW</p>
\t\t\t\t\t<p><span>Total energy consumption:</span> 357 kWh/m2</p>
\t\t\t\t\t<p><span>CO2 emissions: </span>86 kgCO2/m2 </p>

\t\t\t\t</div>
\t\t\t</div>
\t
\t\t\t<div id=\"right\">
\t\t\t\t<div id=\"topRight\">
\t\t\t\t\t<p><strong>Funcionalities</strong></p>
\t\t\t\t\t<p class=\"buttonFuncionality buttonAnalyze ";
        // line 42
        $this->displayBlock('clase_historicData', $context, $blocks);
        echo "\" onclick=\"location.href='";
        echo $this->env->getExtension('routing')->getPath("homepage");
        echo "';\"><strong>Analyze historic data</strong></p>
\t\t\t\t\t<p class=\"buttonFuncionality buttonForecasted ";
        // line 43
        $this->displayBlock('clase_ForecastedData', $context, $blocks);
        echo "\" onclick=\"location.href='";
        echo $this->env->getExtension('routing')->getPath("sensor");
        echo "';\"><strong>Analyze Forecasted data</strong></p>
\t\t\t\t\t<p class=\"buttonFuncionality buttonAction ";
        // line 44
        $this->displayBlock('clase_ActionPlanData', $context, $blocks);
        echo "\" onclick=\"location.href='";
        echo $this->env->getExtension('routing')->getPath("actionPlan");
        echo "';\"><strong>Action plans</strong></p>
\t\t\t\t</div>
\t\t\t\t
\t\t\t\t ";
        // line 47
        $this->displayBlock('javascripts', $context, $blocks);
        // line 48
        echo "\t\t\t\t";
        $this->displayBlock('content', $context, $blocks);
        // line 49
        echo "\t\t\t</div>
\t\t</div>

  
\t\t<div id=\"footer\">\t\t
\t\t        
\t\t</div>
\t</div>
    
";
    }

    // line 42
    public function block_clase_historicData($context, array $blocks = array())
    {
    }

    // line 43
    public function block_clase_ForecastedData($context, array $blocks = array())
    {
    }

    // line 44
    public function block_clase_ActionPlanData($context, array $blocks = array())
    {
    }

    // line 47
    public function block_javascripts($context, array $blocks = array())
    {
    }

    // line 48
    public function block_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "OptimusOptimusBundle:Layouts:layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  150 => 48,  145 => 47,  140 => 44,  135 => 43,  130 => 42,  117 => 49,  114 => 48,  112 => 47,  104 => 44,  98 => 43,  92 => 42,  66 => 19,  62 => 18,  50 => 9,  44 => 5,  41 => 4,  11 => 1,);
    }
}
