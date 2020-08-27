<?php

use Surcouf\Cookbook\Helper\Formatter;

class OcrPurgeImagesCommand extends Ahc\Cli\Input\Command
{
  public function __construct()
  {
    parent::__construct('ocr:purge-images', 'Removes old files from previous OCR readings.');
    $this
      ->option('-f --force', 'Delete without prompting.', 'boolval', false);
  }

  public function execute($force)
  {
    global $Config, $interactor, $writer;
    $fi = new FilesystemIterator(DIR_OCRCACHE, FilesystemIterator::SKIP_DOTS);
    $mindate = (new DateTime())->sub($Config->OcrMaintenance->PurgeFiles->getTimespan());
    $writer->warn('Cache folder contains '.Formatter::t(iterator_count($fi), 'file', 'files', Formatter::FOValuePrefix).'.', true);
    $icount = 0;
    $ifailed = 0;

    if (iterator_count($fi) > 0) {
      if ($force || $interactor->confirm('Should all files older than '.$mindate->format(DTF_SQL).' be deleted??', 'n')) {
        foreach ($fi as $fileInfo) {
          $dif = $mindate->diff((new DateTime())->setTimestamp($fileInfo->getCTime()));
          if ($fileInfo->isFile() && $dif->invert == 1) {
            $path = $fileInfo->getRealPath();
            if (unlink($path)) {
              $icount++;
              if ($this->values()['verbosity'] > 0)
                $writer->comment('  '.$path.' deleted', true);
            } else {
              $ifailed++;
              $writer->error('  '.$path.' delete failed', true);
            }
          }
        }
      }
      $writer->write(Formatter::t($icount, 'file', 'files', Formatter::FOValuePrefix).' deleted.', true);
      if ($ifailed > 0) {
        $writer->error(Formatter::t($ifailed, 'file', 'files', Formatter::FOValuePrefix).' could not be deleted.', true);
      }
    }
    return 1;

  }
}

$app->add(new OcrPurgeImagesCommand, 'opi');
