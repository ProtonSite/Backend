<?php

namespace ProtonSite;

class ProtonController {

    /**
     * Renders the requested view to the screen.
     * @param String $path The path to the view file.
     * @param array $data (Optional) Any required data to be passed to the view.
     */
    public function view(String $path, array $data = []) {
        if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/../private/views/' . $path . '.php' ) ) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/../private/views/' . $path . '.php';
        }
    }

}