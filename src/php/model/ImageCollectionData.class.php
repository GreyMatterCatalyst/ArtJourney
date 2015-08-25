<?php
ClassLoader::requireClassOnce( 'model/DbData' );

class ImageCollectionData extends DbData {
    private $name;
    private $description;
    private $owner_user_id;
}

?>