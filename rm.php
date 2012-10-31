<?php
/**
 * Clear working directory
 * Need for deployment
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
system("rm -r *");
system("rm .htaccess");