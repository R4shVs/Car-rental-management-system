<?php

if (!in_array($auth->getRole(), $role)){
    exit("Error 403");
}