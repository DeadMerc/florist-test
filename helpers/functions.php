<?php
//helper for validator
function not_empty($value)
{
    if (!empty($value)) {
        return true;
    }
    return false;
}