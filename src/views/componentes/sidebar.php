<?php
    function active($current_page) : string {
        return $current_page == $_SERVER['REQUEST_URI'] ? 'active' : '';
    }
?>
<div class="d-flex flex-column flex-shrink-0 p-3 vh-100">
    <a href="/" class="d-flex align-items-center">
        <img src="/static/images/full-logo.svg" width="100%" alt="HomeWorkly">
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <?php require $root .'/views/' . $view['sidebar_links'] ?>
    </ul>
    <p class="text-center">HomeWorkly - <?= date("Y"); ?></p>
</div>