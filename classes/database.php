<?php
class database{

    function opencon(): PDO{
        return new PDO (
        dsn: 'mysql:host=localhost;
        dbname=dbs_app',
        username: 'root',
        password: '');
    }

    

}