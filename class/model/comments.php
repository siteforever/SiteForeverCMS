<?php
/**
 * Модель комментариев
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class model_Comments extends Model
{
    protected $table;

    function createTables()
    {
        $this->table    = DBPREFIX.'comments';
        if ( $this->isExistTable( $this->table ) ) {
            $this->db->query(
                "CREATE TABLE `{$this->table}` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `controller` varchar(50) DEFAULT NULL,
                  `link` int(11) DEFAULT NULL,
                  `author_id` int(11) DEFAULT NULL,
                  `author_name` varchar(100) DEFAULT NULL,
                  `author_email` varchar(100) DEFAULT NULL,
                  `author_url` varchar(250) DEFAULT NULL,
                  `author_city` varchar(100) DEFAULT NULL,
                  `author_ip` varchar(15) DEFAULT NULL,
                  `date` int(11) DEFAULT NULL,
                  `subject` varchar(250) DEFAULT NULL,
                  `text` text,
                  `status` tinyint(4) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `controller` (`controller`),
                  KEY `link` (`link`),
                  KEY `author_id` (`author_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
            );
        }
    }

}
