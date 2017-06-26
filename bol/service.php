<?php

class SPODSHOWCASE_BOL_Service
{
    /**
     * Singleton instance.
     *
     * @var ODE_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return ODE_BOL_Service
     */
    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
    }

    public function gat_datalet_context($dataletId)
    {
        $dbo = OW::getDbo();

        $ex = new OW_Example();
        $ex->andFieldEqual('dataletId', $dataletId);
        $datalet_post = ODE_BOL_DataletPostDao::getInstance()->findObjectByExample($ex);

        switch($datalet_post->plugin)
        {
            case "agora" :
                $context = "agora";
                $query = "SELECT * from ow_spod_agora_room_comment JOIN ow_spod_agora_room ON ow_spod_agora_room_comment.entityId = ow_spod_agora_room.id where ow_spod_agora_room_comment.id = {$datalet_post->postId};";
                break;
            case "newsfeed" :
                $context = "agora";
                $query = "";
                break;
        }

        return array($context => $dbo->queryForRow($query));
    }

}