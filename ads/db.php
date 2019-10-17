<?php
/* common PDO query function
 *
 * Copyright (C) 2019 Jakub T. Jankiewicz
 * released under Creative Commons Attribution Share-Alike license (CC-BY-SA)
 *
 */


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

