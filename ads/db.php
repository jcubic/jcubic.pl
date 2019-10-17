<?php


function query($db, $query, $data = null) {
    if ($data == null) {
        $res = $db->query($query);
    } else {
        $res = $db->prepare($query);
        if ($res) {
            if (!$res->execute($data)) {
                throw new Exception("execute query failed");
            }
        } else {
            throw new Exception("wrong query");
        }
    }
    if ($res) {
        if (preg_match("/^\s*UPDATE|DELETE|ALTER|CREATE|DROP/i", $query)) {
            return $res->rowCount();
        } else {
            return $res->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        throw new Exception("Coudn't open file");
    }
}

