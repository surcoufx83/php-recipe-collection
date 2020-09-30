<?php

namespace Surcouf\Cookbook\Helper\UiHelper;

if (!defined('CORE2'))
  exit;

final class GalleryItem implements GalleryItemInterface {

  private $imgSource, $imgTitle;
  private $bodyTitle, $bodyText;
  private $footerNote, $footerAction;
  private $quote, $quoteIcon;

  public function getAction() : ?array {
    return $this->footerAction;
  }

  public function getImageTitle() : ?string {
    return $this->imgTitle;
  }

  public function getImageUrl() : ?string {
    return $this->imgSource;
  }

  public function getNote() : ?string {
    return $this->footerNote;
  }

  public function getQuote() : ?string {
    return $this->quote;
  }

  public function getQuoteIcon() : ?string {
    return $this->quoteIcon;
  }

  public function getText() : ?string {
    return $this->bodyText;
  }

  public function getTitle() : ?string {
    return $this->bodyTitle;
  }

  public function setBody(string $title, string $text) : GalleryItemInterface {
    $this->bodyTitle = $title;
    $this->bodyText = $text;
    return $this;
  }

  public function setFooterAction(string $title, ?string $url=null, ?string $id=null, ?string $cssClasses=null) : GalleryItemInterface {
    $this->footerAction = [
      'CssClasses' => $cssClasses,
      'Id' => $id,
      'Title' => $title,
      'Url' => $url,
    ];
    return $this;
  }

  public function setFooterNote(string $title) : GalleryItemInterface {
    $this->footerNote = $title;
    return $this;
  }

  public function setImage(string $url, string $title='') : GalleryItemInterface {
    $this->imgSource = $url;
    $this->imgTitle = $title;
    return $this;
  }

  public function setQuote(string $text) : GalleryItemInterface {
    $this->quote = $text;
    return $this;
  }

  public function setQuoteIcon(string $icon) : GalleryItemInterface {
    $this->quoteIcon = $icon;
    return $this;
  }

  public function showFooter() : bool {
    return !is_null($this->footerAction);
  }

  public function showImage() : bool {
    return !is_null($this->imgSource);
  }

}
