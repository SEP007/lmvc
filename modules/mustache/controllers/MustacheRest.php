<?php

class MustacheRest extends Controller {

    public static function render($template) {
        echo Mustache::render($template, self::request());
    }

}