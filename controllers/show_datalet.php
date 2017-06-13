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
            $html_datalet = $this->create_datalet_code($datalet);
            $this->assign('html_datalet', $html_datalet);

            $this->assign('twitter_card', 'summary_large_image');
            $this->assign('twitter_site', '@RouteToPA');
            $this->assign('twitter_title', 'Titolo');
            $this->assign('twitter_description', 'Descrizione');
            $this->assign('twitter_image', OW_URL_HOME . 'ow_plugins/ode/datalet_images/datalet_' . $datalet_id . '.png');

            $this->assign('facebook_url', OW::getRouter()->urlForRoute('spodshowcase.datalet', array("datalet_id" => $datalet_id)));
            $this->assign('facebook_title', 'Titolo');
            $this->assign('facebook_description', 'Descrizione');
            $this->assign('facebook_image', OW_URL_HOME . 'ow_plugins/ode/datalet_images/datalet_' . $datalet_id . '.png');

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