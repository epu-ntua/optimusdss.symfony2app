<?php

/* ::base.html.twig */
class __TwigTemplate_ccd166628ab6e7c5b07de50cef19add62bb575f8a17fe470e2f4decdf52483f0 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!-- app/Resources/views/base.html.twig -->
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html lang=\"es\" xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html\"; charset=\"utf-8\" />
        <title>Optimus ";
        // line 6
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
\t\t
\t\t<meta content=\"\" name=\"description\">
\t\t<meta content=\"\" name=\"keywords\">
\t\t<meta content=\"\" name=\"authors\">
\t\t<meta name=\"language\" content=\"en\">
\t\t<meta name=\"distribution\" content=\"global\">
\t\t<meta name=\"robots\" content=\"all\">
\t\t<meta name=\"Rating\" content=\"general\">

\t   <!--[if lt IE 9]>
            <script src=\"http://html5shim.googlecode.com/svn/trunk/html5.js\"></script>
        <![endif]-->\t\t
\t\t\t\t
        <link href='http://fonts.googleapis.com/css?family=Irish+Grover' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=La+Belle+Aurore' rel='stylesheet' type='text/css'>
        <link href=\"";
        // line 22
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/css/style.css"), "html", null, true);
        echo "\" type=\"text/css\" rel=\"stylesheet\" />
        <link href=\"";
        // line 23
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/css/ui-lightness/jquery-ui-1.10.3.custom.css"), "html", null, true);
        echo "\" type=\"text/css\" rel=\"stylesheet\" />
        
      
        <link rel=\"shortcut icon\" href=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />
    </head>
\t
\t<body>
        ";
        // line 30
        $this->displayBlock('body', $context, $blocks);
        echo "       
    </body>
</html>";
    }

    // line 6
    public function block_title($context, array $blocks = array())
    {
    }

    // line 30
    public function block_body($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "::base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 30,  71 => 6,  64 => 30,  57 => 26,  51 => 23,  47 => 22,  28 => 6,  21 => 1,);
    }
}
