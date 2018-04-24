<?php

class dsgvo {

    var $domain = '';
    var $lang = '';
    var $dsgvo_pool = [];

    private function __construct() {
        return $this;
    }

    private function getDsgvoItems() {
        $this->lang = rex_clang::getCurrent()->getCode();
        $this->dsgvo_pool = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang ORDER by prio',[':lang'=>$lang]);
        return $this->dsgvo_pool;
    }

    public function getDsgvoConsent() {

        if($this->dsgvo_pool == []) {
            $this->dsgvo_pool = getDsgvoItems();
        }

        $output = new rex_fragment();
        $output->setVar("dsgvo_pool", $this->dsgvo_pool);
        $output->setVar("lang", $this->lang);
        $output->setVar("domain", $this->domain);

        return $output->parse('dsgvo-consent.fragment.inc.php');

    }

    public function getDsgvoTracking() {

        if($this->dsgvo_pool == []) {
            $this->dsgvo_pool = getDsgvoItems();
        }
        
        $output = new rex_fragment();
        $output->setVar("dsgvo_pool", $this->dsgvo_pool);
        $output->setVar("lang", $this->lang);
        $output->setVar("domain", $this->domain);

        return $output->parse('dsgvo-tracking.fragment.inc.php');

    }

    public function getDsgvoPage() {

        if($this->dsgvo_pool == []) {
            $this->dsgvo_pool = getDsgvoItems();
        }
        
        $output = new rex_fragment();
        $output->setVar("dsgvo_pool", $this->dsgvo_pool);
        $output->setVar("lang", $this->lang);
        $output->setVar("domain", $this->domain);

    echo $output->parse('dsgvo-page.fragment.inc.php');

    }

}