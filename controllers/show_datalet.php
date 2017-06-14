<?php

class SPODSHOWCASE_CTRL_ShowDatalet extends OW_ActionController
{
    public function index(array $params)
    {
        //OW::getDocument()->getMasterPage()->setTemplate(OW::getPluginManager()->getPlugin('spodshowcase')->getRootDir() . 'master_pages/empty.html');

        $datalet_id = $params['datalet_id'];

        $datalet = ODE_BOL_Service::getInstance()->getDataletById($datalet_id);

        if(!empty($datalet))
        {
            $document = OW::getDocument();

            $html_datalet = $this->create_datalet_code($datalet);
            $datalet_para = json_decode($datalet->params);

            $this->assign('html_datalet', $html_datalet);

            $ode_dir = OW::getPluginManager()->getPlugin('ode')->getDirName();

            $document->addMetaInfo("twitter:card", 'summary_large_image');
            $document->addMetaInfo("twitter:site", '@RouteToPA');
            $document->addMetaInfo("twitter:title", isset($datalet_para->datalettitle) ? $datalet_para->datalettitle : 'No title');
            $document->addMetaInfo("twitter:description", isset($datalet_para->description) ? $datalet_para->description : 'No description');
            $document->addMetaInfo("twitter:image", OW_URL_HOME . 'ow_plugins/' . $ode_dir . '/datalet_images/datalet_' . $datalet_id . '.png');

            $document->addMetaInfo("og:url", OW::getRouter()->urlForRoute('spodshowcase.datalet', array("datalet_id" => $datalet_id)), "property");
            $document->addMetaInfo("og:type", 'article', "property");
            $document->addMetaInfo("og:title", isset($datalet_para->datalettitle) ? $datalet_para->datalettitle : 'No title', "property" );
            $document->addMetaInfo("og:description", isset($datalet_para->description) ? $datalet_para->description : 'No description', "property");
            $document->addMetaInfo("og:image", OW_URL_HOME . 'ow_plugins/' . $ode_dir . '/datalet_images/datalet_' . $datalet_id . '.png', "property");
            $document->addMetaInfo("og:image:width", "948", "property" );
            $document->addMetaInfo("og:image:height", "490", "property" );

        }
    }

    public function datalet(array $params)
    {
        OW::getDocument()->getMasterPage()->setTemplate(OW::getPluginManager()->getPlugin('spodshowcase')->getRootDir() . 'master_pages/empty.html');

        $datalet_id = $params['datalet_id'];

        $datalet = ODE_BOL_Service::getInstance()->getDataletById($datalet_id);

        if(!empty($datalet))
        {
            $html_datalet = $this->create_datalet_code($datalet);
            $this->assign('html_datalet', $html_datalet);
        }
    }

    private function create_datalet_code($datalet)
    {
        $params = json_decode($datalet->params);
        $html  = "<link rel='import' href='".SPODPR_COMPONENTS_URL."datalets/{$datalet->component}/{$datalet->component}.html' />";
        $html .= "<{$datalet->component} ";

        foreach ($params as $key => $value){
            $html .= $key."='".$this->htmlSpecialChar($value)."' ";
        }

        //CACHE
        $html .= " data='{$datalet->data}'";
        $html .= " ></{$datalet->component}>";

        return $html;
    }

    protected function htmlSpecialChar($string)
    {
        return str_replace("'","&#39;", $string);
    }

}