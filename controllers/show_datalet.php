<?php

class SPODSHOWCASE_CTRL_ShowDatalet extends OW_ActionController
{
    public function index(array $params)
    {
        OW::getDocument()->getMasterPage()->setTemplate(OW::getPluginManager()->getPlugin('spodshowcase')->getRootDir() . 'master_pages/empty.html');

        $datalet_id = $params['datalet_id'];

        $datalet = ODE_BOL_Service::getInstance()->getDataletById($datalet_id);

        if(!empty($datalet))
        {
            $avatars = SPODAGORA_CLASS_Tools::getInstance()->process_avatar(BOL_AvatarService::getInstance()->getDataForUserAvatars(array($datalet->ownerId)));

            $document = OW::getDocument();

            //OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('spodshowcase')->getStaticCssUrl() . 'showcase.css');
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('spodshowcase')->getStaticCssUrl() . 'showcase_page.css');
            //OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spodshowcase')->getStaticJsUrl() . 'skrollr.js');
            OW::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js');
            OW::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/stats.js/r16/Stats.min.js');
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spodshowcase')->getStaticJsUrl() . 'tt.js');

            $html_datalet = $this->create_datalet_code($datalet);
            $datalet_para = json_decode($datalet->params);
            $context = SPODSHOWCASE_BOL_Service::getInstance()->gat_datalet_context($datalet_id);

            $url = $this->get_dataset_ckan_url($datalet_para->{'data-url'});

            $this->assign('html_datalet', $html_datalet);
            $this->assign('context', $context);
            $this->assign('datalet', $datalet);
            $this->assign('dataset', $url);
            $this->assign('avatar', $avatars[$datalet->ownerId]);
            $this->assign('datalettitle', empty($datalet_para->datalettitle) ? '' : $datalet_para->datalettitle);
            $this->assign('dataletdescription', empty($datalet_para->description) ? '' : "({$datalet_para->description})");

            //$this->assign("staticResourcesUrl", OW::getPluginManager()->getPlugin('spodshowcase')->getStaticUrl());

            // ADD DATALET DEFINITIONS
            $this->assign('datalet_definition_import', ODE_CLASS_Tools::getInstance()->get_all_datalet_definitions());

            $ode_dir = OW::getPluginManager()->getPlugin('ode')->getDirName();
            $url_img = OW_URL_HOME . 'ow_plugins/' . $ode_dir . '/datalet_images/datalet_' . $datalet_id . '.png';
            $this->assign('ur_share_img', $url_img);

            $preference = BOL_PreferenceService::getInstance()->findPreference('fb_app_id');
            $fb_app_id_pref = empty($preference) ? "0000000" : $preference->defaultValue;

            $domain_parse = parse_url($datalet_para->{'data-url'});

            $document->addMetaInfo("twitter:card", 'summary_large_image');
            $document->addMetaInfo("twitter:site", '@RouteToPA');
            //$document->addMetaInfo("twitter:title", isset($datalet_para->datalettitle) ? $datalet_para->datalettitle : 'No title');
            $document->addMetaInfo("twitter:title", isset($datalet_para->datalettitle) ? $datalet_para->datalettitle : $datalet->component);
            //$document->addMetaInfo("twitter:description", isset($datalet_para->description) ? $datalet_para->description : 'No description');
            $document->addMetaInfo("twitter:description", isset($datalet_para->description) ? $datalet_para->description : 'A visualization from ' . $domain_parse['host']);
            $document->addMetaInfo("twitter:image", $url_img);

            $redirect_uri = OW::getRouter()->urlForRoute('spodshowcase.datalet', array("datalet_id" => $datalet_id));
            $document->addMetaInfo("fb:app_id", $fb_app_id_pref, "property");
            $document->addMetaInfo("og:url", $redirect_uri, "property");
            $document->addMetaInfo("og:type", 'article', "property");
            //$document->addMetaInfo("og:title", isset($datalet_para->datalettitle) ? $datalet_para->datalettitle : 'No title', "property" );
            $document->addMetaInfo("og:title", isset($datalet_para->datalettitle) ? $datalet_para->datalettitle :  $datalet->component, "property" );
            //$document->addMetaInfo("og:description", isset($datalet_para->description) ? $datalet_para->description : 'No description', "property");
            $document->addMetaInfo("og:description", isset($datalet_para->description) ? $datalet_para->description : 'A visualization from ' . $domain_parse['host'], "property");
            $document->addMetaInfo("og:image", $url_img, "property");
            $document->addMetaInfo("og:image:width", "948", "property" );
            $document->addMetaInfo("og:image:height", "490", "property" );

        }else {
            throw new Redirect404Exception();
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
            $html_datalet = "<style>html,body{margin:0;padding:0;height: 100%} {$datalet->component}{--base-datalet-visibility: true; --datalet-container-size:100%}</style>" . $html_datalet;
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

    private function get_dataset_ckan_url($url)
    {
        if(strpos($url, "datastore_search?resource_id"))
        {
            $exploded_url = explode("/", $url);
            $exploded_url = $exploded_url[0] . "//" . $exploded_url[2];
            $replaced_url = str_replace("datastore_search?resource_id", "resource_show?id", $url);

            $ch = curl_init($replaced_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $res = curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (200 == $retcode) {
                $data = json_decode($res, true);
                $url  = $exploded_url . "/dataset/" . $data["result"]["package_id"] . "/resource/" . $data["result"]["id"];
            }
        }

        return $url;
    }

    protected function htmlSpecialChar($string)
    {
        return str_replace("'","&#39;", $string);
    }

}