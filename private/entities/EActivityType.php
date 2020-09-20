<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

final class EActivityType {

  const Undefined = 0;
  const RecipeCreated = 10;
  const RecipePublished = 11;
  const RecipeRemoved = 12;
  const PictureAdded = 20;
  const PictureRemoved = 21;
  const RatingAdded = 30;
  const RatingRemoved = 31;
  const TagAdded = 40;
  const TagRemoved= 41;
  const CommentAdded = 50;
  const CommentRemoved = 51;

}
