<?php
/**
 * 文件保存入库
 * @author Gavin
 */
class File extends EMongoGridFS{
    /**
     * This field is optional, but:
     * from PHP MongoDB driver manual:
     *
     * 'You should be able to use any files created by MongoGridFS with any other drivers, and vice versa.
     * However, some drivers expect that all metadata associated with a file be in a "metadata" field.
     * If you're going to be using other languages, it's a good idea to wrap info you might want them
     * to see in a "metadata" field.'
     *
     * @var array $metadata array of additional info/metadata about a file
     */
    public $metadata = array();
 
    // this method should return the collection name for storing files
    public function getCollectionName(){
        return 'file';
    }
 
    // normal rules method, if you use metadata field, set it as a 'safe' attribute
    public function rules(){
        return array(
            array('filename, metadata','safe'),
            array('filename','required'),
        );
    }
 
    /**
     * Just like normal ActiveRecord/EMongoDocument classes
     */
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
}