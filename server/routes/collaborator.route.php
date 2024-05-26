<?php

require_once  __DIR__ . "/../controllers/collaborator/collaborator.controller.php";
require_once __DIR__ . "/../utils/request-is-empty.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_method = $_POST['_method'] ?? null;
    if ($_method) {
        if ($_method == "PUT" && isset($_GET['id'])) {
            $id = $_GET['id'];
            unset($_POST['_method']);
            CollaboratorController::update($id, $_POST);
        } elseif ($_method == "PATCH" && isset($_GET['id'])) {
            $id = $_GET['id'];
            unset($_POST['_method']);
            CollaboratorController::patch($id, $_POST);
        } else {
            unset($_POST['_method']);
            CollaboratorController::create($_POST);
        }
    } else {
        CollaboratorController::create($_POST);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		return CollaboratorController::selectById($id);
	} else {
		return CollaboratorController::selectAll();
	}
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		return CollaboratorController::delete($id);
	}
}
