#!/bin/bash

cat \
  ./common-functions.js \
  ./vue-components.js \
  ./component-rc-button.js \
  ./component-rc-navbar.js \
  ./component-RecipesCreator.js \
  ./component-RecipeEditor.js \
  ./component-RecipeGallery.js \
  ./component-SearchRecipe.js \
  ./component-UserLogin.js \
  ./component-UserProfile.js \
  ./vue-router.js \
  ./vue.js > ../../../public/script/vue-custom-1.2.8.js
