<?php

namespace Surcouf\Cookbook\Database\Setup;

if (!defined('CORE2'))
  exit;

final class DbSetup3 {

  static function install(\Mysqli &$Database, string $dbname, string $dbuser) : bool {
    if (
      self::execute($Database, self::getQuery_createTable_activities($dbname)) &&
      self::execute($Database, self::getQuery_createView_allrecipes($dbuser)) &&
      self::execute($Database, self::getQuery_createView_allrecipetextdata($dbuser)) &&
      self::finish($Database)
      )
      return true;
    return false;
  }

  static private function execute(\Mysqli &$Database, string $query) : bool {
    $result = $Database->query($query);
    if (!$result)
      self::reportError($Database, __METHOD__, $query);
    return $result;
  }

  static private function finish(\Mysqli &$Database) : bool {
    $query = 'INSERT INTO `db_version`(`version_value`) VALUES (3)';
    $result = $Database->query($query);
    if (!$result)
      self::reportError($Database, __METHOD__, $query);
    return $result;
  }

  static private function reportError(\Mysqli &$Database, string $method, string $query) : void {
    $query = 'INSERT INTO `db_logs`(`entry_code`, `entry_message`, `entry_file`, `entry_method`, `entry_query`) VALUES
      ('.$Database->errno.',
       \''.$Database->real_escape_string($Database->error).'\',
       \''.$Database->real_escape_string(__FILE__).'\',
       \''.$Database->real_escape_string($method).'\',
       \''.$Database->real_escape_string($query).'\')';
    $Database->rollback();
    $Database->autocommit(true);
    $Database->query($query);
  }

  static private function getQuery_createTable_activities(string $dbname) : string {
    return 'CREATE TABLE `activities` (
    	`entry_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	`user_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL,
    	`entry_timestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    	`entry_type` TINYINT(3) UNSIGNED NOT NULL,
    	`entry_data` VARCHAR(1024) NULL DEFAULT NULL COLLATE \'utf8mb4_general_ci\',
    	`recipe_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    	`picture_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    	`rating_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    	`tag_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    	PRIMARY KEY (`entry_id`) USING BTREE,
    	INDEX `entry_timestamp` (`entry_timestamp`) USING BTREE,
    	INDEX `FK_activities_users` (`user_id`) USING BTREE,
    	INDEX `FK_activities_recipes` (`recipe_id`) USING BTREE,
    	INDEX `FK_activities_recipe_pictures` (`picture_id`) USING BTREE,
    	INDEX `FK_activities_recipe_ratings` (`rating_id`) USING BTREE,
    	INDEX `FK_activities_recipe_tags` (`tag_id`) USING BTREE,
    	CONSTRAINT `FK_activities_recipe_pictures` FOREIGN KEY (`picture_id`) REFERENCES `'.$dbname.'`.`recipe_pictures` (`picture_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    	CONSTRAINT `FK_activities_recipe_ratings` FOREIGN KEY (`rating_id`) REFERENCES `'.$dbname.'`.`recipe_ratings` (`entry_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    	CONSTRAINT `FK_activities_recipe_tags` FOREIGN KEY (`tag_id`) REFERENCES `'.$dbname.'`.`recipe_tags` (`entry_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    	CONSTRAINT `FK_activities_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `'.$dbname.'`.`recipes` (`recipe_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    	CONSTRAINT `FK_activities_users` FOREIGN KEY (`user_id`) REFERENCES `'.$dbname.'`.`users` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE=\'utf8mb4_general_ci\'
    ENGINE=InnoDB';
  }

  static private function getQuery_createView_allrecipes(string $dbuser) : string {
    return 'CREATE
      ALGORITHM=UNDEFINED
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
        count(`ra`.`entry_vote`) AS `recipe_votes`,
        count(`ra`.`entry_comment`) AS `recipe_comments`
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

  static private function getQuery_createView_allrecipetextdata(string $dbuser) : string {
    return 'CREATE
      ALGORITHM=UNDEFINED
      DEFINER=`'.$dbuser.'`@`localhost`
      SQL SECURITY INVOKER
      VIEW `allrecipetextdata` AS
        select
          `r`.`recipe_id` AS `recipe_id`,
          `r`.`recipe_name` AS `recipe_name`,
          `r`.`recipe_description` AS `recipe_description`,
          group_concat(`i`.`ingredient_description` separator \' \') AS `recipe_ingredients`,
          group_concat(`s`.`step_data` separator \' \') AS `recipe_steps`
        from ((
          `recipes` `r`
          join `recipe_ingredients` `i` on(`i`.`recipe_id` = `r`.`recipe_id`))
          join `recipe_steps` `s` on(`s`.`recipe_id` = `r`.`recipe_id`))
        where `r`.`recipe_public` = 1
        group by
          `r`.`recipe_id`,
          `r`.`recipe_name`,
          `r`.`recipe_description`';
  }

}
