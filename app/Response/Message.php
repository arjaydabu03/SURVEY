<?php
namespace App\Response;

class Message
{
    //STATUS CODES
    const CREATED_STATUS = 201;
    const UNPROCESS_STATUS = 422;
    const DATA_NOT_FOUND = 404;
    const SUCESS_STATUS = 200;
    const DENIED_STATUS = 403;
    const CUT_OFF_STATUS = 409;

    //USER OPERATIONS
    const USER_SAVE = "User successfully save.";
    const LOGIN_USER = "Sucessfully login.";
    const USER_UPDATE = "User successfully updated.";
    const USER_DISPLAY = "User display successfully.";
    //ROLE OPERATIONS
    const ROLE_SAVE = "Role successfully save.";
    const ROLE_UPDATE = "Role successfully updated.";
    const ROLE_DISPLAY = "Role display successfully.";

    const QUESTION_SAVE = "Question successfully save.";
    const QUESTION_UPDATE = "Question successfully updated.";
    const QUESTION_DISPLAY = "Question display successfully.";

    const ANSWER_SAVE = "Answer successfully save.";
    const ANSWER_UPDATE = "Answer successfully updated.";
    const ANSWER_DISPLAY = "Answer display successfully.";

    const SURVEY_SAVE = "Survey successfully save.";
    const SURVEY_UPDATE = "Survey successfully updated.";
    const SURVEY_DISPLAY = "Survey display successfully.";

    //GLOBAL MESSAGE
    const NOT_FOUND = "Data not Found";
    const INVALID_ACTION = "Invalid action.";
    const ARCHIVE_STATUS = "Successfully archived.";
    const RESTORE_STATUS = "Successfully restore.";
    const LOGOUT_USER = "Logout Successfully";
}
