<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup4 extends DbSetup {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      parent::execute($Database, self::getQuery_dropColumn_ratings_comment($dbname)) &&
      parent::execute($Database, self::getQuery_alterView_allrecipes($dbuser)) &&
      parent::finish($Database, 4)
      )
      return true;
    return false;
  }

  static private function getQuery_dropColumn_ratings_comment() : string {
    return 'ALTER TABLE `recipe_ratings` DROP COLUMN `entry_comment`';
  }

  static private function getQuery_alterView_allrecipes(string $dbuser) : string {
    return 'ALTER
      ALGORITHM = UNDEFINED
      DEFINER=`'.$dbuser.'`@`localhost`
      SQL SECURITY INVOKER
      VIEW `allrecipes` AS
      select
        `r`.`recipe_id` AS `recipe_id`,
        `r`.`user_id` AS `user_id`,
        `r`.`recipe_public` AS `recipe_public`,
        `r`.`recipe_name` AS `recipe_name`,
        `r`.`recipe_description` AS `recipe_description`,
        `r`.`recipe_eater` AS `recipe_eater`,
        `r`.`recipe_source_desc` AS `recipe_source_desc`,
        `r`.`recipe_source_url` AS `recipe_source_url`,
        `r`.`recipe_created` AS `recipe_created`,
        `r`.`recipe_published` AS `recipe_published`,
        `p`.`picture_id` AS `picture_id`,
        `p`.`picture_sortindex` AS `picture_sortindex`,
        `p`.`picture_name` AS `picture_name`,
        `p`.`picture_description` AS `picture_description`,
        `p`.`picture_hash` AS `picture_hash`,
        `p`.`picture_filename` AS `picture_filename`,
        `p`.`picture_full_path` AS `picture_full_path`,
        sum(`ra`.`entry_viewed`) AS `recipe_views`,
        sum(`ra`.`entry_cooked`) AS `recipe_cooked`,
        sum(`ra`.`entry_vote`) AS `recipe_votesum`,
        count(`ra`.`entry_vote`) AS `recipe_votes`
      from ((
        `recipes` `r`
        left join `recipe_pictures` `p` on(`p`.`recipe_id` = `r`.`recipe_id` and `p`.`picture_sortindex` = 0))
        left join `recipe_ratings` `ra` on(`ra`.`recipe_id` = `r`.`recipe_id`))
      where `r`.`recipe_public` = 1
      group by
        `r`.`recipe_id`,
        `r`.`user_id`,
        `r`.`recipe_public`,
        `r`.`recipe_name`,
        `r`.`recipe_description`,
        `r`.`recipe_eater`,
        `r`.`recipe_source_desc`,
        `r`.`recipe_source_url`,
        `r`.`recipe_created`,
        `r`.`recipe_published`,
        `p`.`picture_id`,
        `p`.`picture_sortindex`,
        `p`.`picture_name`,
        `p`.`picture_description`,
        `p`.`picture_hash`,
        `p`.`picture_filename`,
        `p`.`picture_full_path`';
  }

}
