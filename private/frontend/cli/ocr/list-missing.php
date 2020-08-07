<?php

class OcrListMissingCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('ocr:list-missing', 'Shows the number of files without OCR result and lists the maximum 10 first entries.');
  }

  public function execute()
  {
    global $db, $writer;

    $query = 'SELECT COUNT(*) AS `count`
      FROM `filesystem` `fs`
      JOIN `file_extensions` `fe` ON `fe`.`ext_key` LIKE `fs`.`fs_extension` AND `fe`.`ext_canocr`=1
      WHERE `fs`.`fs_isdir` = 0 AND `fs`.`fs_canocr` IN (-1, 1) AND (`fs`.`fs_pages` = 0 OR `fs`.`fs_pages` <> `fs`.`fs_ocr_pages`)';
    if ($result = $db->query($query)) {
      $result = $result->fetch_assoc();
      $items = intval($result['count']);
      $writer->warn->write('Database contains '.$items.' without OCR information.', true);
      $writer->write('The first 10 records:', true);

      $query = 'SELECT `fs`.*
        FROM `filesystem` `fs`
        JOIN `file_extensions` `fe` ON `fe`.`ext_key` LIKE `fs`.`fs_extension` AND `fe`.`ext_canocr`=1
        WHERE `fs`.`fs_isdir` = 0 AND `fs`.`fs_canocr` IN (-1, 1) AND (`fs`.`fs_pages` = 0 OR `fs`.`fs_pages` <> `fs`.`fs_ocr_pages`) LIMIT 10';
      if ($result = $db->query($query)) {
        $table = array();
        while($record = $result->fetch_assoc()) {
          $table[] = array(
            'Id' => $record['fs_id'],
            'Name' => $record['fs_name'],
            'Completed' => $record['fs_ocr_pages'].' / '.$record['fs_pages'],
          );
        }
        $writer->table($table);
        return 1;
      } else {
        $this->error('Database error '.$db->errno.': '.$db->error);
        return 104;
      }
    } else {
      $this->error('Database error '.$db->errno.': '.$db->error);
      return 104;
    }
  }
}

$app->add(new OcrListMissingCommand, 'olm');
