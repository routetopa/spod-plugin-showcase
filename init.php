<?php

OW::getRouter()->addRoute(new OW_Route('spodshowcase.datalet', 'datalet/:datalet_id', "SPODSHOWCASE_CTRL_ShowDatalet", 'index'));
OW::getRouter()->addRoute(new OW_Route('spodshowcase.share_datalet', 'share_datalet/:datalet_id', "SPODSHOWCASE_CTRL_ShowDatalet", 'datalet'));